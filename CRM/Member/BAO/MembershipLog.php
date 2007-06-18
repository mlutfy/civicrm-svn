<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.8                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Member/DAO/MembershipLog.php';

class CRM_Member_BAO_MembershipLog extends CRM_Member_DAO_MembershipLog 
{

    /**
     * function to add the membership log record
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function add(&$params, &$ids) 
    {
        $membershipLog              =& new CRM_Member_DAO_MembershipLog( );
        $membershipLog->copyValues( $params );
        
        $membershipLog->save( );
        $membershipLog->free( );
        
        return $membershipLog;
    }
    
    /**
     * Function to delete membership log record 
     * 
     * @param int $membershipTypeId
     * @static
     */
    
    static function del( $membershipID, $contactID ) 
    {
        $membershipLog  =& new CRM_Member_DAO_MembershipLog( );
        $membershipLog->membership_id = $membershipID ;
        return $membershipLog->delete();
    }

    static function resetModifedID( $contactID ) {
        $query = "
UPDATE civicrm_membership_log
   SET modified_id = null
 WHERE modified_id = %1";

        $params = array( 1 => array( $contactID, 'Integer' ) );
        CRM_Core_DAO::executeQuery( $query, $params );
    }

}
?>
