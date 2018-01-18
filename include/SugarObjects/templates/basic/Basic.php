<?php

/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2017 SalesAgility Ltd.
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
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
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
 * reasonably feasible for  technical reasons, the Appropriate Legal Notices must
 * display the words  "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

class Basic extends SugarBean
{
    protected static $doNotDisplayOptInTickForModule = array(
        'Users',
        'Employees'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @deprecated deprecated since version 7.6, PHP4 Style Constructors are deprecated and will be removed in 8.0,
     *     please update your code, use __construct instead
     */
    public function Basic()
    {
        $deprecatedMessage =
            'PHP4 Style Constructors are deprecated and will be remove in 8.0, please update your code';
        if (isset($GLOBALS['log'])) {
            $GLOBALS['log']->deprecated($deprecatedMessage);
        } else {
            trigger_error($deprecatedMessage, E_USER_DEPRECATED);
        }
        self::__construct();
    }

    /**
     * @see SugarBean::get_summary_text()
     */
    public function get_summary_text()
    {
        return "$this->name";
    }

    /**
     * edit view should show confirm opt in (only if enabled)
     *
     * @param string $emailField
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getConfirmOptInTickFromSugarEmailAddressField($emailField)
    {
        $this->validateSugarEmailAddressField($emailField);

        return $this->displayOptInFromSugarEmailAddressField($emailField);
    }


    /**
     * @param string $emailField
     * @return string
     */
    public function getOptInStatusFromSugarField($emailField) {
        $emailAddress = $this->fromSugarEmailAddressField($emailField);


        if ($emailAddress !== null && !in_array($this->module_name, self::$doNotDisplayOptInTickForModule, true)) {


            if ($emailAddress->invalid_email == '1') {
                return 'INVALID';
            }

            if ($emailAddress->opt_out == '1') {
                return 'OPT_OUT';
            }

            if (
                $emailAddress->confirm_opt_in == 'confirmed-opt-in'
            ) {
                return 'OPT_IN_PENDING_EMAIL_CONFIRMED';
            } elseif (
                $emailAddress->confirm_opt_in == 'opt-in'
                && !empty($emailAddress->confirm_opt_in_sent_date)
            ) {
                return 'OPT_IN_PENDING_EMAIL_SENT';
            } elseif (
                empty($emailAddress->confirm_opt_in_sent_date)
                && $emailAddress->confirm_opt_in !== ''
            ) {
                return 'OPT_IN_PENDING_EMAIL_NOT_SENT';
            }
        }

        // Otherwise
        return 'UNKNOWN_OPT_IN_STATUS';
    }

    /**
     *
     * @global array $sugar_config
     * @global \LoggerManager $log
     * @param string $emailField
     * @return \EmailAddress
     * @throws InvalidArgumentException
     */
    public function fromSugarEmailAddressField($emailField)
    {
        $this->validateSugarEmailAddressField($emailField);

        global $sugar_config;

        /** @var EmailAddress $emailAddressBean */
        $emailAddressBean = BeanFactory::getBean('EmailAddresses');

        if (!$sugar_config['email_enable_confirm_opt_in']) {
            global $log;
            $log->warn('Confirm Opt In is not enabled.');
            $emailAddressBean->confirm_opt_in = 'confirmed-opt-in';
            return $emailAddressBean;
        }

        $emailAddressId = $this->getIdFromSugarEmailAddressField($emailField);
        return $emailAddressBean->retrieve($emailAddressId);
    }

    /**
     *
     * @global \LoggerManager $log
     * @param string $emailField
     * @return string|null EmailAddress ID or null on error
     */
    protected function getIdFromSugarEmailAddressField($emailField)
    {
        global $log;

        $this->validateSugarEmailAddressField($emailField);
        $emailAddress = $this->cleanUpEmailAddress($this->{$emailField});

        if (!$emailAddress) {

            $log->warn('Trying to get an empty email address.');
            return null;
        }

        // List view requires us to retrieve the mail so we can see the email addresses
        $this->retrieve();

        $found = false;
        $addresses = $this->emailAddress->addresses;
        foreach ($addresses as $address) {
            if ($this->cleanUpEmailAddress($address['email_address']) === $emailAddress) {
                $found = true;
                $emailAddressId = $address['email_address_id'];
                break;
            }
        }

        if (!$found) {
            // Changed exception to error as demo data is never selected.
            $log->fatal('A Basic bean has not selected email address. (' . $emailAddress . ')');
            return null;
        }

        return $emailAddressId;
    }

    /**
     * @param string $emailField
     * @return string
     */
    protected function displayOptInFromSugarEmailAddressField($emailField)
    {
        global $sugar_config;
        global $app_list_strings;
        global $app_strings;
        global $mod_strings;

        $tickHtml = '';

        if (isset($sugar_config['email_enable_confirm_opt_in']) && $sugar_config['email_enable_confirm_opt_in']  !== '') {
            $template = new Sugar_Smarty();
            $template->assign('APP', $app_strings);
            $template->assign('APP_LIST_STRINGS', $app_list_strings);
            $template->assign('MOD', $mod_strings);
            $template->assign('OPT_IN_STATUS', $this->getOptInStatusFromSugarField($emailField));
            $tickHtml = $template->fetch('include/SugarObjects/templates/basic/tpls/displayEmailAddressOptInField.tpl');
        }

        return $tickHtml;
    }

    /**
     *
     * @param string $emailField
     * @throws InvalidArgumentException
     */
    protected function validateSugarEmailAddressField($emailField)
    {
        if (!is_string($emailField)) {
            throw new InvalidArgumentException('Invalid type. $emailField must be a string value, eg. email1');
        }

        if (!preg_match('/^email\d+/', $emailField)) {
            throw new InvalidArgumentException(
                '$emailField is invalid, "' . $emailField . '" given. Expected valid name eg. email1'
            );
        }
    }

    /**
     *
     * @param string $emailAddress
     * @return string
     */
    private function cleanUpEmailAddress($emailAddress)
    {
        $ret = $emailAddress;
        $ret = trim($ret);
        $ret = strtolower($ret);

        return $ret;
    }
}
