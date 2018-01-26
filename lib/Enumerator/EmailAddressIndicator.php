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

namespace SuiteCRM\Enumerator;


/**
 * Class ExceptionCode
 * @package SuiteCRM\Enumerator
 * Holds al opp in status codes
 */
class EmailAddressIndicator
{
    const INVALID_OPT_IN_SETTINGS = 'INVALID_OPT_IN_SETTINGS';
    const OPT_IN = 'OPT_IN';
    const OPT_IN_DISABLED = 'OPT_IN_DISABLED';
    const OPT_IN_PENDING_EMAIL_CONFIRMED = 'OPT_IN_PENDING_EMAIL_CONFIRMED';
    const OPT_IN_PENDING_EMAIL_FAILED = 'OPT_IN_PENDING_EMAIL_FAILED';
    const OPT_IN_PENDING_EMAIL_NOT_SENT = 'OPT_IN_PENDING_EMAIL_NOT_SENT';
    const OPT_IN_PENDING_EMAIL_SENT = 'OPT_IN_PENDING_EMAIL_SENT';
    const OPT_OUT = 'OPT_OUT';
    const UNKNOWN_OPT_IN_STATUS = 'UNKNOWN_OPT_IN_STATUS';
    const INVALID = 'INVALID';


    public static function isOptedInStatus($emailAddressIndicatorStatus = EmailAddressIndicator::UNKNOWN_OPT_IN_STATUS)
    {
        return in_array($emailAddressIndicatorStatus, array(
            EmailAddressIndicator::OPT_IN_PENDING_EMAIL_CONFIRMED,
            EmailAddressIndicator::OPT_IN_PENDING_EMAIL_SENT,
            EmailAddressIndicator::OPT_IN_PENDING_EMAIL_NOT_SENT,
            EmailAddressIndicator::OPT_IN_PENDING_EMAIL_FAILED,
        ), true);
    }
}
