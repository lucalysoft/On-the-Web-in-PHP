<?php
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.

 * SuiteCRM is an extension to SugarCRM Community Edition developed by Salesagility Ltd.
 * Copyright (C) 2011 - 2014 Salesagility Ltd.
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
 ********************************************************************************/

$dictionary['AOR_Scheduled_Reports'] = array(
	'table'=>'aor_scheduled_reports',
	'audited'=>true,
		'duplicate_merge'=>true,
		'fields'=>array (
            'email1' =>
                array (
                    'name' => 'email1',
                    'vname' => 'LBL_EMAIL',
                    'group' => 'email1',
                    'type' => 'varchar',
                    'function' =>
                        array (
                            'name' => 'getEmailAddressWidget',
                            'returns' => 'html',
                        ),
                    'source' => 'non-db',
                    'studio' =>
                        array (
                            'editField' => true,
                            'searchview' => false,
                        ),
                    'full_text_search' =>
                        array (
                            'boost' => 3,
                            'analyzer' => 'whitespace',
                        ),
                ),
            'email_addresses_primary' =>
                array (
                    'name' => 'email_addresses_primary',
                    'type' => 'link',
                    'relationship' => 'aor_scheduled_reports_email_addresses_primary',
                    'source' => 'non-db',
                    'vname' => 'LBL_EMAIL_ADDRESS_PRIMARY',
                    'duplicate_merge' => 'disabled',
                ),
            'email_addresses' =>
                array (
                    'name' => 'email_addresses',
                    'type' => 'link',
                    'relationship' => 'aor_scheduled_reports_email_addresses',
                    'source' => 'non-db',
                    'vname' => 'LBL_EMAIL_ADDRESSES',
                    'reportable' => false,
                    'unified_search' => true,
                    'rel_fields' =>
                        array (
                            'primary_address' =>
                                array (
                                    'type' => 'bool',
                                ),
                        ),
                ),
            'email_addresses_non_primary' =>
                array (
                    'name' => 'email_addresses_non_primary',
                    'type' => 'email',
                    'source' => 'non-db',
                    'vname' => 'LBL_EMAIL_NON_PRIMARY',
                    'studio' => false,
                    'reportable' => false,
                    'massupdate' => false,
                ),


            'schedule' =>
  array (
    'required' => true,
    'name' => 'schedule',
    'vname' => 'LBL_SCHEDULE',
    'type' => 'CronSchedule',
    'dbType' => 'varchar',
    'massupdate' => 0,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => '0',
    'audited' => false,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'len' => '100',
    'size' => '20',
  ),
            'last_run' => array (
                'name' => 'last_run',
                'vname' => 'LBL_LAST_RUN',
                'dbtype' => 'datetime',
                'type' => 'readonly',
                'required' => false,
                'reportable' => false,
            ),
            'status' => array (
                'name' => 'status',
                'vname' => 'LBL_STATUS',
                'type' => 'enum',
                'options' => 'aor_scheduled_reports_status_dom',
                'len' => 100,
                'editable' => false,
                'required' => false,
                'reportable' => false,
                'importable' => 'required',
            ),

    "aor_scheduled_reports_aor_reports" => array (
                'name' => 'aor_scheduled_reports_aor_reports',
                'type' => 'link',
                'relationship' => 'aor_scheduled_reports_aor_reports',
                'source' => 'non-db',
                'module' => 'AOR_Reports',
                'bean_name' => 'AOR_Report',
                'vname' => 'LBL_AOR_SCHEDULED_REPORTS_AOR_REPORTS_FROM_AOR_REPORTS_TITLE',
                'id_name' => 'aor_scheduled_reports_aor_reportsaor_reports_ida',
            ),
    "aor_scheduled_reports_aor_reports_name" => array (
    'name' => 'aor_scheduled_reports_aor_reports_name',
    'type' => 'relate',
    'source' => 'non-db',
    'vname' => 'LBL_AOR_SCHEDULED_REPORTS_AOR_REPORTS_FROM_AOR_REPORTS_TITLE',
    'save' => true,
    'id_name' => 'aor_scheduled_reports_aor_reportsaor_reports_ida',
    'link' => 'aor_scheduled_reports_aor_reports',
    'table' => 'aor_reports',
    'module' => 'AOR_Reports',
    'rname' => 'name',
),
"aor_scheduled_reports_aor_reportsaor_reports_ida" => array (
    'name' => 'aor_scheduled_reports_aor_reportsaor_reports_ida',
    'type' => 'link',
    'relationship' => 'aor_scheduled_reports_aor_reports',
    'source' => 'non-db',
    'reportable' => false,
    'side' => 'right',
    'vname' => 'LBL_AOR_SCHEDULED_REPORTS_AOR_REPORTS_FROM_AOR_SCHEDULED_REPORTS_TITLE',
),



),
	'relationships'=>array (
        'aor_scheduled_reports_email_addresses' =>
            array (
                'lhs_module' => 'AOR_Scheduled_Reports',
                'lhs_table' => 'aor_scheduled_reports',
                'lhs_key' => 'id',
                'rhs_module' => 'EmailAddresses',
                'rhs_table' => 'email_addresses',
                'rhs_key' => 'id',
                'relationship_type' => 'many-to-many',
                'join_table' => 'email_addr_bean_rel',
                'join_key_lhs' => 'bean_id',
                'join_key_rhs' => 'email_address_id',
                'relationship_role_column' => 'bean_module',
                'relationship_role_column_value' => 'AOR_Scheduled_Reports',
            ),
        'aor_scheduled_reports_email_addresses_primary' =>
            array (
                'lhs_module' => 'AOR_Scheduled_Reports',
                'lhs_table' => 'aor_scheduled_reports',
                'lhs_key' => 'id',
                'rhs_module' => 'EmailAddresses',
                'rhs_table' => 'email_addresses',
                'rhs_key' => 'id',
                'relationship_type' => 'many-to-many',
                'join_table' => 'email_addr_bean_rel',
                'join_key_lhs' => 'bean_id',
                'join_key_rhs' => 'email_address_id',
                'relationship_role_column' => 'primary_address',
                'relationship_role_column_value' => '1',
            ),
),
	'optimistic_locking'=>true,
		'unified_search'=>true,
	);
if (!class_exists('VardefManager')){
        require_once('include/SugarObjects/VardefManager.php');
}
VardefManager::createVardef('AOR_Scheduled_Reports','AOR_Scheduled_Reports', array('basic','assignable'));