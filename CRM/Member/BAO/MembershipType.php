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

require_once 'CRM/Member/DAO/MembershipType.php';

class CRM_Member_BAO_MembershipType extends CRM_Member_DAO_MembershipType 
{

    /**
     * static holder for the default LT
     */
    static $_defaultMembershipType = null;
    

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
     * @return object CRM_Member_BAO_MembershipType object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults ) 
    {
        $membershipType =& new CRM_Member_DAO_MembershipType( );
        $membershipType->copyValues( $params );
        self::getDatesForMembershipType($params['id']);
        if ( $membershipType->find( true ) ) {
            CRM_Core_DAO::storeValues( $membershipType, $defaults );
            return $membershipType;
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
        return CRM_Core_DAO::setFieldValue( 'CRM_Member_DAO_MembershipType', $id, 'is_active', $is_active );
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
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        
        // action is taken depending upon the mode
        $membershipType               =& new CRM_Member_DAO_MembershipType( );
        $membershipType->domain_id    = CRM_Core_Config::domainID( );
        
        $membershipType->copyValues( $params );
        
        $membershipType->id = CRM_Utils_Array::value( 'membershipType', $ids );
        $membershipType->member_of_contact_id = CRM_Utils_Array::value( 'memberOfContact', $ids );
        $membershipType->contribution_type_id = CRM_Utils_Array::value( 'contributionType', $ids );

        $membershipType->save( );
        return $membershipType;
    }
    
    /**
     * Function to delete membership Types 
     * 
     * @param int $membershipTypeId
     * @static
     */
    
    static function del($membershipTypeId) 
    {
        //check dependencies
        
        //delete from membership Type table
        require_once 'CRM/Member/DAO/MembershipType.php';
        $membershipType =& new CRM_Member_DAO_MembershipType( );
        $membershipType->id = $membershipTypeId;
        $membershipType->delete();
    }


    /**
     * Function to get membership Types 
     * 
     * @param int $membershipTypeId
     * @static
     */
    static function getMembershipTypes()
    {
        require_once 'CRM/Member/DAO/Membership.php';
        $membershipTypes = array();
        $membershipType =& new CRM_Member_DAO_MembershipType( );
        $membershipType->is_active = 1;
        $membershipType->visibility = 'Public';
        $membershipType->orderBy(' weight');
        $membershipType->find();
        while ( $membershipType->fetch() ) {
            $membershipTypes[$membershipType->id] = $membershipType->name; 
        }
        return $membershipTypes;
     }
    
    /**
     * Function to get membership Type Details 
     * 
     * @param int $membershipTypeId
     * @static
     */
    function getMembershipTypeDetails( $membershipTypeId ) 
    {
        require_once 'CRM/Member/DAO/Membership.php';
        $membershipTypeDetails = array();
        
        $membershipType =& new CRM_Member_DAO_MembershipType( );
        $membershipType->is_active = 1;
        $membershipType->id = $membershipTypeId;
        if ( $membershipType->find(true) ) {
            CRM_Core_DAO::storeValues($membershipType, $membershipTypeDetails );
            return   $membershipTypeDetails;
        } else {
            return null;
        }
    }

    /**
     * Function to calculate start date and end date for new membership 
     * 
     * @param int $membershipTypeId
     * @return Array array fo the start date, end date and join date of the membership
     * @static
     */
    function getDatesForMembershipType( $membershipTypeId ) 
    {
        $membershipTypeDetails = self::getMembershipTypeDetails( $membershipTypeId );
        $joinDate = date('Y-m-d');
        
        if ( $membershipTypeDetails['period_type'] == 'rolling' ) {
            $startDate  = $joinDate;
        } else if ( $membershipTypeDetails['period_type'] == 'fixed' ) {
            $toDay  = explode('-', date('Y-m-d') );
            $month     = substr( $membershipTypeDetails['fixed_period_start_day'], 0, strlen($membershipTypeDetails['fixed_period_start_day'])-2);
            $day       = substr( $membershipTypeDetails['fixed_period_start_day'],-2);
            $year      = $toDay[0];
            $startDate = $year.'-'.$month.'-'.$day;
        }
       
        if ( $membershipTypeDetails['period_type'] == 'fixed' && $membershipTypeDetails['fixed_period_rollover_day'] != null ) {
            $toDay  = explode('-', date('Y-m-d') );
            $month     = substr( $membershipTypeDetails['fixed_period_rollover_day'], 0, strlen($membershipTypeDetails['fixed_period_rollover_day'])-2);
            $day       = substr( $membershipTypeDetails['fixed_period_rollover_day'],-2);
            if ( $month > $toDay[1] ) {
                $fixed_period_rollover = true;
            } else if ( $month == $toDay[1] && $day >= $toDay[2]) {
                $fixed_period_rollover = true;
            } else {
                $fixed_period_rollover = false;
            }
                
        }
        
        $date  = explode('-', $startDate );
        $year  = $date[0];
        $month = $date[1];
        $day   = $date[2];
        
        switch ( $membershipTypeDetails['duration_unit'] ) {
            
        case 'year' :
            if ( $fixed_period_rollover ) {
                $year  = $year   + 2*$membershipTypeDetails['duration_interval'];
            } else {
                $year  = $year   + $membershipTypeDetails['duration_interval'];
            }
            break;
        case 'month':
            if( $fixed_period_rollover ) {
                $month = $month  + 2*$membershipTypeDetails['duration_interval'];
            } else {
                $month = $month  + $membershipTypeDetails['duration_interval'];
            }
            break;
        case 'day':
            if ( $fixed_period_rollover ) {
                $day   = $day    + 2*$membershipTypeDetails['duration_interval'];
            } else {
                $day   = $day    + $membershipTypeDetails['duration_interval'];
            }
            break;
            
        }
        $endDate = date('Y-m-d',mktime($hour, $minute, $second, $month, $day-1, $year));
        $membershipDates = array();
        $membershipDates['start_date']  = $startDate;
        $membershipDates['end_date']    = $endDate;
        $membershipDates['join_date']   = $joinDate;

        return $membershipDates;
        
    }


}

?>
