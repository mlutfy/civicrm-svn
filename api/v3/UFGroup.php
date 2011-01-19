<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * File for the CiviCRM APIv3 user framework group functions
 *
 * @package CiviCRM_APIv3
 * @subpackage API_UF
 * 
 * @copyright CiviCRM LLC (c) 2004-2010
 * @version $Id: UFGroup.php 30171 2010-10-14 09:11:27Z mover $
 *
 */


/**
 * Files required for this package
 */
require_once 'api/v3/utils.php'; 
require_once 'CRM/Core/BAO/UFGroup.php';


/**
 * Use this API to create a new group. See the CRM Data Model for uf_group property definitions
 *
 * @param $params  array   Associative array of property name/value pairs to insert in group.
 *
 * @return   Newly create $ufGroupArray array
 *
 * @access public 
 */
function civicrm_uf_group_create($params, $groupId = null)
{
    if (!is_array($params) or empty($params) or (int) $groupId < 1) {
        return civicrm_create_error('Params must be a non-empty array and a positive integer.');
    }
    
    _civicrm_initialize( );
    
    $ids = array();
    $ids['ufgroup'] = $groupId;
    
    require_once 'CRM/Core/BAO/UFGroup.php';
    
    $ufGroup = CRM_Core_BAO_UFGroup::add( $params,$ids );
    _civicrm_object_to_array( $ufGroup, $ufGroupArray);
    
    return $ufGroupArray;
}



/**
 * Delete uf group
 *  
 * @param $groupId int  Valid uf_group id that to be deleted
 *
 * @return true on successful delete or return error
 *
 * @access public
 *
 */
function civicrm_uf_group_delete( $groupId ) {
    _civicrm_initialize( );
    
    if(! isset( $groupId ) ) {
        return civicrm_create_error("provide a valid groupId.");
    }
    
    require_once 'CRM/Core/BAO/UFGroup.php';
    return CRM_Core_BAO_UFGroup::del($groupId);

}