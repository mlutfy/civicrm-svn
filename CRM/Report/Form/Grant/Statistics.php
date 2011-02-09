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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Report/Form.php';
require_once 'CRM/Grant/PseudoConstant.php';
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Contact/BAO/ContactType.php';
class CRM_Report_Form_Grant_Statistics extends CRM_Report_Form {
    
    protected $_addressField = false;

    protected $_customGroupExtends = array( 'Grant' );

    protected $_report = 'Grant';

    function __construct( ) 
    {
        $this->_columns = 
            array( 
                  'civicrm_grant'  =>
                  array( 'dao'     => 'CRM_Grant_DAO_Grant',
                         'fields'  =>
                         array( 'summary_statistics' => 
                                array( 'name'          => 'id', 
                                       'title'         => ts( 'Summary Statistics' ),
                                       'required'      => true,
                                       ),
                                'grant_type_id' =>
                                array( 'name'          => 'grant_type_id' ,
                                       'title'         => ts( 'By Grant Type' ),
                                       ),
                                'status_id' =>
                                array( 'no_display'    => true,
                                       'required'      => true, 
                                       ),
                                'amount_total' => 
                                array( 'no_display'    => true,
                                       'required'      => true, 
                                       ),
                                'grant_report_received' => 
                                array( 'no_display'    => true,
                                       'required'      => true, 
                                       ),
                                'currency' =>
                                array( 'no_display'    => true,
                                       'required'      => true, 
                                       ),
                                ),  
                         'filters' =>             
                         array( 'application_received_date' =>
                                array( 'name'          => 'application_received_date' ,
                                       'title'         => ts( 'Application Received' ),
                                       'operatorType'  => CRM_Report_Form::OP_DATE,
                                       'type'          => CRM_Utils_Type::T_DATE,
                                       ),
                                'decision_date'=>
                                array( 'name'          => 'decision_date' ,
                                       'title'         => ts( 'Grant Decision' ),
                                       'operatorType'  => CRM_Report_Form::OP_DATE,
                                       'type'          => CRM_Utils_Type::T_DATE,
                                       ),
                                'money_transfer_date' =>
                                array( 'name'          => 'money_transfer_date' ,
                                       'title'         => ts( 'Money Transferred' ),
                                       'operatorType'  => CRM_Report_Form::OP_DATE,
                                       'type'          => CRM_Utils_Type::T_DATE,
                                       ),
                                'grant_due_date' =>
                                array( 'name'          => 'grant_due_date' ,
                                       'title'         => ts( 'Grant Report Due' ),
                                       'operatorType'  => CRM_Report_Form::OP_DATE,
                                       'type'          => CRM_Utils_Type::T_DATE,
                                       ),
                                'grant_type'     => 
                                array( 'name'          => 'grant_type_id' ,
                                       'title'         => ts( 'Grant Type' ),
                                       'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                       'options'       => CRM_Grant_PseudoConstant::grantType( ),
                                       ),
                                'status_id' => 
                                array( 'name'          => 'status_id',
                                       'title'         => ts('Grant Status' ),
                                       'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                       'options'       => CRM_Grant_PseudoConstant::grantStatus( ),
                                       ),
                                'amount_requested' =>
                                array( 'name'          => 'amount_requested' ,
                                       'title'         => ts( 'Amount Requested' ),
                                       'type'          => CRM_Utils_Type::T_MONEY,
                                       ),
                                'amount_granted' =>
                                array( 'name'          => 'amount_granted',
                                       'title'         => ts( 'Amount Granted' ),
                                       ),
                                'grant_report_received'=>
                                array( 'name'          => 'grant_report_received',
                                       'title'         => ts( 'Report Received' ), 
                                       'operatorType'  => CRM_Report_Form::OP_SELECT,
                                       'options'       => array( '' => ts('- select -'), 
                                                                 0  => ts('No'), 
                                                                 1  => ts('Yes'),
                                                                 ),
                                       ),
                                ),
                         ),
                 'civicrm_contact'=>
                 array( 'dao'     => 'CRM_Contact_DAO_Contact',
                        'fields'  =>
                            array( 'id' =>
                                   array( 'required'      => true,  
                                          'no_display'    => true,
                                          ), 
                                   'gender_id' =>  
                                   array( 'name'          => 'gender_id',
                                          'title'         => ts( 'By Gender' ),
                                          ),
                                   'contact_type' =>
                                   array( 'name'          => 'contact_type',
                                          'title'         => ts( 'By Contact Type' ),
                                          ),
                                   ),
                        'filters' =>
                        array(
                            'gender_id' => 
                            array( 'name'          => 'gender_id',
                                   'title'         => ts('Gender' ),
                                   'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                   'options'       => CRM_Core_PseudoConstant::gender( ),
                                   ),
                            'contact_type' => 
                            array( 'name'          => 'contact_type',
                                   'title'         => ts('Contact Type' ),
                                   'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                   'options'       => CRM_Contact_BAO_ContactType::basicTypePairs( ),
                                   ),
                            ),
                        'grouping'=> 'contact-fields',
                  ),
                  'civicrm_world_region' => 
                  array( 'dao'    => 'CRM_Core_DAO_Worldregion',
                         'fields' => 
                         array( 'id'       =>
                                array( 'no_display'    => true,
                                       ),
                                'name'      => 
                                array( 'name'          => 'name',
                                       'title'         => ts( 'By World Region' ),
                                       ),
                                ),
                         'filters' =>
                         array( 'region_id' =>
                                array( 'name'          => 'id', 
                                       'title'         => ts( 'World Region' ),
                                       'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                       'options'       => CRM_Core_PseudoConstant::worldRegion( ),
                                       ),
                                ),
                         ),
                  'civicrm_address' =>
                  array( 'dao' => 'CRM_Core_DAO_Address',
                         'fields' =>
                         array( 'country_id' => 
                                array( 'name'          => 'country_id',
                                       'title'         => ts( 'By Country' ), 
                                       ),
                                ),
                         'filters' =>             
                         array( 'country_id' => 
                                array( 'title'         => ts( 'Country' ), 
                                       'operatorType'  => CRM_Report_Form::OP_MULTISELECT,
                                       'options'       => CRM_Core_PseudoConstant::country( ),
                                       ), 
                                ),
                         ),
                   ); 
        parent::__construct( );
    }

    function select( ) 
    {
        $select = array( );
        
        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( in_array( $tableName, array( 'civicrm_address', 'civicrm_world_region' ) ) ) {
                $this->_addressField = true;
            }

            if ( array_key_exists('fields', $table) ) { 
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        
                        $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                        
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['type']  = CRM_Utils_Array::value( 'type', $field );
                    }
                }
            }
        }
        
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function from( ) 
    {
        $this->_from = "
        FROM civicrm_grant {$this->_aliases['civicrm_grant']}
                        LEFT JOIN civicrm_contact {$this->_aliases['civicrm_contact']} 
                    ON ({$this->_aliases['civicrm_grant']}.contact_id  = {$this->_aliases['civicrm_contact']}.id  ) ";
        if ( $this->_addressField ) {
            $this->_from .= "
                  LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
                         ON {$this->_aliases['civicrm_contact']}.id = 
                            {$this->_aliases['civicrm_address']}.contact_id AND 
                            {$this->_aliases['civicrm_address']}.is_primary = 1\n
                  LEFT JOIN civicrm_country country
                         ON {$this->_aliases['civicrm_address']}.country_id = 
                            country.id
                  LEFT JOIN civicrm_worldregion {$this->_aliases['civicrm_world_region']}
                         ON country.region_id = 
                            {$this->_aliases['civicrm_world_region']}.id";
        } 
    }

    function where( ) 
    {
        $granted = array_search( 'Granted', CRM_Grant_PseudoConstant::grantStatus( ) );
        $whereClause  = " 
WHERE {$this->_aliases['civicrm_grant']}.amount_total IS NOT NULL 
  AND {$this->_aliases['civicrm_grant']}.amount_total > 0";
        $this->_where = $whereClause . " AND {$this->_aliases['civicrm_grant']}.status_id = {$granted} ";

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) { 
                foreach ( $table['filters'] as $fieldName => $field ) {
                    
                    $clause = null;
                    if ( CRM_Utils_Array::value( 'type', $field ) & CRM_Utils_Type::T_DATE ) {
                        $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );
                        
                        if ( $relative || $from || $to ) {
                            $clause = $this->dateClause( $field['name'], $relative, $from, $to, $field['type'] );
                        }
                    } else { 
                        $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                        if ( ( $fieldName == 'grant_report_received' ) && 
                             ( CRM_Utils_Array::value( "{$fieldName}_value", $this->_params ) === 0 ) ) {
                            $op = 'nll';
                            $this->_params["{$fieldName}_value"] = NULL;
                        }
                        if ( $op ) {
                            $clause = 
                                $this->whereClause( $field,
                                                    $op,
                                                    CRM_Utils_Array::value( "{$fieldName}_value", $this->_params ),
                                                    CRM_Utils_Array::value( "{$fieldName}_min", $this->_params ),
                                                    CRM_Utils_Array::value( "{$fieldName}_max", $this->_params ) );
                        }
                    }
                    if ( ! empty( $clause ) ) {
                        $clauses[] = $clause;
                        $this->_where .= " AND " . implode( ' AND ', $clauses );
                        $this->_whereClause = $whereClause . " AND " . implode( ' AND ', $clauses );
                    }
                }
            }
        }
    }

    function groupBy( ) 
    {
        $this->_groupBy = '';
        
        if ( CRM_Utils_Array::value( 'fields', $this->_params ) &&
             is_array( $this->_params['fields'] ) &&
             !empty( $this->_params['fields'] ) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists( 'fields', $table ) ) {
                    foreach ( $table['fields'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                            $this->_groupBy[] = $field['dbAlias'];
                        }
                    }
                }
            }
        } 
        if ( !empty( $this->_groupBy ) ) {
            $this->_groupBy = " GROUP BY " . implode( ', ', $this->_groupBy );
        } 
    }

    function alterDisplay( &$rows ) 
    {
        $totalStatistics = parent::statistics( $rows );
        $awardedGrantsAmount = $grantsReceived = $totalAmount = $awardedGrants = $grantReportsReceived = 0;
        
        $grantTypes      = CRM_Grant_PseudoConstant::grantType( );
        $countries       = CRM_Core_PseudoConstant::country( );
        $gender          = CRM_Core_PseudoConstant::gender( );

        $query = $sql = "
SELECT COUNT({$this->_aliases['civicrm_grant']}.id) as count , 
         SUM({$this->_aliases['civicrm_grant']}.amount_total) as totalAmount 
  {$this->_from} ";
        
        $query .= " {$this->_whereClause}";
        $result = CRM_Core_DAO::executeQuery( $query );
        while ( $result->fetch( ) ) {
            $grantsReceived = $result->count;
            $totalAmount    = $result->totalAmount;
        }
        
        $sql   .= " {$this->_where}";
        $values = CRM_Core_DAO::executeQuery( $sql );
        while ( $values->fetch( ) ) {
            $awardedGrants       = $values->count;
            $awardedGrantsAmount = $values->totalAmount;
        }
        
        foreach ( $rows as $key => $values ) {
            if ( CRM_Utils_Array::value( 'civicrm_grant_grant_report_received', $values ) ) {
                $grantReportsReceived ++;
            }
            
            if ( CRM_Utils_Array::value( 'civicrm_grant_grant_type_id', $values ) ) {
                $grantType = CRM_Utils_Array::value( $values['civicrm_grant_grant_type_id'], $grantTypes );
                $grantStatistics['civicrm_grant_grant_type_id']['title'] = ts( 'By Grant Type' );
                self::getStatistics( $grantStatistics['civicrm_grant_grant_type_id'], $grantType, $values, 
                                     $awardedGrants, $awardedGrantsAmount );
            }
            
            if ( array_key_exists( 'civicrm_address_country_id', $values ) ) {
                $country = CRM_Utils_Array::value( $values['civicrm_address_country_id'], $countries );
                $country = ( $country ) ? $country : 'Unassigned';
                $grantStatistics['civicrm_address_country_id']['title'] = ts( 'By Country' );
                self::getStatistics( $grantStatistics['civicrm_address_country_id'], $country, $values, 
                                     $awardedGrants, $awardedGrantsAmount );
            }
            
            if ( array_key_exists( 'civicrm_world_region_name', $values ) ) {
                $region = CRM_Utils_Array::value( 'civicrm_world_region_name', $values );
                $region = ( $region ) ? $region : 'Unassigned';
                $grantStatistics['civicrm_world_region_name']['title'] = ts( 'By Region' );
                self::getStatistics( $grantStatistics['civicrm_world_region_name'], $region, $values, 
                                     $awardedGrants, $awardedGrantsAmount );
            }
            
            if ( $type = CRM_Utils_Array::value( 'civicrm_contact_contact_type', $values ) ) {
                $grantStatistics['civicrm_contact_contact_type']['title'] = ts( 'By Contact Type' );
                $title = "Total Number of {$type}(s)";
                self::getStatistics( $grantStatistics['civicrm_contact_contact_type'], $title, $values, 
                                     $awardedGrants, $awardedGrantsAmount );
            }
            
            if ( array_key_exists( 'civicrm_contact_gender_id', $values ) ) {
                $genderLabel = CRM_Utils_Array::value( $values['civicrm_contact_gender_id'], $gender );
                $genderLabel = ( $genderLabel ) ? $genderLabel : 'Unassigned';
                $grantStatistics['civicrm_contact_gender_id']['title'] = ts( 'By Gender' );
                self::getStatistics( $grantStatistics['civicrm_contact_gender_id'], $genderLabel, $values, 
                                     $awardedGrants, $awardedGrantsAmount );
            }
            
            foreach ( $values as $customField => $customValue ) {
                if ( strstr( $customField, 'civicrm_value_' ) ) {
                    $customFieldTitle  = CRM_Utils_Array::value( 'title', $this->_columnHeaders[$customField] );
                    $customGroupTitle  = explode( '_custom', strstr( $customField, 'civicrm_value_' ) );
                    $customGroupTitle  = $this->_columns[$customGroupTitle[0]]['group_title'];
                    $grantStatistics[$customGroupTitle]['title'] = ts( "By {$customGroupTitle}" );
                    
                    $customData = ( $customValue ) ? false : true;
                    self::getStatistics( $grantStatistics[$customGroupTitle], $customFieldTitle, $values, 
                                         $awardedGrants, $awardedGrantsAmount, $customData );
                }
            }
        }
        
        $totalStatistics['total_statistics'] = 
            array( 'grants_received'        => array( 'title'  => 'Grant Requests Received',
                                                      'count'  => $grantsReceived,
                                                      'amount' => $totalAmount ),
                   'grants_awarded'         => array( 'title'  => 'Grants Awarded',
                                                      'count'  => $awardedGrants,
                                                      'amount' => $awardedGrantsAmount ),
                   'grants_report_received' => array( 'title'  => 'Grant Reports Received',
                                                      'count'  => $grantReportsReceived ), );
        
        $this->assign( 'totalStatistics', $totalStatistics );
        $this->assign( 'grantStatistics', $grantStatistics );
        
        if ( $this->_outputMode == 'csv' || 
             $this->_outputMode == 'pdf' ) {
            $this->_columnHeaders = array( 'civicrm_grant_total_grants' => array( 'title' => 'Summary', ),
                                           'civicrm_grant_count'        => array( 'title' => 'Count', ),
                                           'civicrm_grant_amount'       => array( 'title' => 'Amount', ),
                                           );
            foreach ( $totalStatistics['total_statistics'] as $title => $value ) {
                $row[]= array( 'civicrm_grant_total_grants' => $value['title'],
                               'civicrm_grant_count'        => $value['count'],
                               'civicrm_grant_amount'       => $value['amount'] );
            }
            $row[] = $totalAmount = array( );
            foreach ( $grantStatistics as $key => $value ) {
                $row[] = array( 'civicrm_grant_total_grants' => $value['title'],
                                'civicrm_grant_count'        => 'Number of Grants (%)',
                                'civicrm_grant_amount'       => 'Total Amount (%)' );
                
                foreach ( $value['value'] as $field => $values ) {
                    foreach ( $values['currency'] as $currency => $amount ) {
                        $totalAmount[$currency] = $currency . $amount['value'] . "({$values['percentage']}%)";
                    }
                    $totalAmt = implode( ', ', $totalAmount );
                    $count = $values['count'] . " ({$values['percentage']}%)";
                    $row[] = array( 'civicrm_grant_total_grants' => $field,
                                    'civicrm_grant_count'        => $count,
                                    'civicrm_grant_amount'       => $totalAmt );
                }
                $row[] = array( );
            }
            $rows = $row;
        }
    }

    static function getStatistics( &$grantStatistics, $fieldValue, $values, 
                                   $awardedGrants, $awardedGrantsAmount, $customData = false )
    {
        $currencies = CRM_Core_PseudoConstant::currencySymbols( 'symbol', 'name' );
        $currency   = $currencies[$values['civicrm_grant_currency']];
        
        if ( !$customData ) {
            $grantStatistics['value'][$fieldValue]['currency'][$currency]['value'] += $values['civicrm_grant_amount_total'];
            $grantStatistics['value'][$fieldValue]['currency'][$currency]['percentage'] =
                round( ( $grantStatistics['value'][$fieldValue]['currency'][$currency]['value'] / $awardedGrantsAmount ) * 100 );
            $grantStatistics['value'][$fieldValue]['count'] ++;
            $grantStatistics['value'][$fieldValue]['percentage'] = 
                round( ( $grantStatistics['value'][$fieldValue]['count'] / $awardedGrants ) * 100 );
        } else {
            $grantStatistics['value'][$fieldValue]['unassigned_currency'][$currency]['value'] += $values['civicrm_grant_amount_total'];
            $grantStatistics['value'][$fieldValue]['unassigned_currency'][$currency]['percentage'] =
                round( ( $grantStatistics['value'][$fieldValue]['unassigned_currency'][$currency]['value'] / $awardedGrantsAmount ) * 100 );
            $grantStatistics['value'][$fieldValue]['unassigned_count'] ++;
            $grantStatistics['value'][$fieldValue]['unassigned_percentage'] = 
                round( ( $grantStatistics['value'][$fieldValue]['unassigned_count'] / $awardedGrants ) * 100 );
        }
    }
}