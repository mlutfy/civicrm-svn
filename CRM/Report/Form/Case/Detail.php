<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.2                                                |
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
require_once 'CRM/Case/BAO/Case.php';
require_once 'CRM/Case/PseudoConstant.php';

class CRM_Report_Form_Case_Detail extends CRM_Report_Form {
    
    protected $_relField     = false;
    
    protected $_addressField = false;
    
    protected $_emailField   = false;
    
    protected $_phoneField   = false;
    
    
    function __construct( ) {
    	$this->case_statuses = CRM_Case_PseudoConstant::caseStatus();
        $this->case_types = CRM_Case_PseudoConstant::caseType();
        $rels = CRM_Core_PseudoConstant::relationshipType();
     	foreach ($rels as $relid => $v) {
     		$this->rel_types[$relid] = $v['label_b_a'];
        }
        
        $this->_columns = 
            array( 
                  
                  'civicrm_case' =>
                  array( 'dao'       => 'CRM_Case_DAO_Case',
                         'fields'    =>
                         array( 
                               'id'        => 
                               array( 'title'     => ts('Case ID'),
                                      'no_display'=> true,
                                      'required'  => true,
                                      ),
                               'subject'   => 
                               array( 'title' => ts('Subject'),
                                      'required'=> true,
                                      ),   
                               'start_date' => 
                               array( 'title' => ts('Start Date'), ),
                               'end_date' => 
                               array( 'title' => ts('End Date'), ),
                               'status_id' => 
                               array( 'title' => ts('Case Status'),  ),
                               'case_type_id'=> 
                               array('title' => ts('Case Type'),) 
                               
                                ),
                         'filters'   =>  
                         array( 
                               'start_date' => 
                               array( 'title' => ts( 'Start Date' ),
                                      'operatorType' => CRM_Report_Form::OP_DATE,
                                      ),
                               'end_date' => 
                               array( 'title' => ts( 'End Date' ),
                                      'operatorType' => CRM_Report_Form::OP_DATE,
                                      ),
                               'status_id' => 
                               array( 'title' => ts( 'Case Status' ),
                                      'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                      'options' => $this->case_statuses,
                                      ),
                               'case_type_id'=> 
                               array('title' => ts('Case Type'),
                                     'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                     'options' => $this->case_types),
                                ),
                         ),
                  'civicrm_contact' =>
                  array( 'dao'       => 'CRM_Contact_DAO_Contact',
                         'fields'    =>
                         array( 
                               'display_name'           => 
                               array( 'title' => ts('Client Name'),
                                      'required'  => true, ),
                               'id'           => 
                               array( 'no_display'=> true,
                                      'required'  => true, ),
                                ),
                         'filters'   =>  
                         array( 
                               'display_name' => 
                               array( 'title' => ts( 'Client Name' ),
                                      ),
                                ),
                         ),
                  
                  'civicrm_relationship' =>
                  array( 'dao'       => 'CRM_Contact_DAO_Relationship',
                         'fields'    =>
                         array( 'relationship_type_id'           => 
                                array( 'title' => ts('Case Role'),
                                       ),
                                ),
                         'filters'   =>  
                         array( 'relationship_type_id' => 
                                array( 'title' => ts( 'Case Role' ),
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options' => $this->rel_types,
                                       
                                       ),
                                ),
                         ),
                  'civicrm_relationship_type' =>
                  array( 'dao'       => 'CRM_Contact_DAO_RelationshipType',
                         
                         ),
                  'civicrm_email'   =>
                  array( 'dao'       => 'CRM_Core_DAO_Email',
                         'fields'    =>
                         array( 'email' => 
                                array( 'title'      => ts( 'Email' ),
                                       'no_repeat'  => true
                                       ),  ),
                         'grouping'      => 'contact-fields',
                         ),
                  
                  'civicrm_phone'   =>
                  array( 'dao'       => 'CRM_Core_DAO_Phone',
                         'fields'    =>
                         array( 'phone' => 
                                array( 'title'      => ts( 'Phone' ),
                                       'no_repeat'  => true
                                       ), ),
                         'grouping'      => 'contact-fields',
                         ),
                  
                  'civicrm_address' =>
                  array( 'dao' => 'CRM_Core_DAO_Address',
                         'fields' =>
                         array( 'street_address'    => null,
                                'state_province_id' => 
                                array( 'title'   => ts( 'State/Province' ), ),
                                'country_id'        => 
                                array( 'title'   => ts( 'Country' ),  
                                       ), ),
                         'grouping'=> 'contact-fields',
                         'filters' =>             
                         array( 'country_id' => 
                                array( 'title'        => ts( 'Country' ), 
                                       'type'         => CRM_Utils_Type::T_INT,
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => CRM_Core_PseudoConstant::country( ),), 
                                'state_province_id' => 
                                array( 'title'        => ts( 'State/Province' ), 
                                       'type'         => CRM_Utils_Type::T_INT,
                                       'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                       'options'      => CRM_Core_PseudoConstant::stateProvince( ),), ),
                         ),
                  'civicrm_case_contact' =>
                  array( 'dao'       => 'CRM_Case_DAO_CaseContact',
                         ),
                   );
        
        parent::__construct( );
    }
    
    function preProcess( ) {
        parent::preProcess( );
    }
    
    function select( ) {
        $select = array( );
        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) { 
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( $tableName == 'civicrm_address' ) {
                        $this->_addressField = true;
                    }elseif ( $tableName == 'civicrm_relationship' ) {
                        $this->_relField = true;
                    }
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        if ( $tableName == 'civicrm_email' ) {
                            $this->_emailField = true;
                        }elseif ( $tableName == 'civicrm_phone' ) {
                            $this->_phoneField = true;
                        }
                        $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['type']  = CRM_Utils_Array::value( 'type', $field );
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                    }
                }
            }
            
        }
        
        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }
    
    
    function from( ) {
        
        $cc = $this->_aliases['civicrm_case'];
        $c = $this->_aliases['civicrm_contact'];
        $cr = $this->_aliases['civicrm_relationship'];
        $crt = $this->_aliases['civicrm_relationship_type'];
        $ccc = $this->_aliases['civicrm_case_contact'];
        
        $this->_from = "
             FROM civicrm_case $cc
 inner join civicrm_case_contact $ccc on ${ccc}.case_id = ${cc}.id
 inner join civicrm_contact $c on ${c}.id=${ccc}.contact_id
 ";
        if ( $this->_relField ) {
            $this->_from = "
             FROM civicrm_contact $c 
 inner join civicrm_relationship $cr on {$c}.id = ${cr}.contact_id_a
 inner join civicrm_case $cc on ${cc}.id = ${cr}.case_id
 inner join civicrm_relationship_type $crt on ${crt}.id=${cr}.relationship_type_id
 inner join civicrm_case_contact $ccc on ${ccc}.case_id = ${cc}.id
 ";
        } 
         
        if( $this->_addressField ) {
            $this->_from .= "
             LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
                    ON $c.id = {$this->_aliases['civicrm_address']}.contact_id AND 
                       {$this->_aliases['civicrm_address']}.is_primary = 1\n";
        } 
        if ( $this->_emailField ) {
            $this->_from .= " 
             LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']} 
                   ON $c.id = {$this->_aliases['civicrm_email']}.contact_id AND 
                       {$this->_aliases['civicrm_email']}.is_primary = 1\n";  
            
        }
        if( $this->_phoneField ) {
            $this->_from .= "
             LEFT JOIN  civicrm_phone {$this->_aliases['civicrm_phone']} 
                       ON ($c.id = {$this->_aliases['civicrm_phone']}.contact_id AND 
                          {$this->_aliases['civicrm_phone']}.is_primary = 1)";
            
        }
        
    }
    
    function where( ) {
        $clauses = array( );
        $this->_having = '';
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    
                    if ( $field['operatorType'] & CRM_Report_Form::OP_DATE ) {
                        $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );
                        
                        $clause = $this->dateClause( $field['dbAlias'], $relative, $from, $to );
                    } else {
                        
                        $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                        if( $fieldName =="case_type_id" ) {
                            foreach( $this->_params['case_type_id_value'] as $key =>$value ) {
                                $value = CRM_Case_BAO_Case::VALUE_SEPERATOR.$value .CRM_Case_BAO_Case::VALUE_SEPERATOR;
                                $this->_params['case_type_id_value'][$key]  = "'{$value}'";
                            }
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
                    }
                }
            }
        }
        
        if ( empty( $clauses ) ) {
            $this->_where = "WHERE ( 1 ) ";
        } else {
            $this->_where = "WHERE " . implode( ' AND ', $clauses );
        }
    }
    
    function groupBy( ) {
        $this->_groupBy = "";
    }
    
    function statistics( &$rows ) {
        $statistics = parent::statistics( $rows );
        
        $select = "select COUNT( DISTINCT( {$this->_aliases['civicrm_address']}.country_id))";
        $sql    = "{$select} {$this->_from} {$this->_where}";
        $count  = CRM_Core_DAO::singleValueQuery( $sql );
        
        //CaseType statistics
        if ( array_key_exists('filters', $statistics) ) {
            foreach( $statistics['filters'] as $id => $value ) {
                if( $value['title'] == 'Case Type') {
                    $statistics['filters'][$id]['value'] = 'Is '.$this->case_types[ substr( $statistics['filters'][$id]
                                                                                            ['value'], -3,-2 ) ];          
                }
            }
        }
        $statistics['counts']['case']    = array( 
                                                 'title' => ts( 'Total Number of Cases ' ),
                                                 'value' => count( $rows ) );
        $statistics['counts']['country'] = array( 
                                                 'title' => ts( 'Total Number of Countries ' ),
                                                 'value' => $count );
        
        return $statistics;
    }
    
    function postProcess( ) {
        
        $this->beginPostProcess( );

        $sql  = $this->buildQuery( true );
        
        
        $rows = $graphRows = array();
        $this->buildRows ( $sql, $rows );
        
        $this->formatDisplay( $rows );
        $this->doTemplateAssignment( $rows );
        $this->endPostProcess( $rows );	
    }
    
    function alterDisplay( &$rows ) {
        $entryFound = false;
        
        foreach ( $rows as $rowNum => $row ) { 
            if ( array_key_exists('civicrm_case_status_id', $row ) ) { 
                if ( $value = $row['civicrm_case_status_id'] ) {
                    $rows[$rowNum]['civicrm_case_status_id'] = $this->case_statuses[$value];
                    
                    $entryFound = true;
                }
            }
            if ( array_key_exists('civicrm_case_case_type_id', $row ) ) {
                if ( $value = str_replace( CRM_Case_BAO_Case::VALUE_SEPERATOR, "", $row['civicrm_case_case_type_id'] )) {
                    $rows[$rowNum]['civicrm_case_case_type_id'] = $this->case_types[$value];
                    
                    $entryFound = true;
                }
            }
            if ( array_key_exists('civicrm_case_subject', $row ) ) {
                if ( $value = $row['civicrm_case_subject'] ) {
                    $caseId    = $row['civicrm_case_id'];
                    $contactId = $row['civicrm_contact_id'];
                    $rows[$rowNum]['civicrm_case_subject'] = "<a href= 'javascript:viewCase( $caseId,$contactId );'>$value</a>";
                    $rows[$rowNum]['civicrm_case_subject_hover'] = ts("View Details of Case.");
                    
                    $entryFound = true;
                }
            }
            if ( array_key_exists('civicrm_relationship_relationship_type_id', $row ) ) {
                if ( $value = $row['civicrm_relationship_relationship_type_id'] ) {
                    $rows[$rowNum]['civicrm_relationship_relationship_type_id'] = 
                        CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_RelationshipType', $value,'label_b_a' );
                    
                    $entryFound = true;
                }
            }
            if ( array_key_exists('civicrm_address_country_id', $row) ) {
                if ( $value = $row['civicrm_address_country_id'] ) {
                    $rows[$rowNum]['civicrm_address_country_id'] = CRM_Core_PseudoConstant::country( $value, false );
                }
                $entryFound = true;
            }
            if ( array_key_exists('civicrm_address_state_province_id', $row) ) {
                if ( $value = $row['civicrm_address_state_province_id'] ) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = CRM_Core_PseudoConstant::stateProvince( $value, false );
                }
                $entryFound = true;
            }
            if ( !$entryFound ) {
                break;
            }
        }
    }
}
