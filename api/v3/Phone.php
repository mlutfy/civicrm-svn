
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
 * File for the CiviCRM APIv3 phone functions
 *
 * @package CiviCRM_APIv3
 * @subpackage API_Phone
 * 
 * @copyright CiviCRM LLC (c) 2004-2010
 * @version $Id: Phone.php 2011-03-16 ErikHommel $
 */

/**
 * Include utility functions
 */
require_once 'api/v3/utils.php';

/**
 *  Add an Phone for a contact
 * 
 * Allowed @params array keys are:
 * {@schema Core/Phone.xml}
 * {@example PhoneCreate.php}
 * @return array of newly created phone property values.
 * @access public
 */
function civicrm_api3_phone_create( $params ) 
{
  _civicrm_api3_initialize( true );
  try {
    civicrm_api3_verify_one_mandatory ($params, null, array ('contact_id', 'id'));
	/*
	 * if is_primary is not set in params, set default = 0
	 */
	if ( !array_key_exists('is_primary', $params )) {
		$params['is_primary'] = 0; 
	}	
	
    require_once 'CRM/Core/BAO/Phone.php';
    $phoneBAO = CRM_Core_BAO_Phone::add($params);
    
	 if ( is_a( $phoneBAO, 'CRM_Core_Error' )) {
		 return civicrm_api3_create_error( "Phone is not created or updated ");
	 } else {
		 $values = array( );
		 unset($phoneBAO->location_type_id);
		 _civicrm_api3_object_to_array($phoneBAO, $values[$phoneBAO->id]);
		 return civicrm_api3_create_success($values, $params,$phoneBAO);
	 }
  } catch (PEAR_Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  } catch (Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  }
}
/**
 * Deletes an existing Phone
 *
 * @param  array  $params
 *
 * {@schema Core/Phone.xml}
 * {@example PhoneDelete.php 0}
 * @return boolean | error  true if successfull, error otherwise
 * @access public
 */
function civicrm_api3_phone_delete( $params ) 
{
  _civicrm_api3_initialize( true );
  try {
    civicrm_api3_verify_mandatory ($params,null,array ('id'));
    $phoneID = CRM_Utils_Array::value( 'id', $params );

    require_once 'CRM/Core/DAO/Phone.php';
    $phoneDAO = new CRM_Core_DAO_Phone();
    $phoneDAO->id = $phoneID;
    if ( $phoneDAO->find( ) ) {
		while ( $phoneDAO->fetch() ) {
			$phoneDAO->delete();
			return civicrm_api3_create_success($phoneDAO->id,$params,$phoneDAO);
		}
	} else {
		return civicrm_api3_create_error( 'Could not delete phone with id '.$phoneID);
		//recoverable fatal error: Object of class stdClass could not be converted to string in /home/www-home/apps/civicrm-3.3.5/civicrm/api/v3/Phone.php on line 107.
	}
    
  } catch (Exception $e) {
    if (CRM_Core_Error::$modeException) throw $e;
    return civicrm_api3_create_error( $e->getMessage() );
  }
}

/**
 * Retrieve one or more phones 
 *
 * @param  mixed[]  (reference ) input parameters
 * 
 * {@schema Core/Phone.xml}
 * {@example PhoneDelete.php 0}
 * @param  array $params  an associative array of name/value pairs.
 *
 * @return  array details of found phones else error
 * @access public
 */

function civicrm_api3_phone_get($params) 
{   
  _civicrm_api3_initialize(true );
  try {
    civicrm_api3_verify_one_mandatory($params, null, 
		array('id', 'contact_id', 'location_type_id', 'phone_type_id'));
	
    require_once 'CRM/Core/BAO/Phone.php';
    $phoneBAO = new CRM_Core_BAO_Phone();
    $fields = array_keys($phoneBAO->fields());

    foreach ( $fields as $name) {
        if (array_key_exists($name, $params)) {
            $phoneBAO->$name = $params[$name];
        }
    }
    
    if ( $phoneBAO->find() ) {
      $phones = array();
      while ( $phoneBAO->fetch() ) {
        CRM_Core_DAO::storeValues( $phoneBAO, $phone );
        $phones[$phoneBAO->id] = $phone;
      }
      return civicrm_api3_create_success($phones,$params,$phoneBAO);
    } else {
      return civicrm_api3_create_success(array(),$params,$phoneBAO);
    }
				
  } catch (PEAR_Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  } catch (Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  }
}

