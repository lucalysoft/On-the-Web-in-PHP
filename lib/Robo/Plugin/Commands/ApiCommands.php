<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2019 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

namespace SuiteCRM\Robo\Plugin\Commands;

use Api\Core\Config\ApiConfig;
use DateTime;
use DBManager;
use OAuth2Clients;
use Robo\Task\Base\loadTasks;
use Robo\Tasks;
use SuiteCRM\Robo\Traits\RoboTrait;
use SuiteCRM\Robo\Traits\CliRunnerTrait;
use Api\V8\BeanDecorator\BeanManager;
use User;
use DBManagerFactory;

class ApiCommands extends Tasks
{
    use loadTasks;
    use RoboTrait;
    use CliRunnerTrait;

    /**
     * @var DBManager
     */
    protected $db;

    /**
     * @var BeanManager
     */
    protected $beanManager;

    /**
     * @var array
     */
    protected static $beanAliases = [
        User::class => 'Users',
    ];

    /**
     * Configure environment
     */
    public function configureV8Api()
    {
        $this->say('Configure V8 Api');

        $this->bootstrap();
        $this->db = DBManagerFactory::getInstance();
        $this->beanManager = new BeanManager($this->db, static::$beanAliases);

        $this->taskComposerInstall()->noDev()->noInteraction()->run();
        $this->generateKeys();
        $this->setKeyPermissions();
        $this->updateEncryptionKey();
        $this->rebuildHtaccessFile();
        $this->createClient();
    }

    /**
     * Generate OAuth2 public/private keys.
     */
    private function generateKeys()
    {
        $privateKey = openssl_pkey_new(
            [
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ]
        );

        openssl_pkey_export($privateKey, $privateKeyExport);

        $publicKey = openssl_pkey_get_details($privateKey);

        $publicKeyExport = $publicKey['key'];

        file_put_contents(
            ApiConfig::OAUTH2_PRIVATE_KEY,
            $privateKeyExport
        );

        file_put_contents(
            ApiConfig::OAUTH2_PUBLIC_KEY,
            $publicKeyExport
        );
    }

    /**
     * Sets the Oauth2 key permissions.
     */
    private function setKeyPermissions()
    {
        chmod(
            ApiConfig::OAUTH2_PRIVATE_KEY,
            0600
        ) &&
        chmod(
            ApiConfig::OAUTH2_PUBLIC_KEY,
            0600
        );
    }

    /**
     * Update OAuth2 encryption keys.
     * @throws \Exception
     */
    private function updateEncryptionKey()
    {
        $oldKey = ApiConfig::OAUTH2_ENCRYPTION_KEY;
        $key = base64_encode(random_bytes(32));
        $apiConfig = file_get_contents('Api/Core/Config/ApiConfig.php');

        $configFileContents = str_replace(
            $oldKey,
            $key,
            $apiConfig
        );

        file_put_contents(
            'Api/Core/Config/ApiConfig.php', $configFileContents, LOCK_EX
        );
    }

    /**
     * Rebuild .Htaccess file.
     */
    private function rebuildHtaccessFile()
    {
        @require_once __DIR__ . '/../../../../modules/Administration/UpgradeAccess.php';
    }

    /**
     * Creates OAuth2 client.
     * @return array
     * @throws \Exception
     */
    private function createClient()
    {
        $query = <<<SQL
SELECT
    count(`id`) AS `count`
FROM
    `oauth2clients`
WHERE
    `name` LIKE 'V8 API Client %'
SQL;

        $result = $this->db->fetchOne($query);

        $count = $result
            ? (int)$result['count']
            : 0;

        $count++;

        $dateTime = new DateTime();

        $clientSecret = base_convert(
            $dateTime->getTimestamp() * 4096,
            10,
            16
        );

        $clientBean = $this->beanManager->newBeanSafe(
            OAuth2Clients::class
        );

        $clientBean->name = 'V8 API Client ' . $count;
        $clientBean->secret = hash('sha256', $clientSecret);
        $clientBean->{'is_confidential'} = true;
        $clientBean->save();
        $clientBean->retrieve($clientBean->id);

        return !empty($clientBean->fetched_row['id']) ? compact('clientBean', 'clientSecret') : [];
    }
}
