<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
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

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $current_user, $sugar_config;
global $mod_strings;
global $app_list_strings;
global $app_strings;
global $theme;

if (!is_admin($current_user)) {
    sugar_die("Unauthorized access to administration.");
}

require_once('modules/Configurator/Configurator.php');

echo getClassicModuleTitle(
    "Administration",
    array(
        "<a href='index.php?module=Administration&action=index'>" . translate('LBL_MODULE_NAME', 'Administration') . "</a>",
        $mod_strings['LBL_GOOGLE_AUTH_TITLE'],
    ),
    false
);

$cfg          = new Configurator();
$sugar_smarty = new Sugar_Smarty();
$errors       = array();

if (!array_key_exists('google_auth_json', $cfg->config)) {
    $cfg->config['google_auth_json'] = false;
}

if (isset($_REQUEST['do']) && $_REQUEST['do'] == 'save') {
    $cfg->config['google_auth_json'] = !empty($_REQUEST['google_auth_json']);
    $cfg->saveConfig();
    header('Location: index.php?module=Administration&action=index');
    exit();
}

$sugar_smarty->assign('LANGUAGES', get_languages());
$sugar_smarty->assign("JAVASCRIPT", get_set_focus_js());
$sugar_smarty->assign('config', $cfg->config['google_auth_json']);
$sugar_smarty->assign('error', $errors);

// Check for Google Sync JSON
$json = base64_decode($cfg->config['google_auth_json']);

$gcConfig = json_decode($json, true);

if ($gcConfig) {
    $sugar_smarty->assign("GOOGLE_JSON_CONF", 'CONFIGURED');
    $sugar_smarty->assign("GOOGLE_JSON_CONF_COLOR", 'green');
} else {
    $sugar_smarty->assign("GOOGLE_JSON_CONF", 'UNCONFIGURED');
    $sugar_smarty->assign("GOOGLE_JSON_CONF_COLOR", 'black');
}

$sugar_smarty->display('modules/Administration/GoogleCalendarAuth.tpl');

$javascript = new javascript();
$javascript->setFormName('ConfigureSettings');
echo $javascript->getScript();
