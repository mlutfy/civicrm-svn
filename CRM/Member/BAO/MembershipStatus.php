<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Member/DAO/MembershipStatus.php';

class CRM_Member_BAO_MembershipStatus extends CRM_Member_DAO_MembershipStatus 
{

    /**
     * static holder for the default LT
     */
    static $_defaultMembershipStatus = null;
    

    /**
     * class constructor
     */
    function __construct( ) 
    {
        parent::__construct( );
    }
    
    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. Typically the valid params are only
     * contact_id. We'll tweak this function to be more full featured over a period
     * of time. This is the inverse function of create. It also stores all the retrieved
     * values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the flattened values
     *
     * @return object CRM_Member_BAO_MembershipStatus object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $membershipStatus =& new CRM_Member_DAO_MembershipStatus( );
        $membershipStatus->copyValues( $params );
        if ( $membershipStatus->find( true ) ) {
            CRM_Core_DAO::storeValues( $membershipStatus, $defaults );
            return $membershipStatus;
        }
        return null;
    }

    /**
     * update the is_active flag in the db
     *
     * @param int      $id        id of the database record
     * @param boolean  $is_active value we want to set the is_active field
     *
     * @return Object             DAO object on sucess, null otherwise
     * @static
     */
    static function setIsActive( $id, $is_active ) 
    {
        return CRM_Core_DAO::setFieldValue( 'CRM_Member_DAO_MembershipStatus', $id, 'is_active', $is_active );
    }

    /**
     * function to add the membership types
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
        $params['is_active']         =  CRM_Utils_Array::value( 'is_active', $params, false );
        $params['is_current_member'] =  CRM_Utils_Array::value( 'is_current_member', $params, false );
        $params['is_admin']          =  CRM_Utils_Array::value( 'is_admin', $params, false );
        
        // action is taken depending upon the mode
        $membershipStatus             =& new CRM_Member_DAO_MembershipStatus( );
        $membershipStatus->domain_id    = CRM_Core_Config::domainID( );
        $membershipStatus->copyValues( $params );
        
        $membershipStatus->id = CRM_Utils_Array::value( 'membershipStatus', $ids );

        $membershipStatus->save( );
        return $membershipStatus;
    }
    
    /**
     * Function to delete membership Types 
     * 
     * @param int $membershipStatusId
     * @static
     */
    
    static function del($membershipStatusId) 
    {
        //check dependencies
        require_once 'CRM/Member/DAO/Membership.php';
        $membership =& new CRM_Member_DAO_Membership( );
        $query = 'DELETE FROM civicrm_membership 
                  WHERE       calculated_status_id=' . $membershipStatusId . ' OR override_status_id=' . $membershipStatusId . ';';
        $membership->query($query);
        
        //delete from membership Type table
        require_once 'CRM/Member/DAO/MembershipStatus.php';
        $membershipStatus =& new CRM_Member_DAO_MembershipStatus( );
        $membershipStatus->id = $membershipStatusId;
        $membershipStatus->delete();
    }
}
?>