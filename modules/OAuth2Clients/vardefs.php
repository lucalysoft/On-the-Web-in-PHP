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

$dictionary['OAuth2Clients'] = [
    'table' => 'oauth2clients',
    'audited' => false,
    'comment' => 'Provides tokens for security services',
    'fields' => [
        'id' => [
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'name' => [
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'varchar',
            'required' => true,
            'reportable' => false,
            'duplicate_merge' => 'disabled',
        ],
        'secret' => [
            'name' => 'secret',
            'vname' => 'LBL_SECRET',
            'type' => 'varchar',
            'required' => true,
            'reportable' => false,
            'api-visible' => false,
            'len' => '4000',
        ],
        'redirect_url' => [
            'name' => 'redirect_url',
            'vname' => 'LBL_REDIRECT_URL',
            'type' => 'varchar',
            'required' => false,
            'reportable' => false,

        ],
        'is_confidential' => [
            'name' => 'is_confidential',
            'vname' => 'LBL_IS_CONFIDENTIAL',
            'type' => 'bool',
            'default' => true,
            'required' => true,
            'reportable' => false,
            'api-visible' => false,
        ],
        'allowed_grant_type' => [
            'name' => 'allowed_grant_type',
            'vname' => 'LBL_ALLOWED_GRANT_TYPE',
            'type' => 'enum',
            'options' => 'oauth2_grant_type_dom',
            'default' => 'Password Grant',
            'required' => true,
            'reportable' => false,
            'api-visible' => false,
            'inline_edit' => false,
        ],
        'duration_value' => [
            'name' => 'duration_value',
            'type' => 'integer',
            'required' => true,
            'reportable' => false,
            'api-visible' => false,
            'inline_edit' => false,
        ],
        'duration_amount' => [
            'name' => 'duration_amount',
            'vname' => 'LBL_DURATION_AMOUNT',
            'type' => 'integer',
            'required' => true,
            'reportable' => false,
            'api-visible' => false,
            'inline_edit' => false,
        ],
        'duration_unit' => [
            'name' => 'duration_unit',
            'vname' => 'LBL_DURATION_UNIT',
            'type' => 'enum',
            'options' => 'oauth2_duration_units',
            'default' => 'Duration Unit',
            'required' => true,
            'reportable' => false,
            'api-visible' => false,
            'inline_edit' => false,
        ],
        'oauth2tokens' => [
            'name' => 'oauth2tokens',
            'vname' => 'LBL_RELATED_OAUTH2TOKENS',
            'type' => 'link',
            'relationship' => 'oauth2clients_oauth2tokens',
            'module' => 'OAuth2Tokens',
            'bean_name' => 'OAuth2Tokens',
            'source' => 'non-db',
        ],
    ],
    'optimistic_locking' => true,
    'relationships' => [
        'oauth2clients_oauth2tokens' => [
            'rhs_module' => 'OAuth2Tokens',
            'rhs_table' => 'oauth2tokens',
            'rhs_key' => 'client',
            'lhs_module' => 'OAuth2Clients',
            'lhs_table' => 'oauth2clients',
            'lhs_key' => 'id',
            'relationship_type' => 'one-to-many',
        ],
    ],
];
if (!class_exists('VardefManager')) {
    require_once('include/SugarObjects/VardefManager.php');
}

VardefManager::createVardef(
    'OAuth2Clients',
    'OAuth2Clients',
    [
        'default',
    ]
);
