<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */


/**
 * This class contains the funtions for Component export
 *
 */
class CRM_Export_BAO_Export
{
    /**
     * Function to get the list the export fields
     *
     * @param int    $selectAll user preference while export
     * @param array  $ids  contact ids
     * @param array  $params associated array of fields
     * @param string $order order by clause
     * @param array  $associated array of fields
     * @param array  $moreReturnProperties additional return fields
     * @param int    $exportMode export mode
     * @param string $componentClause component clause
     *
     * @static
     * @access public
     */
    static function exportComponents( $selectAll, $ids, $params, $order = null, 
                                      $fields = null, $moreReturnProperties = null, 
                                      $exportMode = CRM_Export_Form_Select::CONTACT_EXPORT,
                                      $componentClause = null )
    {
        $headerRows       = array();
        $primary          = false;
        $returnProperties = array( );
        $origFields       = $fields;
        $queryMode        = null; 
        $paymentFields    = false;

        $phoneTypes = CRM_Core_PseudoConstant::phoneType();
        $imProviders = CRM_Core_PseudoConstant::IMProvider();
        $contactRelationshipTypes = CRM_Contact_BAO_Relationship::getContactRelationshipType( null, null, null, null, true );
        $queryMode = CRM_Contact_BAO_Query::MODE_CONTACTS;
        
        switch ( $exportMode )  {
        case CRM_Export_Form_Select::CONTRIBUTE_EXPORT :
            $queryMode = CRM_Contact_BAO_Query::MODE_CONTRIBUTE;
            break;
        case CRM_Export_Form_Select::EVENT_EXPORT :
            $queryMode = CRM_Contact_BAO_Query::MODE_EVENT;
            break;
        case CRM_Export_Form_Select::MEMBER_EXPORT :
            $queryMode = CRM_Contact_BAO_Query::MODE_MEMBER;
            break;
        case CRM_Export_Form_Select::PLEDGE_EXPORT :
            $queryMode = CRM_Contact_BAO_Query::MODE_PLEDGE;
            break;
        case CRM_Export_Form_Select::CASE_EXPORT :
            $queryMode = CRM_Contact_BAO_Query::MODE_CASE;
            break;
        }
        require_once 'CRM/Core/BAO/CustomField.php';
        if ( $fields ) {
            //construct return properties 
            $locationTypes =& CRM_Core_PseudoConstant::locationType();
            $locationTypeFields =  array ('street_address','supplemental_address_1', 'supplemental_address_2', 'city', 'postal_code', 'postal_code_suffix', 'geo_code_1', 'geo_code_2', 'state_province', 'country', 'phone', 'email', 'im' );
            foreach ( $fields as $key => $value) {
                $phoneTypeId  = null;
                $imProviderId = null;
                $relationshipTypes = $fieldName   = CRM_Utils_Array::value( 1, $value );
                if ( ! $fieldName ) {
                    continue;
                }
                // get phoneType id and IM service provider id seperately
                if ( $fieldName == 'phone' ) { 
                    $phoneTypeId = CRM_Utils_Array::value( 3, $value );
                } else if ( $fieldName == 'im' ) { 
                    $imProviderId = CRM_Utils_Array::value( 3, $value );
                }
                
                if ( array_key_exists ( $relationshipTypes, $contactRelationshipTypes )  ) {
                    if ( CRM_Utils_Array::value( 2, $value ) ) {
                        $relationField = CRM_Utils_Array::value( 2, $value );
                        if ( trim ( CRM_Utils_Array::value( 3, $value ) ) ) {
                            $relLocTypeId = CRM_Utils_Array::value( 3, $value );
                        } else {
                            $relLocTypeId = 1;
                        }

                        if ( $relationField == 'phone' ) { 
                            $relPhoneTypeId  = CRM_Utils_Array::value( 4, $value );                            
                        } else if ( $relationField == 'im' ) {
                            $relIMProviderId = CRM_Utils_Array::value( 4, $value );
                        }
                    } else if ( CRM_Utils_Array::value( 4, $value ) ) {
                        $relationField  = CRM_Utils_Array::value( 4, $value );
                        $relLocTypeId   = CRM_Utils_Array::value( 5, $value );
                        if ( $relationField == 'phone' ) { 
                            $relPhoneTypeId  = CRM_Utils_Array::value( 6, $value );                            
                        } else if ( $relationField == 'im' ) {
                            $relIMProviderId = CRM_Utils_Array::value( 6, $value );
                        }
                    }                    
                }

                $contactType       = CRM_Utils_Array::value( 0, $value );
                $locTypeId         = CRM_Utils_Array::value( 2, $value );
                $phoneTypeId       = CRM_Utils_Array::value( 3, $value );

                
                if ( $relationField ) {
                    if ( in_array ( $relationField, $locationTypeFields ) ) {
                        if ( $relPhoneTypeId ) {                            
                            $returnProperties[$relationshipTypes]['location'][$locationTypes[$relLocTypeId]]['phone-' .$relPhoneTypeId] = 1;
                        } else if ( $relIMProviderId ) {                            
                            $returnProperties[$relationshipTypes]['location'][$locationTypes[$relLocTypeId]]['im-' .$relIMProviderId] = 1;
                        } else {
                            $returnProperties[$relationshipTypes]['location'][$locationTypes[$relLocTypeId]][$relationField] = 1;
                        } 
                        $relPhoneTypeId = $relIMProviderId = null;                       
                    } else {
                        $returnProperties[$relationshipTypes][$relationField]  = 1;
                    }                    
                } else if ( is_numeric($locTypeId) ) {
                    if ($phoneTypeId) {
                        $returnProperties['location'][$locationTypes[$locTypeId]]['phone-' .$phoneTypeId] = 1;
                    } else if ( isset($imProviderId) ) { 
                        //build returnProperties for IM service provider
                        $returnProperties['location'][$locationTypes[$locTypeId]]['im-' .$imProviderId] = 1;
                    } else {
                        $returnProperties['location'][$locationTypes[$locTypeId]][$fieldName] = 1;
                    }
                } else {
                    //hack to fix component fields
                    if ( $fieldName == 'event_id' ) {
                        $returnProperties['event_title'] = 1;
                    } else {
                        $returnProperties[$fieldName] = 1;
                    }
                }
            }

            // hack to add default returnproperty based on export mode
            if ( $exportMode == CRM_Export_Form_Select::CONTRIBUTE_EXPORT ) {
                $returnProperties['contribution_id'] = 1;
            } else if ( $exportMode == CRM_Export_Form_Select::EVENT_EXPORT ) {
                $returnProperties['participant_id'] = 1;
            } else if ( $exportMode == CRM_Export_Form_Select::MEMBER_EXPORT ) {
                $returnProperties['membership_id'] = 1;
            } else if ( $exportMode == CRM_Export_Form_Select::PLEDGE_EXPORT ) {
                $returnProperties['pledge_id'] = 1;
            } else if ( $exportMode == CRM_Export_Form_Select::CASE_EXPORT ) {
                $returnProperties['case_id'] = 1;
            }
         } else {
            $primary = true;
            $fields = CRM_Contact_BAO_Contact::exportableFields( 'All', true, true );
            foreach ($fields as $key => $var) { 
                if ( $key &&
                     ( substr($key,0, 6) !=  'custom' ) ) { //for CRM=952
                    $returnProperties[$key] = 1;
                }
            }
            
            if ( $primary ) {
                $returnProperties['location_type'   ] = 1;
                $returnProperties['im_provider'     ] = 1;
                $returnProperties['phone_type_id'   ] = 1;
                $returnProperties['provider_id'     ] = 1;
                $returnProperties['current_employer'] = 1;
            }
            
            $extraReturnProperties = array( );
            $paymentFields = false;
            
            switch ( $queryMode )  {
            case CRM_Contact_BAO_Query::MODE_EVENT :
                $paymentFields  = true;
                $paymentTableId = "participant_id";
                break;
            case CRM_Contact_BAO_Query::MODE_MEMBER :
                $paymentFields  = true;
                $paymentTableId = "membership_id";
                break;
            case CRM_Contact_BAO_Query::MODE_PLEDGE :
                require_once 'CRM/Pledge/BAO/Query.php';
                $extraReturnProperties = CRM_Pledge_BAO_Query::extraReturnProperties( $queryMode );
                $paymentFields  = true;
                $paymentTableId = "pledge_payment_id";
                break;
            case CRM_Contact_BAO_Query::MODE_CASE :
                require_once 'CRM/Case/BAO/Query.php';
                $extraReturnProperties = CRM_Case_BAO_Query::extraReturnProperties( $queryMode );
                break;
            }
            
            if ( $queryMode != CRM_Contact_BAO_Query::MODE_CONTACTS ) {
                $componentReturnProperties =& CRM_Contact_BAO_Query::defaultReturnProperties( $queryMode );
                $returnProperties          = array_merge( $returnProperties, $componentReturnProperties );
        
                if ( !empty( $extraReturnProperties ) ) {
                    $returnProperties = array_merge( $returnProperties, $extraReturnProperties );
                }
        
                // unset groups, tags, notes for components
                foreach ( array( 'groups', 'tags', 'notes' ) as $value ) {
                    unset( $returnProperties[$value] );
                }
            }
        }
        
        if ( $moreReturnProperties ) {
            $returnProperties = array_merge( $returnProperties, $moreReturnProperties );
        }

        $query =& new CRM_Contact_BAO_Query( 0, $returnProperties, null, false, false, $queryMode );

        list( $select, $from, $where ) = $query->query( );
        
        // make sure the groups stuff is included only if specifically specified
        // by the fields param (CRM-1969), else we limit the contacts outputted to only
        // ones that are part of a group
        if ( CRM_Utils_Array::value( 'groups', $returnProperties ) ) {
            $oldClause = "contact_a.id = civicrm_group_contact.contact_id";
            $newClause = " ( $oldClause AND civicrm_group_contact.status = 'Added' OR civicrm_group_contact.status IS NULL ) ";
            // total hack for export, CRM-3618
            $from = str_replace( $oldClause,
                                 $newClause,
                                 $from );
        }

        if ( $componentClause ) {
            if ( empty( $where ) ) {
                $where = "WHERE $componentClause";
            } else {
                $where .= " AND $componentClause";
            }
        }
        
        $queryString = "$select $from $where";
        
        if ( CRM_Utils_Array::value( 'tags'  , $returnProperties ) || 
             CRM_Utils_Array::value( 'groups', $returnProperties ) ||
             CRM_Utils_Array::value( 'notes' , $returnProperties ) ||
             $query->_useGroupBy ) { 
            $queryString .= " GROUP BY contact_a.id";
        }
        
        if ( $order ) {
            list( $field, $dir ) = explode( ' ', $order, 2 );
            $field = trim( $field );
            if ( CRM_Utils_Array::value( $field, $returnProperties ) ) {
                $queryString .= " ORDER BY $order";
            }
        }

        //hack for student data
        require_once 'CRM/Core/OptionGroup.php';
        $multipleSelectFields = array( 'preferred_communication_method' => 1 );
        
        if ( CRM_Core_Permission::access( 'Quest' ) ) { 
            require_once 'CRM/Quest/BAO/Student.php';
            $studentFields = array( );
            $studentFields = CRM_Quest_BAO_Student::$multipleSelectFields;
            $multipleSelectFields = array_merge( $multipleSelectFields, $studentFields );
        }
        $dao =& CRM_Core_DAO::executeQuery( $queryString, CRM_Core_DAO::$_nullArray );
        $header = false;
        
        $addPaymentHeader = false;
        if ( $paymentFields ) {
            $addPaymentHeader = true;
            //special return properties for event and members
            $paymentHeaders = array( ts('Total Amount'), ts('Contribution Status'), ts('Received Date'),
                                     ts('Payment Instrument'), ts('Transaction ID'));
            
            // get payment related in for event and members
            require_once 'CRM/Contribute/BAO/Contribution.php';
            $paymentDetails = CRM_Contribute_BAO_Contribution::getContributionDetails( $exportMode, $ids );
        }

        $componentDetails = $headerRows = array( );
        $setHeader = true;
        while ( $dao->fetch( ) ) {
            $row = array( );
            //first loop through returnproperties so that we return what is required, and in same order.
            $relationshipField = 0;
            foreach( $returnProperties as $field => $value ) {
                //we should set header only once
                if ( $setHeader ) { 
                    if ( isset( $query->_fields[$field]['title'] ) ) {
                        $headerRows[] = $query->_fields[$field]['title'];
                    } else if ($field == 'phone_type_id'){
                        $headerRows[] = 'Phone Type';
                    } else if ( $field == 'provider_id' ) { 
                        $headerRows[] = 'Im Service Provider'; 
                    } else if ( is_array( $value ) && $field == 'location' ) {
                        // fix header for location type case
                        foreach ( $value as $ltype => $val ) {
                            foreach ( array_keys($val) as $fld ) {
                                $type = explode('-', $fld );
                                $hdr = "{$ltype}-" . $query->_fields[$type[0]]['title'];
                                
                                if ( CRM_Utils_Array::value( 1, $type ) ) {
                                    if ( CRM_Utils_Array::value( 0, $type ) == 'phone' ) {
                                        $hdr .= "-" . CRM_Utils_Array::value( $type[1], $phoneTypes );
                                    } else if ( CRM_Utils_Array::value( 0, $type ) == 'im' ) {
                                        $hdr .= "-" . CRM_Utils_Array::value( $type[1], $imProviders );
                                    }
                                }
                                $headerRows[] = $hdr;
                            }
                        }
                    } else if ( substr( $field, 0, 5 ) == 'case_' ) {
                        if (  $query->_fields['case'][$field]['title'] ) {
                            $headerRows[] = $query->_fields['case'][$field]['title'];
                        } else if ( $query->_fields['activity'][$field]['title'] ){
                            $headerRows[] = $query->_fields['activity'][$field]['title'];
                        }
                    } else if ( array_key_exists( $field, $contactRelationshipTypes ) ) {
                        foreach ( $value as $relationField => $relationValue ) {
                            if ( is_array( $relationValue ) ) {
                                foreach ( $relationValue as $locType => $locValue ) {
                                    if ( $relationField == 'location' ) {
                                        foreach ( $locValue as $locKey => $dont ) {
                                            list ( $serviceProvider, $serviceProviderID ) =  explode( '-', $locKey );
                                            if ( $serviceProvider == 'phone' ) {
                                                list ( $pphone, $pphoneId ) = explode( '-', $pkkey );
                                                $headerRows[] = 
                                                    $contactRelationshipTypes[$field] .' : '. 
                                                    $serviceProvider . ' - ' . 
                                                    CRM_Utils_Array::value( $serviceProviderID, $phoneTypes );
                                            } else if ( $serviceProvider == 'im' ) {
                                                list ( $im, $imId ) = explode( '-', $ikkey );
                                                $headerRows[] = 
                                                    $contactRelationshipTypes[$field] .' : '. 
                                                    $serviceProvider . ' - ' . 
                                                    CRM_Utils_Array::value( $serviceProviderID, $imProviders );
                                            } else {
                                                $headerRows[] = $contactRelationshipTypes[$field] . ' : '. 
                                                    $locType . ' - '.
                                                    $query->_fields[$locKey]['title'];
                                            }
                                        }
                                    }
                                }

                            } else if( $query->_fields[$relationField]['title'] ) {
                                $headerRows[] = $contactRelationshipTypes[$field] . ' : ' . 
                                    $query->_fields[$relationField]['title'];
                            } else {
                                $headerRows[] = $contactRelationshipTypes[$field] . ' : ' . $relationField;
                            }
                        }
                    } else {
                        $headerRows[] = $field;
                    }
                }

                //build row values (data)
                if ( property_exists( $dao, $field ) ) {
                    $fieldValue = $dao->$field;
                    // to get phone type from phone type id
                    if ( $field == 'phone_type_id' ) {
                        $fieldValue = $phoneTypes[$fieldValue];
                    } else if ( $field == 'provider_id' ) {
                        $fieldValue = CRM_Utils_Array::value( $fieldValue , $imProviders );  
                    }
                } else {
                    $fieldValue = '';
                }
                
                if ( $field == 'id' ) {
                    $row[$field] = $dao->contact_id;
                } else if ( $field == 'pledge_balance_amount' ) { //special case for calculated field
                    $row[$field] = $dao->pledge_amount - $dao->pledge_total_paid;
                } else if ( $field == 'pledge_next_pay_amount' ) { //special case for calculated field
                    $row[$field] = $dao->pledge_next_pay_amount + $dao->pledge_outstanding_amount;
                } else if ( is_array( $value ) && $field == 'location' ) {
                    // fix header for location type case
                    foreach ( $value as $ltype => $val ) {
                        foreach ( array_keys($val) as $fld ) {
                            $type = explode('-', $fld );
                            $fldValue = "{$ltype}-" . $type[0];
                            
                            if ( CRM_Utils_Array::value( 1, $type ) ) {
                                $fldValue .= "-" . $type[1];
                            }
                            
                            $row[$fldValue] = $dao->$fldValue;
                        }
                    }
                } else if ( array_key_exists( $field, $contactRelationshipTypes ) ) {
                    require_once 'api/v2/Relationship.php';
                    require_once 'CRM/Contact/BAO/Contact.php';
                    $params['relationship_type_id'] = $contactRelationshipTypes[$field];
                    $contact_id['contact_id']       = $dao->contact_id;  
                    
                    //Get relationships
                    $val = civicrm_contact_relationship_get($contact_id,null,$params);

                    $is_valid = null ;
                    if ( $val['result'] ){
                        foreach( $val['result'] as $k => $v ){
                            $cID['contact_id'] = $v['cid'];
                            if ( $cID ) {
                                //Get Contact Details
                                $data = CRM_Contact_BAO_Contact::retrieve($cID ,$defaults );
                            }
                            $is_valid = true;
                        }
                    }

                    foreach ( $value as $relationkey => $relationvalue ) {

                        if ( $val['result'] &&  $cfID = CRM_Core_BAO_CustomField::getKeyID( $relationkey )){
                            require_once 'CRM/Core/BAO/CustomValueTable.php' ;
                            foreach ( $val['result'] as $k1 => $v1 ){
                                $contID         = $v1['cid'] ;
                                $param1         = array('entityID' => $contID,$relationkey => 1);
                                $getcustomValue = CRM_Core_BAO_CustomValueTable::getValues($param1) ;
                                $getcustomValue = $getcustomValue[$relationkey];
                                $custom_ID = CRM_Core_BAO_CustomField::getKeyID( $relationkey ) ;
                                if ( $cfID = CRM_Core_BAO_CustomField::getKeyID( $relationkey )){
                                    $custom_data = CRM_Core_BAO_CustomField::getDisplayValue($getcustomValue , $cfID, $query->_options );
                                } else {
                                    $custom_data = '';
                                }
                            }
                        }
                        
                        //Get all relationships type custom fields
                        list( $id , $atype , $btype ) = explode('_',$field);
                        $relCustomData = CRM_Core_BAO_CustomField::getFields( 'Relationship', null, null, $id, null, null );
                        require_once 'CRM/Core/BAO/CustomValueTable.php' ;
                        foreach ( $relCustomData as $id => $customdatavalue ){
                            if ( in_array( $relationkey,$customdatavalue ) ){
                                $customkey = "custom_$id" ;
                                if ( $val['result'] ){
                                    foreach ( $val['result'] as $k => $v ) {
                                        $cid   = $v['id'];
                                        $param = array( 'entityID' => $cid, $customkey => 1 );
                                        //Get custom data values
                                        $getCustomValue= CRM_Core_BAO_CustomValueTable::getValues( $param ) ;
                                        if ( !array_key_exists('error_message',$getCustomValue ) ){       
                                            $customData = $getCustomValue[$customkey] ;
                                        } else {
                                            $customData = '' ;
                                        }
                                        if ( $customData ){
                                            break;
                                        }
                                    }
                                }
                            }
                        }

                        if ( is_array( $relationvalue ) ) {
                            if ( array_key_exists ( 'location', $value ) )  {
                                foreach ( $value['location'] as $columnkey => $columnvalue ) {
                                    foreach ( $columnvalue as $colkey => $colvalue ) {

                                        list ( $serviceProvider, $serviceProviderID ) = explode( '-', $colkey );
                                        if ( in_array($serviceProvider,
                                                      array( 'street_address','supplemental_address_1',
                                                             'supplemental_address_2','city','postal_code',
                                                             'postal_code_suffix','state_province','country' )) ) {

                                            $serviceProvider = 'address';  
                                        }
                                        
                                        $output = null;
                                        foreach ( (array)$data->$serviceProvider as $datakey => $datavalue ) {
                                            
                                            if ( $locationTypes[$datavalue['location_type_id']] == $columnkey ) {
                                                if ( array_key_exists( $colkey, $datavalue ) ) {
                                                    $output = $datavalue[$colkey];
                                                } else if ( $colkey == 'country' ) {
                                                    $countryId = $datavalue['country_id'];
                                                    if ( $countryId ) {
                                                        require_once 'CRM/Core/PseudoConstant.php';
                                                        $country = & CRM_Core_PseudoConstant::country( $countryId );
                                                    } else {
                                                        $country = ''; 
                                                    }
                                                    $output = $country;
                                                } else if ( $colkey == 'state_province' ) {
                                                    $stateProvinceId = $datavalue['state_province_id'];
                                                    if ( $stateProvinceId ) {
                                                        $stateProvince = & CRM_Core_PseudoConstant::stateProvince( $stateProvinceId );
                                                    } else {
                                                        $stateProvince = ''; 
                                                    }
                                                    $output = $stateProvince;
                                                } else if ( is_numeric($serviceProviderID) ) {
                                                    if ( $serviceProvider == 'phone' ) {
                                                        if ( isset($datavalue['phone'] ) ) {
                                                            $output = $datavalue['phone'];
                                                        } else {
                                                            $output = '';
                                                        }
                                                    } else if ($serviceProvider == 'im') {
                                                        if ( isset($datavalue['name'] ) ) {
                                                            $output = $datavalue['name'];
                                                        } else {
                                                            $output = '';
                                                        }
                                                    }
                                                } else {
                                                    if ( $datavalue['location_type_id'] ) {
                                                        if ( $colkey == 'im' ) {
                                                            $output =  $datavalue['name'];
                                                        } else {
                                                            $output =  $datavalue[$colkey];
                                                        }
                                                    } else {
                                                        $output = '';
                                                    }                                                    
                                                } 
                                            }
                                        }
                                        
                                        if ( $is_valid ) {
                                            $row[] = $output;
                                        } else {
                                            $row[] = '';
                                        }
                                    }
                                }
                            }
                        } else if ( $cfID = CRM_Core_BAO_CustomField::getKeyID( $relationkey )&& $is_valid){
                            $row[] = $custom_data;
                        } else if ( $query->_fields[$relationkey]['name'] && $is_valid ) {
                            if ( ($query->_fields[$relationkey]['name'] == 'gender')  ) {
                                $getGenders = & CRM_Core_PseudoConstant::gender( );
                                $gender     = array_search($data->gender_id,array_flip($getGenders)) ;
                                $row[]      = $gender ;
                            } else if ( ($query->_fields[$relationkey]['name'] == 'greeting_type')  ) {
                                $getgreeting = & CRM_Core_PseudoConstant::greeting( );
                                $greeting = array_search($data->greeting_type_id,array_flip($getgreeting)) ;
                                $row[]    = $greeting ;
                            } else {
                                $colValue = $query->_fields[$relationkey]['name'] ;
                                $row[]    = $data->$colValue ;
                            }
                            
                        } else if ( $customData && $is_valid ) {
                            $row[] = $customData ;
                        } else {
                            $row[] = '' ;
                        }
                    }
                } else if ( isset( $fieldValue ) && $fieldValue != '' ) {
                    //check for custom data
                    if ( $cfID = CRM_Core_BAO_CustomField::getKeyID( $field ) ) {
                        $row[$field] = CRM_Core_BAO_CustomField::getDisplayValue( $fieldValue, $cfID, $query->_options );
                    } else if ( array_key_exists( $field, $multipleSelectFields ) ) {
                        //option group fixes
                        $paramsNew = array( $field => $fieldValue );
                        if ( $field == 'test_tutoring') {
                            $name = array( $field => array('newName' => $field ,'groupName' => 'test' ));
                        } else if (substr( $field, 0, 4) == 'cmr_') { //for  readers group
                            $name = array( $field => array('newName' => $field, 'groupName' => substr($field, 0, -3) ));
                        } else {
                            $name = array( $field => array('newName' => $field ,'groupName' => $field ));
                        }
                        CRM_Core_OptionGroup::lookupValues( $paramsNew, $name, false );
                        $row[$field] = $paramsNew[$field];
                    } else if ( in_array( $field , array( 'email_greeting', 'postal_greeting', 'addressee' ) ) ) {
                        //special case for greeting replacement
                        $fldValue    = "{$field}_display";
                        $row[$field] = $dao->$fldValue;
                    } else {
                        //normal fields
                        $row[$field] = $fieldValue;
                    }
                } else {
                    // if field is empty or null
                    $row[$field] = '';             
                }
            }

            //build header only once
            $setHeader = false;
        
            // add payment headers if required
            if ( $addPaymentHeader && $paymentFields ) {
                $headerRows = array_merge( $headerRows, $paymentHeaders );
                $addPaymentHeader = false;
            }

            // add payment related information
            if ( $paymentFields && isset( $paymentDetails[ $row[$paymentTableId] ] ) ) {
                $row = array_merge( $row, $paymentDetails[ $row[$paymentTableId] ] );
            }

            //remove organization name for individuals if it is set for current employer
            if ( CRM_Utils_Array::value('contact_type', $row ) && $row['contact_type'] == 'Individual' ) {
                $row['organization_name'] = '';
            }
            
            // add component info
            $componentDetails[] = $row;         
        }
        
        require_once 'CRM/Core/Report/Excel.php';
        CRM_Core_Report_Excel::writeCSVFile( self::getExportFileName( 'csv', $exportMode ), $headerRows, $componentDetails );
        exit();
    }

    /**
     * name of the export file based on mode
     *
     * @param string $output type of output
     * @param int    $mode export mode
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv', $mode = CRM_Export_Form_Select::CONTACT_EXPORT ) 
    {
        switch ( $mode ) {
        case CRM_Export_Form_Select::CONTACT_EXPORT : 
            return ts('CiviCRM Contact Search');
            
        case CRM_Export_Form_Select::CONTRIBUTE_EXPORT : 
            return ts('CiviCRM Contribution Search');
            
        case CRM_Export_Form_Select::MEMBER_EXPORT : 
            return ts('CiviCRM Member Search');
            
        case CRM_Export_Form_Select::EVENT_EXPORT : 
            return ts('CiviCRM Participant Search');

        case CRM_Export_Form_Select::PLEDGE_EXPORT : 
            return ts('CiviCRM Pledge Search');
        case CRM_Export_Form_Select::CASE_EXPORT : 
            return ts('CiviCRM Case Search');
        }
    }


    /**
     * handle the export case. this is a hack, so please fix soon
     *
     * @param $args array this array contains the arguments of the url
     *
     * @static
     * @access public
     */
    static function invoke( $args ) 
    {
        // FIXME:  2005-06-22 15:17:33 by Brian McFee <brmcfee@gmail.com>
        // This function is a dirty, dirty hack.  It should live in its own
        // file.
        $session =& CRM_Core_Session::singleton();
        $type = $_GET['type'];
        
        if ($type == 1) {
            $varName = 'errors';
            $saveFileName = 'Import_Errors.csv';
        } else if ($type == 2) {
            $varName = 'conflicts';
            $saveFileName = 'Import_Conflicts.csv';
        } else if ($type == 3) {
            $varName = 'duplicates';
            $saveFileName = 'Import_Duplicates.csv';
        } else if ($type == 4) {
            $varName = 'mismatch';
            $saveFileName = 'Import_Mismatch.csv';
        } else if ($type == 5) {
            $varName = 'pledgePaymentErrors';
            $saveFileName = 'Import_Pledge_Payment_Errors.csv';
        } else if ($type == 6) {
            $varName = 'softCreditErrors';
            $saveFileName = 'Import_Soft_Credit_Errors.csv';
        } else {
            /* FIXME we should have an error here */
            return;
        }
        
        // FIXME: a hack until we have common import
        // mechanisms for contacts and contributions
        $realm = CRM_Utils_Array::value('realm',$_GET);
        if ($realm == 'contribution') {
            $controller = 'CRM_Contribute_Import_Controller';
        } else if ( $realm == 'membership' ) {
            $controller = 'CRM_Member_Import_Controller';
        } else if ( $realm == 'event' ) {
            $controller = 'CRM_Event_Import_Controller';
        } else if ( $realm == 'activity' ) {
            $controller = 'CRM_Activity_Import_Controller';
        } else {
            $controller = 'CRM_Import_Controller';
        }
        
        require_once 'CRM/Core/Key.php';
        $qfKey = CRM_Core_Key::get( $controller );
        
        $fileName = $session->get($varName . 'FileName', "{$controller}_{$qfKey}");
        
        $config =& CRM_Core_Config::singleton( ); 
        
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Length: ' . filesize($fileName) );
        header('Content-Disposition: attachment; filename=' . $saveFileName);
        
        readfile($fileName);
        
        exit();
    }

    function exportCustom( $customSearchClass, $formValues, $order ) 
    {
        require_once( str_replace( '_', DIRECTORY_SEPARATOR, $customSearchClass ) . '.php' );
        eval( '$search = new ' . $customSearchClass . '( $formValues );' );
      
        $includeContactIDs = false;
        if ( $formValues['radio_ts'] == 'ts_sel' ) {
            $includeContactIDs = true;
        }

        $sql    = $search->all( 0, 0, $order, $includeContactIDs );

        $columns = $search->columns( );

        $header = array_keys  ( $columns );
        $fields = array_values( $columns );

        $rows = array( );
        $dao =& CRM_Core_DAO::executeQuery( $sql,
                                            CRM_Core_DAO::$_nullArray );
        $alterRow = false;
        if ( method_exists( $search, 'alterRow' ) ) {
            $alterRow = true;
        }
        while ( $dao->fetch( ) ) {
            $row = array( );

            foreach ( $fields as $field ) {
                $row[$field] = $dao->$field;
            }
            if ( $alterRow ) {
                $search->alterRow( $row );
            }
            $rows[] = $row;
        }

        require_once 'CRM/Core/Report/Excel.php';
        CRM_Core_Report_Excel::writeCSVFile( self::getExportFileName( ), $header, $rows );
        exit();
    }
}

