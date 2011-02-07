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
 * new version of civicrm apis. See blog post at
 * http://civicrm.org/node/131
 * @todo Write sth
 *
 * @package CiviCRM_APIv3
 * @subpackage API_Contact
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id: Contact.php 30879 2010-11-22 15:45:55Z shot $
 *
 */

/**
 * Include common API util functions
 */
require_once 'api/v3/utils.php';
require_once 'CRM/Contact/BAO/Contact.php';
/**
 * @todo Write sth
 * @todo - make sure it doesn't create new if contact_id is set
 * @todo Erik Hommel 16 dec 2010 introduce version as param
 *
 * @param  array   $params           (reference ) input parameters
 *
 * Allowed @params array keys are:
 * {@schema Contact/Contact.xml}
 * {@schema Core/Address.xml}}
 * 
 * {@example ContactCreate.php 0}
 * 
 * @return array (reference )        contact_id of created or updated contact
 *
 * @static void
 * @access public
 */
function civicrm_contact_create( &$params )
{
    // call update and tell it to create a new contact
  _civicrm_initialize( true );
  try {
    civicrm_api_check_permission(__FUNCTION__, $params, true);

    if(empty($params['contact_id'])){
      $create_new = true;
    }
    return civicrm_contact_update( $params, $create_new );
  } catch (Exception $e) {
    return civicrm_create_error( $e->getMessage() );
  }
}

function civicrm_contact_getfields( &$params ) {
    require_once 'CRM/Contact/BAO/Contact.php';
    $contact = new CRM_Contact_BAO_Contact();
    return ($contact->fields());
}


/**
 * Retrieve one or more contacts, given a set of search params
 *
 * @param  mixed[]  (reference ) input parameters
 *
 * @return array (reference )        array of properties, if error an array with an error id and error message
 * @static void
 * @access public
 *
 * {@example ContactGet.php 0}
 * 
 * @todo Erik Hommel 16 dec 2010 Check that all DB fields are returned
 * @todo Erik Hommel 16 dec 2010 fix custom data (CRM-7231)
 * @todo Erik Hommel 16 dec 2010 Introduce version as param and get rid of $deprecated_behaviour
 * @todo Erik Hommel 16 dec 2010 Use civicrm_return_success / error ?
 * @todo EM 7 Jan 11 - does this return the number of contacts if required (replacement for deprecated contact_search_count function - if so is this tested?
 */

function civicrm_contact_get( &$params )
{
  _civicrm_initialize( );
  try {
    civicrm_verify_mandatory($params);
        // fix for CRM-7384 cater for soft deleted contacts
    $params['contact_is_deleted'] = 0;
    if (isset($params['showAll'])) {
        if (strtolower($params['showAll']) == "active") {
            $params['contact_is_deleted'] = 0;
        }
        if (strtolower($params['showAll']) == "trash") {
            $params['contact_is_deleted'] = 1;
        }
        if (strtolower($params['showAll']) == "all" && isset($params['contact_is_deleted'])) {
            unset($params['contact_is_deleted']);
        }
    }

    $inputParams      = array( );
    $returnProperties = array( );
    $otherVars = array( 'sort', 'offset', 'rowCount', 'smartGroupCache' );

    $sort            = null;
    $offset          = 0;
    $rowCount        = 25;
    $smartGroupCache = false;
    if ( array_key_exists ('return',$params)) {// handle the format return =sort_name,display_name...
      $returnProperties = explode (',',$params['return']);
      $returnProperties = array_flip ($returnProperties); 
      $returnProperties[key($returnProperties)] = 1; 
    }
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 6 ) == 'return' ) { // handle the format return.sort_name=1,return.display_name=1
            $returnProperties[ substr( $n, 7 ) ] = $v;
        } elseif ( in_array( $n, $otherVars ) ) {
            $$n = $v;
        } else {
            $inputParams[$n] = $v;
        }
    }

    if ( empty( $returnProperties ) ) {
        $returnProperties = null;
    }

    require_once 'CRM/Contact/BAO/Query.php';
    $newParams =& CRM_Contact_BAO_Query::convertFormValues( $inputParams );
    list( $contacts, $options ) = CRM_Contact_BAO_Query::apiQuery( $newParams,
                                                                   $returnProperties,
                                                                   null,
                                                                   $sort,
                                                                   $offset,
                                                                   $rowCount,
                                                                   $smartGroupCache );

    return civicrm_create_success($contacts,$params);
  } catch (PEAR_Exception $e) {
    return civicrm_create_error( $e->getMessage() );
  } catch (Exception $e) {
    return civicrm_create_error( $e->getMessage() );
  }
}


/**
 * Delete a contact with given contact id
 *
 * @param  array   	  $params (reference ) input parameters, contact_id element required
 *
 * @return boolean        true if success, else false
 * @static void
 * @access public
 * 
 * @example ContactDelete.php
 */
function civicrm_contact_delete( &$params )
{    
    _civicrm_initialize(true);
    require_once 'CRM/Contact/BAO/Contact.php';

    $contactID = CRM_Utils_Array::value( 'contact_id', $params );
    if ( ! $contactID ) {
        return civicrm_create_error(  'Could not find contact_id in input parameters'  );
    }

    $session =& CRM_Core_Session::singleton( );
    if ( $contactID ==  $session->get( 'userID' ) ) {
        return civicrm_create_error(  'This contact record is linked to the currently logged in user account - and cannot be deleted.'  );
    }
    $restore      = CRM_Utils_Array::value( 'restore', $params ) ? $params['restore'] : false;
    $skipUndelete = CRM_Utils_Array::value( 'skip_undelete', $params ) ? $params['skip_undelete'] : false;
    if ( CRM_Contact_BAO_Contact::deleteContact( $contactID , $restore, $skipUndelete) ) {
        return civicrm_create_success( );
    } else {
        return civicrm_create_error(  'Could not delete contact'  );
    }
}


/**
 * Ensure that we have the right input parameters
 *
 * @todo We also need to make sure we run all the form rules on the params list
 *       to ensure that the params are valid
 * @todo Eileen McNaughton 7 Jan 11 update isn't part of our standard - my preference is to rename to _ & copy the small amount of code in existing _ function into this one
 * @todo Eileen McNaughton 7 Jan 11 Would be good to have some clarity on what is done on e-mails when create_new is set & why not for updates
 *
 * @param array   $params          Associative array of property name/value
 *                                 pairs to insert in new contact.
 *
 * @return null on success, error message otherwise
 * @access public
 *
 * @todo Erik Hommel 16 dec 2010 required check should be incorporated in utils function civicrm_verify_mandatory
 */
function civicrm_contact_update( &$params, $create_new = false )
{
    _civicrm_initialize();
    try {
        civicrm_api_check_permission(__FUNCTION__, $params, true);
    } catch (Exception $e) {
        return civicrm_create_error($e->getMessage());
    }
    require_once 'CRM/Utils/Array.php';
    $contactID = CRM_Utils_Array::value( 'contact_id', $params );

    $dupeCheck = CRM_Utils_Array::value( 'dupe_check', $params, false );
    $values    = _civicrm_contact_check_params( $params, $dupeCheck );
    if ( $values ) {
        return $values;
    }
    
    if ( $create_new ) {
        // Make sure nothing is screwed up before we create a new contact
        if ( !empty( $contactID ) ) {
            return civicrm_create_error( 'Cannot create new contact when contact_id is present' );
        }
        if ( empty( $params[ 'contact_type' ] ) ) {
            return civicrm_create_error( 'Contact Type not specified' );
        }
        
        // If we get here, we're ready to create a new contact
        if ( ($email = CRM_Utils_Array::value( 'email', $params ) ) && !is_array( $params['email'] ) ) {
            require_once 'CRM/Core/BAO/LocationType.php';
            $defLocType = CRM_Core_BAO_LocationType::getDefault( );
            $params['email'] = array( 1 => array( 'email'            => $email,
                                                  'is_primary'       => 1, 
                                                  'location_type_id' => ($defLocType->id)?$defLocType->id:1
                                                  ),
                                      );
        }
    }

    if ( $homeUrl = CRM_Utils_Array::value( 'home_url', $params ) ) {  
        require_once 'CRM/Core/PseudoConstant.php';
        $websiteTypes = CRM_Core_PseudoConstant::websiteType( );
        $params['website'] = array( 1 => array( 'website_type_id' => key( $websiteTypes ),
                                                'url'             => $homeUrl 
                                                )
                                    );  
    }

    if ( isset( $params['suffix_id'] ) &&
         ! ( is_numeric( $params['suffix_id'] ) ) ) {
        $params['suffix_id'] = array_search( $params['suffix_id'] , CRM_Core_PseudoConstant::individualSuffix() );
    }

    if ( isset( $params['prefix_id'] ) &&
         ! ( is_numeric( $params['prefix_id'] ) ) ) {
        $params['prefix_id'] = array_search( $params['prefix_id'] , CRM_Core_PseudoConstant::individualPrefix() );
    }

         if ( isset( $params['gender_id'] )
              && ! ( is_numeric( $params['gender_id'] ) ) ) {
        $params['gender_id'] = array_search( $params['gender_id'] , CRM_Core_PseudoConstant::gender() );
    }
    
    $error = _civicrm_greeting_format_params( $params );
    if ( $error['error_message'] ) {
        return $error['error_message'];
    }
    
    $values   = array( );
    $entityId = CRM_Utils_Array::value( 'contact_id', $params, null );

    if ( ! CRM_Utils_Array::value('contact_type', $params) &&
         $entityId ) {
        $params['contact_type'] = CRM_Contact_BAO_Contact::getContactType( $entityId );
    }
    
    if ( ! ( $csType = CRM_Utils_Array::value('contact_sub_type', $params) ) &&
         $entityId ) {
        require_once 'CRM/Contact/BAO/Contact.php';
        $csType = CRM_Contact_BAO_Contact::getContactSubType( $entityId );
    }
    
    $customValue = _civicrm_contact_check_custom_params( $params, $csType ); 

    if ( $customValue ) {
        return $customValue;
    }
    _civicrm_custom_format_params( $params, $values, $params['contact_type'], $entityId );

    $params = array_merge( $params, $values );

    $contact =& _civicrm_contact_update( $params, $contactID );

    if ( is_a( $contact, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( $contact->_errors[0]['message'] );
    } else {
        $values = array( );
        _civicrm_object_to_array_unique_fields($contact, $values[$contact->id]);
     
    }

    return civicrm_create_success($values,$params);
}
function _civicrm_contact_check_params( &$params, $dupeCheck = true, $dupeErrorArray = false, $requiredCheck = true )
{
    if ( $requiredCheck ) {
        $required = array(
                          'Individual'   => array(
                                                  array( 'first_name', 'last_name' ),
                                                  'email',
                                                  ),
                          'Household'    => array(
                                                  'household_name',
                                                  ),
                          'Organization' => array(
                                                  'organization_name',
                                                  ),
                          );
        
        // cannot create a contact with empty params
        if ( empty( $params ) ) {
            return civicrm_create_error( 'Input Parameters empty' );
        }
        
        if ( ! array_key_exists( 'contact_type', $params ) ) {
            return civicrm_create_error( 'Contact Type not specified' );
        }
        
        // contact_type has a limited number of valid values
        $fields = CRM_Utils_Array::value( $params['contact_type'], $required );
        if ( $fields == null ) {
            return civicrm_create_error( "Invalid Contact Type: {$params['contact_type']}" );
        }
        
        if ( $csType = CRM_Utils_Array::value('contact_sub_type', $params) ) {
            if ( !(CRM_Contact_BAO_ContactType::isExtendsContactType($csType, $params['contact_type'])) ) {
                return civicrm_create_error( "Invalid or Mismatched Contact SubType: {$csType}" );
            }
        }

        if ( !CRM_Utils_Array::value( 'contact_id', $params ) ) { 
            $valid = false;
            $error = '';
            foreach ( $fields as $field ) {
                if ( is_array( $field ) ) {
                    $valid = true;
                    foreach ( $field as $element ) {
                        if ( ! CRM_Utils_Array::value( $element, $params ) ) {
                            $valid = false;
                            $error .= $element; 
                            break;
                        }
                    }
                } else {
                    if ( CRM_Utils_Array::value( $field, $params ) ) {
                        $valid = true;
                    }
                }
                if ( $valid ) {
                    break;
                }
            }
            
            if ( ! $valid ) {
                return civicrm_create_error( "Required fields not found for {$params['contact_type']} : $error" );
            }
        }
    }
    
    if ( $dupeCheck ) {
        // check for record already existing
        require_once 'CRM/Dedupe/Finder.php';
        $dedupeParams = CRM_Dedupe_Finder::formatParams($params, $params['contact_type']);

        // CRM-6431
        // setting 'check_permission' here means that the dedupe checking will be carried out even if the 
        // person does not have permission to carry out de-dupes
        // this is similar to the front end form
        if (isset($params['check_permission'])){
            $dedupeParams['check_permission'] = $fields['check_permission'];
        }

        $ids = implode(',', CRM_Dedupe_Finder::dupesByParams($dedupeParams, $params['contact_type']));
        
        if ( $ids != null ) {
            if ( $dupeErrorArray ) {
                $error = CRM_Core_Error::createError( "Found matching contacts: $ids",
                                                      CRM_Core_Error::DUPLICATE_CONTACT, 
                                                      'Fatal', $ids );
                return civicrm_create_error( $error->pop( ) );
            }
            
            return civicrm_create_error( "Found matching contacts: $ids", $ids );
        }
    }

    //check for organisations with same name
    if ( CRM_Utils_Array::value( 'current_employer', $params ) ) {
        $organizationParams = array();
        $organizationParams['organization_name'] = $params['current_employer'];
        
        require_once 'CRM/Dedupe/Finder.php';
        $dedupParams = CRM_Dedupe_Finder::formatParams($organizationParams, 'Organization');
        
        $dedupParams['check_permission'] = false;            
        $dupeIds = CRM_Dedupe_Finder::dupesByParams($dedupParams, 'Organization', 'Fuzzy');
        
        // check for mismatch employer name and id
        if ( CRM_Utils_Array::value( 'employer_id', $params )
             && !in_array( $params['employer_id'] ,$dupeIds ) ) {
            return civicrm_create_error('Employer name and Employer id Mismatch');
        }
        
        // show error if multiple organisation with same name exist
        if ( !CRM_Utils_Array::value( 'employer_id', $params )
             && (count($dupeIds) > 1) ) {
            return civicrm_create_error('Found more than one Organisation with same Name.');
        }
    }
    
    return null;
}


/** 
 * Takes an associative array and creates a contact object and all the associated 
 * derived objects (i.e. individual, location, email, phone etc) 
 * 
 * @param array $params (reference ) an assoc array of name/value pairs 
 * @param  int     $contactID        if present the contact with that ID is updated
 * 
 * @return object CRM_Contact_BAO_Contact object  
 * @access public 
 * @static 
 */ 
function _civicrm_contact_update( &$params, $contactID = null )
{
    require_once 'CRM/Core/Transaction.php';
    $transaction = new CRM_Core_Transaction( );

    if ( $contactID ) {
        $params['contact_id'] = $contactID;
    }
    require_once 'CRM/Contact/BAO/Contact.php';
    
    $contact = CRM_Contact_BAO_Contact::create( $params );

    $transaction->commit( );

    return $contact;
}

/**
 * @todo Move this to ContactFormat.php 
 * @todo Eileen McNaughton 7/01/11 What does this do? I think it should go & we can revive a corrected version from v2 if need be
 */
function civicrm_contact_format_create( &$params )
{
    _civicrm_initialize( );

    CRM_Core_DAO::freeResult( );

    // return error if we have no params
    if ( empty( $params ) ) {
        return civicrm_create_error( 'Input Parameters empty' );
    }

    $error = _civicrm_required_formatted_contact($params);
    if (civicrm_error( $error, 'CRM_Core_Error')) {
        return $error;
    }
    
    $error = _civicrm_validate_formatted_contact($params);
    if (civicrm_error( $error, 'CRM_Core_Error')) {
        return $error;
    }

    //get the prefix id etc if exists
    require_once 'CRM/Contact/BAO/Contact.php';
    CRM_Contact_BAO_Contact::resolveDefaults($params, true);

    require_once 'CRM/Import/Parser.php';
    if ( CRM_Utils_Array::value('onDuplicate', $params) != CRM_Import_Parser::DUPLICATE_NOCHECK) {
        CRM_Core_Error::reset( );
        $error = _civicrm_duplicate_formatted_contact($params);
        if (civicrm_error( $error, 'CRM_Core_Error')) {
            return $error;
        }
    }
    
    $contact = CRM_Contact_BAO_Contact::create( $params, 
                                                CRM_Utils_Array::value( 'fixAddress',  $params ) );
    
    _civicrm_object_to_array($contact, $contactArray);
    return $contactArray;
}

/**
 * Ensure that we have the right input parameters for custom data
 *
 * @param array   $params          Associative array of property name/value
 *                                 pairs to insert in new contact.
 * @param string  $csType          contact subtype if exists/passed.
 *
 * @return null on success, error message otherwise
 * @access public
 */
function _civicrm_contact_check_custom_params( $params, $csType = null )
{
    empty($csType) ? $onlyParent = true : $onlyParent = false;
    
    require_once 'CRM/Core/BAO/CustomField.php';
    $customFields = CRM_Core_BAO_CustomField::getFields( $params['contact_type'], false, false, $csType, null, $onlyParent );
    
    foreach ($params as $key => $value) {
        if ($customFieldID = CRM_Core_BAO_CustomField::getKeyID($key)) {
            /* check if it's a valid custom field id */
            if ( !array_key_exists($customFieldID, $customFields)) {

                $errorMsg = "Invalid Custom Field Contact Type: {$params['contact_type']}";
                if ( $csType ) {
                    $errorMsg .= " or Mismatched SubType: {$csType}.";  
                }
                return civicrm_create_error( $errorMsg );  
            }
        }
    }
}

/**
 * Validate the addressee or email or postal greetings 
 *
 * @param  $params                   Associative array of property name/value
 *                                   pairs to insert in new contact.
 * 
 * @return array (reference )        null on success, error message otherwise
 *
 * @access public
 */
function _civicrm_greeting_format_params( &$params ) 
{
    $greetingParams = array( '', '_id', '_custom' );
    foreach ( array( 'email', 'postal', 'addressee' ) as $key ) {
        $greeting = '_greeting';
        if ( $key == 'addressee' ) {
            $greeting = '';   
        } 

        $formatParams = false;
        // unset display value from params.
        if ( isset( $params["{$key}{$greeting}_display"] ) ) {
            unset( $params["{$key}{$greeting}_display"] );  
        }

        // check if greetings are present in present
        foreach ( $greetingParams as $greetingValues ) {
            if ( array_key_exists( "{$key}{$greeting}{$greetingValues}", $params ) ) {
                $formatParams = true;
                break;
            }
        }

        if ( !$formatParams ) continue;
    
        // format params
        if ( CRM_Utils_Array::value( 'contact_type', $params ) == 'Organization' && $key != 'addressee' ) {
            return civicrm_create_error( 'You cannot use email/postal greetings for contact type %1.', 
                                             array( 1 => $params['contact_type'] ) );
        }
        
        $nullValue      = false; 
        $filter         = array( 'contact_type'  => $params['contact_type'],
                                 'greeting_type' => "{$key}{$greeting}" );
        
        $greetings      = CRM_Core_PseudoConstant::greeting( $filter );
        $greetingId     = CRM_Utils_Array::value( "{$key}{$greeting}_id",     $params );
        $greetingVal    = CRM_Utils_Array::value( "{$key}{$greeting}",        $params );
        $customGreeting = CRM_Utils_Array::value( "{$key}{$greeting}_custom", $params );
        
        if ( !$greetingId && $greetingVal ) {
            $params["{$key}{$greeting}_id"] = CRM_Utils_Array::key( $params["{$key}{$greeting}"], $greetings );
        }
        
        if ( $customGreeting && $greetingId &&
             ( $greetingId != array_search( 'Customized', $greetings ) ) ) {
            return civicrm_create_error( 'Provide either %1 greeting id and/or %1 greeting or custom %1 greeting',
                                             array( 1 => $key ) );
        }
        
        if ( $greetingVal && $greetingId &&
             ( $greetingId != CRM_Utils_Array::key( $greetingVal, $greetings ) ) ) {
            return civicrm_create_error( 'Mismatch in %1 greeting id and %1 greeting',
                                             array( 1 => $key ) );
        } 
        
        if ( $greetingId ) {

            if ( !array_key_exists( $greetingId, $greetings ) ) {
                return civicrm_create_error( 'Invalid %1 greeting Id', array( 1 => $key ) );
            }
            
            if ( !$customGreeting && ( $greetingId == array_search( 'Customized', $greetings ) ) ) {
                return civicrm_create_error( 'Please provide a custom value for %1 greeting', 
                                                 array( 1 => $key ) );
            }
                        
        } else if ( $greetingVal ) {

            if ( !in_array( $greetingVal, $greetings ) ) {
                return civicrm_create_error( 'Invalid %1 greeting', array( 1 => $key ) );
            }

            $greetingId = CRM_Utils_Array::key( $greetingVal, $greetings );
        }
                     
        if ( $customGreeting ) {
            $greetingId = CRM_Utils_Array::key( 'Customized', $greetings );
        }

        $customValue = $params['contact_id'] ? CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact', 
                                                                            $params['contact_id'], 
                                                                            "{$key}{$greeting}_custom" ) : false;
                
        if ( array_key_exists( "{$key}{$greeting}_id", $params ) && empty( $params["{$key}{$greeting}_id"] ) ) {
            $nullValue = true;
        } else if ( array_key_exists( "{$key}{$greeting}", $params ) && empty( $params["{$key}{$greeting}"] ) ) {
            $nullValue = true;
        } else if ( $customValue && array_key_exists( "{$key}{$greeting}_custom", $params ) 
                    && empty( $params["{$key}{$greeting}_custom"] ) ) {
            $nullValue = true;
        }

        $params["{$key}{$greeting}_id"] = $greetingId;

        if ( !$customValue && !$customGreeting && array_key_exists( "{$key}{$greeting}_custom", $params ) ) {
            unset( $params["{$key}{$greeting}_custom"] );
        }
        
        if ( $nullValue ) {
            $params["{$key}{$greeting}_id"]     = '';
            $params["{$key}{$greeting}_custom"] = '';
        }
                                
        if ( isset( $params["{$key}{$greeting}"] ) ) {
            unset( $params["{$key}{$greeting}"] );
        }
    }
}
