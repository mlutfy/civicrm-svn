<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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

require_once 'CRM/Report/Form.php';

class CRM_Report_Form_Walklist extends CRM_Report_Form {
    protected $_addressField = false;

    protected $_emailField = false;
    
    protected $_phoneField = false;

    protected $_summary = null;

    function __construct( ) {
        $this->_columns = 
            array( 'civicrm_contact'      =>
                   array( 'dao'     => 'CRM_Contact_DAO_Contact',
                          'fields'  =>
                          array( 'id'           => 
                                 array( 'title' => ts( 'Contact ID' ),
                                        'required'  => true, ),  
                                'display_name' => 
                                 array( 'title' => ts( 'Contact Name' ),
                                        'required'  => true,
                                        'no_repeat' => true ),
                                 ),
                          'filters' =>             
                          array('sort_name'    => 
                                array( 'title'      => ts( 'Contact Name' ),
                                       'operator'   => 'like' ) ),
                          'grouping'=> 'contact-fields',
                          'order_bys'=>             
                          array( 'display_name' => array( 'title' => ts( 'Contact Name' ),
                                                          'required'  => true ) ),
                          ),
                   
                   'civicrm_address' =>
                   array( 'dao' => 'CRM_Core_DAO_Address',
                          'fields' =>
                          array(
                                'street_number'     => array( 'title' => ts( 'Street Number' ),
                                                              'type'  => 1 ),
                                'street_address'    => null,
                                'city'              => null,
                                'postal_code'       => null,
                                'state_province_id' => 
                                array( 'title'   => ts( 'State/Province' ), 
                                       'default' => true ),
                                'country_id'        => 
                                array( 'title' => ts( 'Country' ), ), ),
                          'filters' =>             
                          array( 
                                'street_number'  => array( 'title' => ts( 'Street Number' ),
                                                           'type'  => 1,
                                                           'name'  => 'street_number' ),
                                'street_address' => null,
                                'city'           => null,
                                 ),
                          'grouping'=> 'location-fields',
                          ),

                   'civicrm_email' => 
                   array( 'dao' => 'CRM_Core_DAO_Email',
                          'fields' =>
                          array( 'email' => array( 'default'=> true )),
                          'grouping'=> 'location-fields',
                          ),
                   
                   'civicrm_phone' => 
                   array( 'dao' => 'CRM_Core_DAO_Phone',
                          'fields' =>
                          array( 'phone' => null),
                          'grouping'=> 'location-fields',
                          ),
                   );
        $this->_options = array( 'include_statistics' => array( 'title'  => ts( 'Include Statistics' ),
                                                                'type'   => 'checkbox',
                                                                'default'=> true )
                                 );
        parent::__construct( );
    }

    function preProcess( ) {
        $this->assign( 'reportTitle', ts('Walk List Report' ) );
        parent::preProcess( );
    }

    function select( ) {
        $select = array( );

        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            foreach ( $table['fields'] as $fieldName => $field ) {
                if ( CRM_Utils_Array::value( 'required', $field ) ||
                     CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                    if ( $tableName == 'civicrm_address' ) {
                        $this->_addressField = true;
                    } else if ( $tableName == 'civicrm_email' ) {
                        $this->_emailField = true;
                    } else if ( $tableName == 'civicrm_phone' ) {
                        $this->_phoneField = true;
                    }

                    $select[] = "{$table['alias']}.{$fieldName} as {$tableName}_{$fieldName}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                    $this->_columnHeaders["{$tableName}_{$fieldName}"]['type']  = $field['type'];
                }
            }
        }

        $this->_select = "SELECT " . implode( ",\n", $select ) . " ";
    }

    function from( ) {
        $this->_from = null;

        $this->_from = "
FROM       civicrm_contact {$this->_aliases['civicrm_contact']}
";

        if ( $this->_addressField ) {
            $this->_from .= "LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND {$this->_aliases['civicrm_address']}.is_primary = 1\n";
        }
        
        if ( $this->_emailField ) {
            $this->_from .= "LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']} ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND {$this->_aliases['civicrm_email']}.is_primary = 1\n";
        }

        if ( $this->_phoneField ) {
            $this->_from .= "LEFT JOIN civicrm_phone {$this->_aliases['civicrm_phone']} ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND {$this->_aliases['civicrm_phone']}.is_primary = 1\n";
        }
    }

    function where( ) {
        $clauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;

                    if ( $field['type'] & CRM_Utils_Type::T_DATE ) {
                        $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );
                        
                        $clause = $this->dateClause( $field['name'], $relative, $from, $to );
                    } else {
                        $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
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


    function orderBy( ) {
        $this->_orderBy = "";
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('order_bys', $table) ) {
                foreach ( $table['order_bys'] as $fieldName => $field ) {
                    $this->_orderBy[] = $field['dbAlias'];
                }
            }
        }
        $this->_orderBy = "ORDER BY " . implode( ', ', $this->_orderBy ) . " ";
    }

    function statistics( &$rows ) {
        $statistics   = array();

        $statistics[] = array( 'title' => ts('Row(s) Listed'),
                               'value' => count($rows) );

        
        return $statistics;
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );

        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;

        $this->processReportMode( );

        $this->select ( );
        $this->from   ( );
        $this->where  ( );
        $this->orderBy( );
        $this->limit  ( );

        $sql  = "{$this->_select} {$this->_from} {$this->_where} {$this->_orderBy} {$this->_limit}";
        $dao  = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );
        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_columnHeaders as $key => $value ) {
                $row[$key] = $dao->$key;
            }
            $rows[] = $row;
        }

        $this->formatDisplay( $rows );

        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );

        if ( CRM_Utils_Array::value( 'include_statistics', $this->_params['options'] ) ) {
            $this->assign( 'statistics',
                           $this->statistics( $rows ) );
        }

        parent::endPostProcess( );
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $entryFound = false;
        foreach ( $rows as $rowNum => $row ) {
            // handle state province
            if ( array_key_exists('civicrm_address_state_province_id', $row) ) {
                if ( $value = $row['civicrm_address_state_province_id'] ) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = 
                        CRM_Core_PseudoConstant::stateProvince( $value );
                }
                $entryFound = true;
            }

            // handle country
            if ( array_key_exists('civicrm_address_country_id', $row) ) {
                if ( $value = $row['civicrm_address_country_id'] ) {
                    $rows[$rowNum]['civicrm_address_country_id'] = 
                        CRM_Core_PseudoConstant::country( $value );
                }
                $entryFound = true;
            }

            // convert display name to links
            if ( array_key_exists('civicrm_contact_display_name', $row) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Utils_System::url( 'civicrm/contact/view', 
                                              'reset=1&cid=' . $row['civicrm_contact_id'] );
                $rows[$rowNum]['civicrm_contact_display_name'] = "<a href='$url'>" . 
                    $row["civicrm_contact_display_name"] . '</a>';
                $entryFound = true;
            }

            // skip looking further in rows, if first row itself doesn't 
            // have the column we need
            if ( !$entryFound ) {
                break;
            }
        }
    }
}
