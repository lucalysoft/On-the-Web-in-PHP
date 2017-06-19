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

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

include_once 'include/Exceptions/SugarControllerException.php';

class EmailsController extends SugarController
{
    /**
     * @var Email $bean ;
     */
    public $bean;

    /**
     * @see EmailsController::composeBean()
     */
    const COMPOSE_BEAN_MODE_UNDEFINED = 0;

    /**
     * @see EmailsController::composeBean()
     */
    const COMPOSE_BEAN_MODE_REPLY_TO = 1;

    /**
     * @see EmailsController::composeBean()
     */
    const COMPOSE_BEAN_MODE_REPLY_TO_ALL = 2;

    /**
     * @see EmailsController::composeBean()
     */
    const COMPOSE_BEAN_MODE_FORWARD = 3;

    protected static $doNotImportFields = array(
        'action',
        'type',
        'send',
        'record',
        'from_addr_name',
        'reply_to_addr',
        'to_addrs_names',
        'cc_addrs_names',
        'bcc_addrs_names',
        'imap_keywords',
        'raw_source',
        'description',
        'description_html',
        'date_sent',
        'message_id',
        'name',
        'status',
        'reply_to_status',
        'mailbox_id',
        'created_by_link',
        'modified_user_link',
        'assigned_user_link',
        'assigned_user_link',
        'uid',
        'msgno',
        'folder',
        'folder_type',
        'inbound_email_record',
        'is_imported',
        'has_attachment',
        'id',
    );

    /**
     * @see EmailsViewList
     */
    public function action_index()
    {
        $this->view = 'list';
    }

    /**
     * @see EmailsViewDetaildraft
     */
    public function action_DetailDraftView()
    {
        $this->view = 'detaildraft';
    }

    /**
     * @see EmailsViewCompose
     */
    public function action_ComposeView()
    {
        $this->view = 'compose';
    }

    /**
     * @see EmailsViewSendemail
     */
    public function action_send()
    {
        $this->bean = $this->bean->populateBeanFromRequest($this->bean, $_REQUEST);
        $this->bean->save();

        $this->bean->handleMultipleFileAttachments();

        if ($this->bean->send()) {
            $this->bean->status = 'sent';
            $this->bean->save();
        } else {
            $this->bean->status = 'sent_error';
        }

        $this->view = 'sendemail';
    }


    /**
     * @see EmailsViewCompose
     */
    public function action_SaveDraft()
    {
        $this->bean = $this->bean->populateBeanFromRequest($this->bean, $_REQUEST);
        $this->bean->mailbox_id = $_REQUEST['inbound_email_id'];
        $this->bean->status = 'draft';
        $this->bean->save();
        $this->bean->handleMultipleFileAttachments();
        $this->view = 'savedraftemail';
    }

    /**
     * @see EmailsViewPopup
     */
    public function action_Popup()
    {
        $this->view = 'popup';
    }

    /**
     * Gets the values of the "from" field
     * includes the signatures for each account
     */
    public function action_GetFromFields()
    {
        global $current_user;
        $email = new Email();
        $email->email2init();
        $ie = new InboundEmail();
        $ie->email = $email;
        $accounts = $ieAccountsFull = $ie->retrieveAllByGroupIdWithGroupAccounts($current_user->id);
        $accountSignatures = $current_user->getPreference('account_signatures', 'Emails');
        if($accountSignatures != null) {
            $emailSignatures = unserialize(base64_decode($accountSignatures));
            $defaultEmailSignature = $current_user->getPreference('signature_default');
        } else {
            $defaultEmailSignature = null;
            $GLOBALS['log']->warn('User does not have a signature');
        }

        $prependSignature = $current_user->getPreference('signature_prepend');

        $data = array();
        foreach ($accounts as $inboundEmailId => $inboundEmail) {
            $storedOptions = unserialize(base64_decode($inboundEmail->stored_options));
            $isGroupEmailAccount = $inboundEmail->isGroupEmailAccount();
            $isPersonalEmailAccount = $inboundEmail->isPersonalEmailAccount();

            $dataAddress = array(
                'type' => $inboundEmail->module_name,
                'id' => $inboundEmail->id,
                'attributes' => array(
                    'from' => $storedOptions['from_addr']
                ),
                'prepend' => $prependSignature,
                'isPersonalEmailAccount' => $isPersonalEmailAccount,
                'isGroupEmailAccount' => $isGroupEmailAccount
            );

            // Include signature
            if (isset($emailSignatures[$inboundEmail->id])) {
                $emailSignatureId = $emailSignatures[$inboundEmail->id];
            } else {
                $emailSignatureId = $defaultEmailSignature;
            }

            $signature = $current_user->getSignature($emailSignatureId);
            if(!$signature) {
                $GLOBALS['log']->warn('User does not have a signature, empty string will used instead');
                $signature['signature_html'] = '';
                $signature['signature'] = '';
            }
            $dataAddress['emailSignatures'] = array(
                'html' => utf8_encode(html_entity_decode($signature['signature_html'])),
                'plain' => $signature['signature'],
            );

            
            $data[] = $dataAddress;
        }

        $dataEncoded = json_encode(array('data' => $data));
        echo $dataEncoded;

        $this->view = 'ajax';
    }

    public function action_CheckEmail()
    {
        $inboundEmail = new InboundEmail();
        $inboundEmail->syncEmail();

        echo json_encode(array('response' => array()));
        $this->view = 'ajax';
    }

    /**
     * Used to list folders in the list view
     */
    public function action_GetFolders()
    {
        require_once 'include/SugarFolders/SugarFolders.php';
        global $current_user, $mod_strings;
        $email = new Email();
        $email->email2init();
        $ie = new InboundEmail();
        $ie->email = $email;
        $GLOBALS['log']->debug('********** EMAIL 2.0 - Asynchronous - at: refreshSugarFolders');
        $rootNode = new ExtNode('', '');
        $folderOpenState = $current_user->getPreference('folderOpenState', 'Emails');
        $folderOpenState = empty($folderOpenState) ? '' : $folderOpenState;

        try {
            $ret = $email->et->folder->getUserFolders($rootNode, sugar_unserialize($folderOpenState), $current_user,
                true);
            $out = json_encode(array('response' => $ret));
        } catch (SugarFolderEmptyException $e) {
            $GLOBALS['log']->warn($e->getMessage());
            $out = json_encode(array('errors' => array($mod_strings['LBL_ERROR_NO_FOLDERS'])));
        }

        echo $out;
        $this->view = 'ajax';
    }


    /**
     * @see EmailsViewDetailnonimported
     */
    public function action_DisplayDetailView()
    {
        global $db;
        $emails = BeanFactory::getBean("Emails");
        $result = $emails->get_full_list('', "uid = '{$db->quote($_REQUEST['uid'])}'");
        if (empty($result)) {
            $this->view = 'detailnonimported';
        } else {
            header('location:index.php?module=Emails&action=DetailView&record=' . $result[0]->id);
        }
    }

    /**
     * @see EmailsViewDetailnonimported
     */
    public function action_ImportAndShowDetailView()
    {
        global $db;
        if (isset($_REQUEST['inbound_email_record']) && !empty($_REQUEST['inbound_email_record'])) {
            $inboundEmail = new InboundEmail();
            $inboundEmail->retrieve($db->quote($_REQUEST['inbound_email_record']), true, true);
            $inboundEmail->connectMailserver();
            $importedEmailId = $inboundEmail->returnImportedEmail($_REQUEST['msgno'], $_REQUEST['uid']);


            // Set the fields which have been posted in the request
            $this->bean = $this->setAfterImport($importedEmailId, $_REQUEST);

            if ($importedEmailId !== false) {
                header('location:index.php?module=Emails&action=DetailView&record=' . $importedEmailId);
            }
        } else {
            // When something fail redirect user to index
            header('location:index.php?module=Emails&action=index');
        }

    }

    /**
     * @see EmailsViewImport
     */
    public function action_ImportView()
    {
        $this->view = 'import';

    }

    public function action_GetCurrentUserID()
    {
        global $current_user;
        echo json_encode(array("response" => $current_user->id));
        $this->view = 'ajax';
    }

    public function action_ImportFromListView()
    {
        global $db;

        if (isset($_REQUEST['inbound_email_record']) && !empty($_REQUEST['inbound_email_record'])) {
            $inboundEmail = BeanFactory::getBean('InboundEmail', $db->quote($_REQUEST['inbound_email_record']));
            if (isset($_REQUEST['folder']) && !empty($_REQUEST['folder'])) {
                $inboundEmail->mailbox = $_REQUEST['folder'];
            }
            $inboundEmail->connectMailserver();

            if (isset($_REQUEST['all']) && $_REQUEST['all'] === 'true') {
                // import all in folder
                $importedEmailsId = $inboundEmail->importAllFromFolder();
                foreach ($importedEmailsId as $importedEmailId) {
                    $this->bean = $this->setAfterImport($importedEmailId, $_REQUEST);
                }
            } else {
                foreach ($_REQUEST['uid'] as $uid) {
                    $importedEmailId = $inboundEmail->returnImportedEmail($_REQUEST['msgno'], $uid);
                    $this->bean = $this->setAfterImport($importedEmailId, $_REQUEST);
                }
            }

        } else {
            $GLOBALS['log']->fatal('EmailsController::action_ImportFromListView() missing inbound_email_record');
        }

        header('location:index.php?module=Emails&action=index');
    }

    public function action_ReplyTo()
    {
        global $current_user;
        $this->composeBean($_REQUEST, self::COMPOSE_BEAN_MODE_REPLY_TO);
        $this->view = 'compose';
    }

    public function action_ReplyToAll()
    {
        global $current_user;
        $this->composeBean($_REQUEST, self::COMPOSE_BEAN_MODE_REPLY_TO_ALL);
        $this->view = 'compose';
    }

    public function action_Forward()
    {
        global $current_user;
        $this->composeBean($_REQUEST, self::COMPOSE_BEAN_MODE_FORWARD);
        $this->view = 'compose';
    }

    public function action_SendDraft()
    {
        $this->view = 'ajax';
        echo json_encode(array());
    }


    /**
     * @throws SugarControllerException
     */
    public function action_MarkEmails()
    {
        $this->markEmails($_REQUEST);
        echo json_encode(array('response' => true));
        $this->view = 'ajax';
    }

    /**
     * @param array $request
     * @throws SugarControllerException
     */
    public function markEmails($request)
    {
        // validate the request

        if (!isset($request['inbound_email_record']) || !$request['inbound_email_record']) {
            throw new SugarControllerException('No Inbound Email record in request');
        }

        if (!isset($request['folder']) || !$request['folder']) {
            throw new SugarControllerException('No Inbound Email folder in request');
        }

        // connect to requested inbound email server
        // and select the folder

        $ie = $this->getInboundEmail($request['inbound_email_record']);
        $ie->mailbox = $request['folder'];
        $ie->connectMailserver();

        // get requested UIDs and flag type

        $UIDs = $this->getRequestedUIDs($request);
        $type = $this->getRequestedFlagType($request);

        // mark emails
        $ie->markEmails($UIDs, $type);
    }

    /**
     * @param array $request
     * @param int $mode
     * @throws InvalidArgumentException
     * @see EmailsController::COMPOSE_BEAN_MODE_UNDEFINED
     * @see EmailsController::COMPOSE_BEAN_MODE_REPLY_TO
     * @see EmailsController::COMPOSE_BEAN_MODE_REPLY_TO_ALL
     * @see EmailsController::COMPOSE_BEAN_MODE_FORWARD
     */
    public function composeBean($request, $mode = self::COMPOSE_BEAN_MODE_UNDEFINED)
    {

        if ($mode === self::COMPOSE_BEAN_MODE_UNDEFINED) {
            throw new InvalidArgumentException('EmailController::composeBean $mode argument is COMPOSE_BEAN_MODE_UNDEFINED');
        }

        global $db;
        global $mod_strings;

        if (isset($request['record']) && !empty($request['record'])) {
            $this->bean->retrieve($request['record']);
        } else {
            $inboundEmail = BeanFactory::getBean('InboundEmail', $db->quote($request['inbound_email_record']));
            $inboundEmail->connectMailserver();
            $importedEmailId = $inboundEmail->returnImportedEmail($request['msgno'], $request['uid']);
            $this->bean->retrieve($importedEmailId);
        }

        $_REQUEST['return_module'] = 'Emails';
        $_REQUEST['return_Action'] = 'index';

        if ($mode === self::COMPOSE_BEAN_MODE_REPLY_TO || $mode === self::COMPOSE_BEAN_MODE_REPLY_TO_ALL) {
            // Move email addresses from the "from" field to the "to" field
            $this->bean->to_addrs = $this->bean->from_addr;
            $this->bean->to_addrs_names = $this->bean->from_addr_name;
        } else {
            if ($mode === self::COMPOSE_BEAN_MODE_FORWARD) {
                $this->bean->to_addrs = '';
                $this->bean->to_addrs_names = '';
            }
        }

        if ($mode !== self::COMPOSE_BEAN_MODE_REPLY_TO_ALL) {
            $this->bean->cc_addrs_arr = array();
            $this->bean->cc_addrs_names = '';
            $this->bean->cc_addrs = '';
            $this->bean->cc_addrs_ids = '';
            $this->bean->cc_addrs_emails = '';
        }

        if ($mode === self::COMPOSE_BEAN_MODE_REPLY_TO || $mode === self::COMPOSE_BEAN_MODE_REPLY_TO_ALL) {
            // Add Re to subject
            $this->bean->name = $mod_strings['LBL_RE'] . $this->bean->name;
        } else {
            if ($mode === self::COMPOSE_BEAN_MODE_FORWARD) {
                // Add FW to subject
                $this->bean->name = $mod_strings['LBL_FW'] . $this->bean->name;
            } else {
                $this->bean->name = $mod_strings['LBL_NO_SUBJECT'] . $this->bean->name;
            }
        }

        // Move body into original message
        if (!empty($this->bean->description_html)) {
            $this->bean->description = '<br>' . $mod_strings['LBL_ORIGINAL_MESSAGE_SEPERATOR'] . '<br>' .
                $this->bean->description_html;
        } else {
            if (!empty($this->bean->description)) {
                $this->bean->description = PHP_EOL . $mod_strings['LBL_ORIGINAL_MESSAGE_SEPERATOR'] . PHP_EOL .
                    $this->bean->description;
            }
        }

    }


    /**
     * @param $request
     * @return null|string
     */
    private function getRequestedUIDs($request)
    {
        $ret = $this->getRequestedArgument($request, 'uid');
        if (is_array($ret)) {
            $ret = implode(',', $ret);
        }

        return $ret;
    }

    /**
     * @param array $request
     * @return null|mixed
     */
    private function getRequestedFlagType($request)
    {
        $ret = $this->getRequestedArgument($request, 'type');

        return $ret;
    }

    /**
     * @param array $request
     * @param string $key
     * @return null|mixed
     */
    private function getRequestedArgument($request, $key)
    {
        if (!isset($request[$key])) {
            $GLOBALS['log']->error("Requested key is not set: ");

            return null;
        }

        return $request[$key];
    }

    /**
     * return an Inbound Email by requested record
     *
     * @param string $record
     * @return InboundEmail
     * @throws SugarControllerException
     */
    private function getInboundEmail($record)
    {
        $db = DBManagerFactory::getInstance();
        $ie = BeanFactory::getBean('InboundEmail', $db->quote($record));
        if (!$ie) {
            throw new SugarControllerException("BeanFactory can't resolve an InboundEmail record: $record");
        }

        return $ie;
    }

    /**
     * @param array $request
     * @return bool|Email
     * @see Email::id
     * @see EmailsController::action_ImportAndShowDetailView()
     * @see EmailsController::action_ImportView()
     */
    protected function setAfterImport($importedEmailId, $request)
    {
        $emails = BeanFactory::getBean("Emails", $importedEmailId);
        foreach ($request as $requestKey => $requestValue) {
            if (strpos($requestKey, 'SET_AFTER_IMPORT_') !== false) {
                $field = str_replace('SET_AFTER_IMPORT_', '', $requestKey);
                if (in_array($field, self::$doNotImportFields)) {
                    continue;
                }

                $emails->{$field} = $requestValue;
            }
        }

        $emails->save();

        return $emails;
    }
}