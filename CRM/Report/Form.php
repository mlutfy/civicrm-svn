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

require_once 'CRM/Core/Form.php';

class CRM_Report_Form extends CRM_Core_Form {

    const  
        ROW_COUNT_LIMIT = 50;

    /** 
     * Operator types - used for displaying filter elements
     */
    const
        OP_INT         =  1,
        OP_STRING      =  2,
        OP_DATE        =  4,
        OP_SELECT      =  8,
        OP_MULTISELECT =  16;

    /**
     * The id of the report instance
     *
     * @var integer
     */
    protected $_id;

    /**
     * The id of the report template
     *
     * @var integer;
     */
    protected $_templateID;

    /**
     * The report title
     *
     * @var string
     */
    protected $_title;

    /**
     * The set of all columns in the report. An associative array
     * with column name as the key and attribues as the value
     *
     * @var array
     */
    protected $_columns;

    /**
     * The set of filters in the report
     *
     * @var array
     */
    protected $_filters = array( );

    /**
     * The set of optional columns in the report
     *
     * @var array
     */
    protected $_options = array( );

    protected $_defaults = array( );

    /**
     * Set of statistic fields
     *
     * @var array
     */
    protected $_statFields = array();

    /**
     * Set of statistics data
     *
     * @var array
     */
    protected $_statistics = array();

    /**
     * List of fields not to be repeated during display
     *
     * @var array
     */
    protected $_noRepeats  = array();

    /**
     * List of fields not to be displayed
     *
     * @var array
     */
    protected $_noDisplay  = array();

    /**
     * An attribute for checkbox/radio form field layout
     *
     * @var array
     */
    protected $_fourColumnAttribute = array('</td><td width="25%">', '</td><td width="25%">', 
                                            '</td><td width="25%">', '</tr><tr><td>');

    protected $_force = 1;

    protected $_params         = null;
    protected $_formValues     = null;
    protected $_instanceValues = null;

    protected $_instanceForm   = false;

    protected $_instanceButtonName = null;
    protected $_printButtonName    = null;
    protected $_pdfButtonName      = null;
    protected $_csvButtonName      = null;
    protected $_groupButtonName    = null;
    protected $_chartButtonName    = null;
    protected $_csvSupported       = true;
    protected $_add2groupSupported = true;
    protected $_groups             = null;
    protected $_having             = null;
    protected $_rowsFound          = null;
    protected $_select             = null;
        
    protected $_rollup         = null;
    
    /**
     * To what frequency group-by a date column
     *
     * @var array
     */
    protected $_groupByDateFreq = array( 'MONTH'    => 'Month',
                                         'YEARWEEK' => 'Week',
                                         'QUARTER'  => 'Quarter',
                                         'YEAR'     => 'Year'  );
    
    /**
     * 
     */
    function __construct( ) {
        parent::__construct( );
    }

    function preProcessCommon( ) {
        $this->_force = CRM_Utils_Request::retrieve( 'force',
                                                     'Boolean',
                                                     CRM_Core_DAO::$_nullObject );


        $this->_id = $this->get( 'instanceId' );
        if ( !$this->_id ) {
            $this->_id  = CRM_Report_Utils_Report::getInstanceID( );
        }

        if ( $this->_id ) {
            $params = array( 'id' => $this->_id );
            $this->_instanceValues = array( );
            CRM_Core_DAO::commonRetrieve( 'CRM_Report_DAO_Instance',
                                          $params,
                                          $this->_instanceValues );
            if ( empty($this->_instanceValues) ) {
                CRM_Core_Error::fatal("Report could not be loaded.");
            }

            if ( !empty($this->_instanceValues['permission']) && 
                 (!(CRM_Core_Permission::check( $this->_instanceValues['permission'] ) ||
                    CRM_Core_Permission::check( 'administer Reports' ))) ) {
                CRM_Utils_System::permissionDenied( );
                exit();
            }
            $this->_formValues = unserialize( $this->_instanceValues['form_values'] );

            // lets always do a force if reset is found in the url.
            if ( CRM_Utils_Array::value( 'reset', $_GET ) ) {
                $this->_force = 1;
            }

            // set the mode
            $this->assign( 'mode', 'instance' );
        } else {
            list($optionValueID, $optionValue) = CRM_Report_Utils_Report::getValueIDFromUrl( );
            $instanceCount = CRM_Report_Utils_Report::getInstanceCount( $optionValue );
            if ( ($instanceCount > 0) && $optionValueID ) {
                $this->assign( 'instanceUrl', 
                               CRM_Utils_System::url( 'civicrm/report/list', 
                                                      "reset=1&ovid=$optionValueID" ) );
            }
            if ( $optionValueID ) {
                $this->_description = 
                    CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $optionValueID, 'description' );
            }
            
            // set the mode
            $this->assign( 'mode', 'template' );
        }

        // lets display the 
        $this->_instanceForm       = $this->_force || $this->_id || ( ! empty( $_POST ) );

        // do not display instance form if CiviReport permission is absent
        if ( ! CRM_Core_Permission::check( 'administer Reports' ) ) {
            $this->_instanceForm   = false;
        }

        $this->assign( 'criteriaForm', false );
        if ( CRM_Core_Permission::check( 'administer Reports' ) ||
             CRM_Core_Permission::check( 'access Report Criteria' ) ) {
            $this->assign( 'criteriaForm', true );
        }

        $this->_instanceButtonName = $this->getButtonName( 'submit', 'save'  );
        $this->_printButtonName    = $this->getButtonName( 'submit', 'print' );
        $this->_pdfButtonName      = $this->getButtonName( 'submit', 'pdf'   );
        $this->_csvButtonName      = $this->getButtonName( 'submit', 'csv'   );
        $this->_groupButtonName    = $this->getButtonName( 'submit', 'group' );
        $this->_chartButtonName    = $this->getButtonName( 'submit', 'chart' );
    }

    function addBreadCrumb() {
        $breadCrumbs = array( array( 'title' => ts('Report Templates'),
                                     'url'   => CRM_Utils_System::url('civicrm/admin/report/template/list','reset=1') ) );
        
        CRM_Utils_System::appendBreadCrumb( $breadCrumbs );
    }    

    function preProcess( ) {
        self::preProcessCommon( );
        if ( !$this->_id ) {
            self::addBreadCrumb();
        }

        foreach ( $this->_columns as $tableName => $table ) {
            // set alias
            if ( ! isset( $table['alias'] ) ) {
                $this->_columns[$tableName]['alias'] = substr( $tableName, 8 );
            }
            $this->_aliases[$tableName] = $this->_columns[$tableName]['alias'];

            // higher preference to bao object
            if ( array_key_exists('bao', $table) ) {
                require_once str_replace( '_', DIRECTORY_SEPARATOR, $table['bao'] . '.php' );
                eval( "\$expFields = {$table['bao']}::exportableFields( );");
            } else {
                require_once str_replace( '_', DIRECTORY_SEPARATOR, $table['dao'] . '.php' );
                eval( "\$expFields = {$table['dao']}::export( );");
            }

            $doNotCopy   = array('required');

            $fieldGroups = array('fields', 'filters', 'group_bys', 'order_bys');
            foreach ( $fieldGroups as $fieldGrp ) {
                if ( CRM_Utils_Array::value( $fieldGrp, $table ) && is_array( $table[$fieldGrp] ) ) {
                    foreach ( $table[$fieldGrp] as $fieldName => $field ) {
                        if ( array_key_exists($fieldName, $expFields) ) {
                            foreach ( $doNotCopy as $dnc ) {
                                // unset the values we don't want to be copied.
                                unset($expFields[$fieldName][$dnc]);
                            }
                            if ( empty($field) ) {
                                $this->_columns[$tableName][$fieldGrp][$fieldName] = $expFields[$fieldName];
                            } else {
                                foreach ( $expFields[$fieldName] as $property => $val ) {
                                    if ( ! array_key_exists($property, $field) ) {
                                        $this->_columns[$tableName][$fieldGrp][$fieldName][$property] = $val;
                                    }
                                }
                            }

                            // fill other vars
                            if ( CRM_Utils_Array::value( 'no_repeat', $field ) ) {
                                $this->_noRepeats[] = "{$tableName}_{$fieldName}";
                            }
                            if ( CRM_Utils_Array::value( 'no_display', $field ) ) {
                                $this->_noDisplay[] = "{$tableName}_{$fieldName}";
                            }
                        }

                        // set alias = table-name, unless already set
                        $alias = isset($field['alias']) ? $field['alias'] : 
                            ( isset($this->_columns[$tableName]['alias']) ? 
                              $this->_columns[$tableName]['alias'] : $tableName );
                        $this->_columns[$tableName][$fieldGrp][$fieldName]['alias'] = $alias;

                        // set name = fieldName, unless already set
                        if ( !isset($this->_columns[$tableName][$fieldGrp][$fieldName]['name']) ) {
                            $this->_columns[$tableName][$fieldGrp][$fieldName]['name'] = $fieldName;
                        }

                        // set dbAlias = alias.name, unless already set
                        if ( !isset($this->_columns[$tableName][$fieldGrp][$fieldName]['dbAlias']) ) {
                            $this->_columns[$tableName][$fieldGrp][$fieldName]['dbAlias'] = 
                                $alias . '.' . $this->_columns[$tableName][$fieldGrp][$fieldName]['name'];
                        }

                        if ( CRM_Utils_Array::value('type', $this->_columns[$tableName][$fieldGrp][$fieldName] ) && 
                             in_array($this->_columns[$tableName][$fieldGrp][$fieldName]['type'],
                                      array(CRM_Utils_Type::T_MONEY, CRM_Utils_Type::T_INT)) && 
                             !isset($this->_columns[$tableName][$fieldGrp][$fieldName]['operatorType']) ) {
                            $this->_columns[$tableName][$fieldGrp][$fieldName]['operatorType'] = 
                                CRM_Report_Form::OP_INT;
                        }
                    }
                }
            }

            // copy filters to a separate handy variable
            if ( array_key_exists('filters', $table) ) {
                $this->_filters[$tableName] = $this->_columns[$tableName]['filters'];
            }

            if ( array_key_exists('group_bys', $table) ) {
                $groupBys[$tableName] = $this->_columns[$tableName]['group_bys'];
            }

            if ( array_key_exists('fields', $table) ) {
                $reportFields[$tableName] = $this->_columns[$tableName]['fields'];
            }
            
        }

        if ( $this->_force ) {
            $this->setDefaultValues( false );
        }

        require_once 'CRM/Report/Utils/Get.php';
        CRM_Report_Utils_Get::processFilter( $this->_filters,
                                             $this->_defaults );
        CRM_Report_Utils_Get::processGroupBy( $groupBys,
                                              $this->_defaults );
        CRM_Report_Utils_Get::processFields( $reportFields,
                                             $this->_defaults );
        CRM_Report_Utils_Get::processChart( $this->_defaults );
        
        if ( $this->_force ) {
            $this->_formValues = $this->_defaults;
            $this->postProcess( );
        }

    }

    function setDefaultValues( $freeze = true ) {
        $freezeGroup = array();

        // FIXME: generalizing form field naming conventions would reduce 
        // lots of lines below.
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( !array_key_exists('no_display', $field) ) {
                        if ( isset($field['required']) ) {
                            // set default
                            $this->_defaults['fields'][$fieldName] = 1;
                            
                            if ( $freeze ) {
                                // find element object, so that we could use quickform's freeze method 
                                // for required elements
                                $obj = $this->getElementFromGroup("fields",
                                                                  $fieldName);
                                if ( $obj ) {
                                    $freezeGroup[] = $obj;
                                }
                            }
                        } else if ( isset($field['default']) ) {
                            $this->_defaults['fields'][$fieldName] = $field['default'];
                        }
                    }
                }
            }

            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( isset($field['default']) ) {
                        if ( CRM_Utils_Array::value('frequency', $field) ) {
                            $this->_defaults['group_bys_freq'][$fieldName] = 'MONTH';
                        }
                        $this->_defaults['group_bys'][$fieldName] = $field['default'];
                    }
                }
            }
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    if ( isset($field['default']) ) {
                        if ( CRM_Utils_Array::value('type', $field ) & CRM_Utils_Type::T_DATE ) {
                            $this->_defaults["{$fieldName}_relative"] = $field['default'];
                        } else {
                            $this->_defaults["{$fieldName}_value"]    = $field['default'];
                        }
                    }
                    //assign default value as "in" for multiselect
                    //operator, To freeze the select element
                    if ( CRM_Utils_Array::value('operatorType', $field ) == CRM_Report_FORM::OP_MULTISELECT ) {
                        $this->_defaults["{$fieldName}_op"] = 'in';
                    }
                }
            }

            foreach ( $this->_options as $fieldName => $field ) {
                if ( isset($field['default']) ) {
                    $this->_defaults['options'][$fieldName] = $field['default'];
                }
            }
        }

        // lets finish freezing task here itself
        if ( !empty($freezeGroup) ) {
            foreach ( $freezeGroup as $elem ) {
                $elem->freeze();
            }
        }

        if ( $this->_formValues ) {
            $this->_defaults = array_merge( $this->_defaults, $this->_formValues );
        }

        if ( $this->_instanceValues ) {
            $this->_defaults = array_merge( $this->_defaults, $this->_instanceValues );
        }

        require_once 'CRM/Report/Form/Instance.php';
        CRM_Report_Form_Instance::setDefaultValues( $this, $this->_defaults );
        
        return $this->_defaults;
    }

    function getElementFromGroup( $group, $grpFieldName ) {
        $eleObj = $this->getElement( $group );
        foreach ( $eleObj->_elements as $index => $obj ) {
            if ( $grpFieldName == $obj->_attributes['name']) {
                return $obj;
            }
        }
        return false;
    }

    function addColumns( ) {
        $options = array();
        $colGroups = null;
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( !array_key_exists('no_display', $field) ) {
                        if ( isset($table['grouping']) ) { 
                            $colGroups[$table['grouping']][$field['title']] = $fieldName;
                        } else {
                            $colGroups[$tableName][$field['title']] = $fieldName;
                        }
                        $options[$field['title']] = $fieldName;
                    }
                } 
            }
        }
        
        $this->addCheckBox( "fields", ts('Select Columns'), $options, null, 
                            null, null, null, $this->_fourColumnAttribute );
        $this->assign( 'colGroups', $colGroups );
    }

    function addFilters( ) {
        require_once 'CRM/Utils/Date.php';
        require_once 'CRM/Core/Form/Date.php';
        $options = $filters = array();
        $count = 1;
        foreach ( $this->_filters as $table => $attributes ) {
            foreach ( $attributes as $fieldName => $field ) {
                // get ready with option value pair
                $operations = self::getOperationPair( CRM_Utils_Array::value( 'operatorType', $field ) );
                
                $filters[$table][$fieldName] = $field;
                
                switch ( CRM_Utils_Array::value( 'operatorType', $field )) {
                case CRM_Report_FORM::OP_MULTISELECT :
                    // assume a multi-select field
                    if ( !empty( $field['options'] ) ) {
                        $element = $this->addElement('select', "{$fieldName}_op", ts( 'Operator:' ), $operations);
                        $element->freeze();
                        $select = $this->addElement('select', "{$fieldName}_value", null, 
                                                    $field['options'], array( 'size' => 4, 
                                                                              'style' => 'width:200px'));
                        $select->setMultiple( true );
                    }
                    break;
                    
                case CRM_Report_FORM::OP_SELECT :
                    // assume a select field
                    $this->addElement('select', "{$fieldName}_op", ts( 'Operator:' ), $operations);
                    $this->addElement('select', "{$fieldName}_value", null, $field['options']);
                    break;
                    
                case CRM_Report_FORM::OP_DATE :
                    // build datetime fields
                    CRM_Core_Form_Date::buildDateRange( $this, $fieldName, $count );
                    $count++;
                    break;
                    
                case CRM_Report_FORM::OP_INT:
                    // and a min value input box
                    $this->add( 'text', "{$fieldName}_min", ts('Min') );
                    // and a max value input box
                    $this->add( 'text', "{$fieldName}_max", ts('Max') );
                default:
                    // default type is string
                    $this->addElement('select', "{$fieldName}_op", ts( 'Operator:' ), $operations,
                                      array('onchange' =>"return showHideMaxMinVal( '$fieldName', this.value );"));
                    // we need text box for value input
                    $this->add( 'text', "{$fieldName}_value", null );
                    break;
                }
            }
        }
        $this->assign( 'filters', $filters );
    }

    function addOptions( ) {
        if ( !empty( $this->_options ) ) {
            // FIXME: For now lets build all elements as checkboxes. 
            // Once we clear with the format we can build elements based on type
            
            $options = array();
            foreach ( $this->_options as $fieldName => $field ) {
                if ( $field['type'] == 'select' ) {
                    $this->addElement( 'select', "{$fieldName}", $field['title'], $field['options']);
                } else {
                    $options[$field['title']] = $fieldName;
                }
            }

            $this->addCheckBox( "options", $field['title'], 
                                $options, null, 
                                null, null, null, $this->_fourColumnAttribute );
        }
    }

    function addChartOptions( ) {
        if ( !empty( $this->_charts ) ) {
            $this->addElement( 'select', "charts", ts( 'Chart' ), $this->_charts );
            $this->assign( 'charts', $this->_charts );
            $this->addElement('submit', $this->_chartButtonName, ts('View') );
        }
    }
    
    function addGroupBys( ) {
        $options = $freqElements = array( );

        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('group_bys', $table) ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( !empty($field) ) {
                        $options[$field['title']] = $fieldName;
                        if ( CRM_Utils_Array::value( 'frequency', $field ) ) {
                            $freqElements[$field['title']] = $fieldName;
                        }
                    }
                }
            }
        }
        $this->addCheckBox( "group_bys", ts('Group by columns'), $options, null, 
                            null, null, null, $this->_fourColumnAttribute );
        $this->assign( 'groupByElements', $options );

        foreach ( $freqElements as $name ) {
            $this->addElement( 'select', "group_bys_freq[$name]", 
                               ts( 'Frequency' ), $this->_groupByDateFreq );
        }
    }

    function buildInstanceAndButtons( ) {
        require_once 'CRM/Report/Form/Instance.php';
        CRM_Report_Form_Instance::buildForm( $this );
        
        $label = $this->_id ? ts( 'Update Report' ) : ts( 'Create Report' );
        
        $this->addElement( 'submit', $this->_instanceButtonName, $label );
        $this->addElement('submit', $this->_printButtonName, ts( 'Print Report' ) );
        $this->addElement('submit', $this->_pdfButtonName, ts( 'PDF' ) );
        if ( $this->_instanceForm ){
            $this->assign( 'instanceForm', true );
        }

        $label = $this->_id ? ts( 'Print Report' ) : ts( 'Print Preview' );
        $this->addElement('submit', $this->_printButtonName, $label );

        $label = $this->_id ? ts( 'PDF' ) : ts( 'Preview PDF' );
        $this->addElement('submit', $this->_pdfButtonName, $label );

        $label = $this->_id ? ts( 'Export to CSV' ) : ts( 'Preview CSV' );

        if ( $this->_csvSupported ) {
            $this->addElement('submit', $this->_csvButtonName, $label );
        }

        if ( CRM_Core_Permission::check( 'administer Reports' ) && $this->_add2groupSupported ) {
            $this->addElement( 'select', 'groups', ts( 'Group' ), 
                               array( '' => ts( '- select group -' )) + CRM_Core_PseudoConstant::staticGroup( ) );
            $this->assign( 'group', $this->_groups );
        }
        
        //$this->addElement('select', 'select_add_to_group_id', ts('Group'), $groupList);
        $label = ts( 'Add these Contacts to Group' );
        $this->addElement('submit', $this->_groupButtonName, $label );

        $this->addChartOptions( );
        $this->addButtons( array(
                                 array ( 'type'      => 'submit',
                                         'name'      => ts('Preview Report'),
                                         'isDefault' => true   ),
                                 )
                           );
    }

    function buildQuickForm( ) {
        $this->addColumns( );

        $this->addFilters( );
      
        $this->addOptions( );

        $this->addGroupBys( );

        $this->buildInstanceAndButtons( );

        //add form rule for report
        if ( is_callable( array( $this, 'formRule' ) ) ) {
            $this->addFormRule( array( get_class($this), 'formRule' ), $this );
        }
    }
    
    static function getOperationPair( $type = "string" ) {
        // FIXME: At some point we should move these key-val pairs 
        // to option_group and option_value table.

        switch ( $type ) {
        case CRM_Report_FORM::OP_INT :
            return array( 'lte' => 'Is less than or equal to', 
                          'gte' => 'Is greater than or equal to',
                          'bw'  => 'Is between',
                          'eq'  => 'Is equal to', 
                          'lt'  => 'Is less than', 
                          'gt'  => 'Is greater than',
                          'neq' => 'Is not equal to', 
                          'nbw' => 'Is not between',
                          );
            break;
        case CRM_Report_FORM::OP_SELECT :
            return array( 'eq'  => 'Is equal to' );
            break;
        case CRM_Report_FORM::OP_MULTISELECT :
            return array( 'in'  => 'Is one of' );
            break;
        default:
            // type is string
            return array('has'  => 'Contains', 
                         'sw'   => 'Starts with', 
                         'ew'   => 'Ends with',
                         'nhas' => 'Does not contain', 
                         'eq'   => 'Is equal to', 
                         'neq'  => 'Is not equal to', 
                         );
        }
    }

    static function getSQLOperator( $operator = "like" ) {
        switch ( $operator ) {
        case 'eq':
            return '=';
        case 'lt':
            return '<'; 
        case 'lte':
            return '<='; 
        case 'gt':
            return '>'; 
        case 'gte':
            return '>='; 
        case 'ne' :
        case 'neq':
            return '!=';
        case 'nhas':
            return 'NOT LIKE';
        case 'in':
            return 'IN';
        default:
            // type is string
            return 'LIKE';
        }
    }

    static function whereClause( &$field, $op,
                                 $value, $min, $max ) {

        $type   = CRM_Utils_Type::typeToString( CRM_Utils_Array::value( 'type', $field ) );
        $clause = null;

        switch ( $op ) {
        case 'bw':
        case 'nbw':
            if ( ( $min !== null && strlen( $min ) > 0 ) ||
                 ( $max !== null && strlen( $max ) > 0 ) ) {
                $min = CRM_Utils_Type::escape( $min, $type );
                $max = CRM_Utils_Type::escape( $max, $type );
                $clauses = array( );
                if ( $min ) {
                    if ( $op == 'bw' ) {
                        $clauses[] = "( {$field['dbAlias']} >= $min )";
                    } else {
                        $clauses[] = "( {$field['dbAlias']} < $min )";
                    }
                }
                if ( $max ) {
                    if ( $op == 'bw' ) {
                        $clauses[] = "( {$field['dbAlias']} <= $max )";
                    } else {
                        $clauses[] = "( {$field['dbAlias']} > $max )";
                    }
                }

                if ( ! empty( $clauses ) ) {
                    if ( $op == 'bw' ) {
                        $clause = implode( ' AND ', $clauses );
                    } else {
                        $clause = implode( ' OR ', $clauses );
                    }
                }
            }
            break;

        case 'has':
        case 'nhas': 
            if ( $value !== null && strlen( $value ) > 0 ) {
                $value  = CRM_Utils_Type::escape( $value, $type );
                if ( strpos( $value, '%' ) === false ) {
                    $value = "'%{$value}%'";
                } else {
                    $value = "'{$value}'";
                }
                $sqlOP  = self::getSQLOperator( $op );
                $clause = "( {$field['dbAlias']} $sqlOP $value )";
            }
            break;
                
        case 'in':
            if ( $value !== null && count( $value ) > 0 ) {
                $sqlOP  = self::getSQLOperator( $op );
                $clause = "( {$field['dbAlias']} $sqlOP (" . implode( ', ', $value ) . ") )";
            }
            break;

        case 'sw':
        case 'ew':
            if ( $value !== null && strlen( $value ) > 0 ) {
                $value  = CRM_Utils_Type::escape( $value, $type );
                if ( strpos( $value, '%' ) === false ) {
                    if ( $op == 'sw' ) {
                        $value = "'{$value}%'";
                    } else {
                        $value = "'%{$value}'";
                    }
                } else {
                    $value = "'{$value}'";
                }
                $sqlOP  = self::getSQLOperator( $op );
                $clause = "( {$field['dbAlias']} $sqlOP $value )";
            }
            break;
                
        default:
            if ( $value !== null && strlen( $value ) > 0 ) {
                if ( isset($field['clause']) ) {
                    // FIXME: we not doing escape here. Better solution is to use two 
                    // different types - data-type and filter-type 
                    eval("\$clause = \"{$field['clause']}\";"); 
                } else {
                    $value  = CRM_Utils_Type::escape( $value, $type );
                    $sqlOP  = self::getSQLOperator( $op );
                    if ( $field['type'] == CRM_Utils_Type::T_STRING ) {
                        $value = "'{$value}'";
                    }
                    $clause = "( {$field['dbAlias']} $sqlOP $value )";
                }
            }
            break;
        }
        
        return $clause;
    }

    static function dateClause( $fieldName,
                                $relative, $from, $to ) {
        $clauses         = array( );
        list($from, $to) = self::getFromTo($relative, $from, $to);

        if ( $from ) {
            $clauses[] = "( {$fieldName} >= $from )";
        }

        if ( $to ) {
            $clauses[] = "( {$fieldName} <= {$to}235959 )";
        }

        if ( ! empty( $clauses ) ) {
            return implode( ' AND ', $clauses );
        }

        return null;
    }

    static function dateDisplay( $relative, $from, $to ) {
        list($from, $to) = self::getFromTo($relative, $from, $to);

        if ( $from ) {
            $clauses[] = CRM_Utils_Date::customFormat($from, null, array('m', 'M'));
        } else {
            $clauses[] = 'Past';
        }

        if ( $to ) {
            $clauses[] = CRM_Utils_Date::customFormat($to, null, array('m', 'M'));
        } else {
            $clauses[] = 'Today';
        }

        if ( ! empty( $clauses ) ) {
            return implode( ' - ', $clauses );
        }

        return null;
    }

    static function getFromTo( $relative, $from, $to ) {
        require_once 'CRM/Utils/Date.php';
        if ( $relative ) {
            list( $term, $unit ) = explode( '.', $relative );
            $dateRange = CRM_Utils_Date::relativeToAbsolute( $term, $unit );
            $from = $dateRange['from'];
            $to   = $dateRange['to'];
        }

        if ( CRM_Utils_Date::isDate( $from ) ) {
            $revDate = array_reverse( $from );
            $from    = CRM_Utils_Date::format( $revDate );
        } else {
            $from    = null;
        }

        if ( CRM_Utils_Date::isDate( $to ) ) {
            $revDate = array_reverse( $to );
            $to      = CRM_Utils_Date::format( $revDate );
        } else {
            $to      = null;
        }

        return array($from, $to);
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
    }

    function removeDuplicates( &$rows ) {
        if ( empty($this->_noRepeats) ) {
            return;
        }
        $checkList = array();

        foreach ( $rows as $key => $list ) {
            foreach ( $list as $colName => $colVal ) {
                if ( is_array($checkList[$colName]) && 
                     in_array($colVal, $checkList[$colName]) ) {
                    $rows[$key][$colName] = "";
                }
                if ( in_array($colName, $this->_noRepeats) ) {
                    $checkList[$colName][] = $colVal;
                }
            }
        }
    }

    function fixSubTotalDisplay( &$row, $fields, $subtotal = true ) {
        require_once 'CRM/Utils/Money.php';
        foreach ( $row as $colName => $colVal ) {
            if ( in_array($colName, $fields) ) {
                $row[$colName] = $row[$colName];
            } else if ( isset($this->_columnHeaders[$colName]) ) {
                if ( $subtotal ) {
                    $row[$colName] = "Subtotal";
                    $subtotal = false;
                } else {
                    unset($row[$colName]);
                }
            }
        }
    }

    function grandTotal( &$rows ) {
        if ( !$this->_rollup || ($this->_rollup == '') || 
             ($this->_limit && count($rows) >= self::ROW_COUNT_LIMIT) ) {
            return false;
        }
        $lastRow = array_pop($rows);

        $grandFlag = false;
        foreach ($this->_columnHeaders as $fld => $val) {
            if ( !in_array($fld, $this->_statFields) ) {
                if ( !$grandFlag ) {
                    $lastRow[$fld] = "Grand Total";
                    $grandFlag = true;
                } else{
                    $lastRow[$fld] = "";
                }
            }
        }

        $this->assign( 'grandStat', $lastRow );
        return true;
    }

    function formatDisplay( &$rows, $pager = true ) {
        // set pager based on if any limit was applied in the query. 
        if ( $pager ) {
            $this->setPager( );
        }

        // allow building charts if any
        if ( ! empty($this->_params['charts']) && !empty($rows) ) {
            require_once 'CRM/Utils/PChart.php';
            $this->buildChart( $rows );
            $this->assign( 'chartEnabled', true );
        }
        
        // unset columns not to be displayed.
        foreach ( $this->_columnHeaders as $key => $value ) {
            if ( is_array($value) && isset($value['no_display']) ) {
                unset($this->_columnHeaders[$key]);
            }
        }

        // unset columns not to be displayed.
        if ( !empty($rows) ) {
            foreach ( $this->_noDisplay as $noDisplayField ) {
                foreach ( $rows as $rowNum => $row ) {
                    unset($this->_columnHeaders[$noDisplayField]);
                }
            }
        }

        // process grand-total row
        $this->grandTotal( $rows );

        // use this method for formatting rows for display purpose.
        $this->alterDisplay( $rows );
    }

    function buildChart( &$rows ) {
        // override this method for building charts.
    }

    function where( ) {
        $whereClauses = $havingClauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    if ( CRM_Utils_Array::value( 'type', $field ) & CRM_Utils_Type::T_DATE ) {
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
                        if ( CRM_Utils_Array::value( 'having', $field ) ) {
                            $havingClauses[] = $clause;
                        } else {
                            $whereClauses[] = $clause;
                        }
                    }
                }
            }
        }

        if ( empty( $whereClauses ) ) {
            $this->_where = "WHERE ( 1 ) ";
            $this->_having = "";
        } else {
            $this->_where = "WHERE " . implode( ' AND ', $whereClauses );
        }

        if ( !empty( $havingClauses ) ) {
            // use this clause to construct group by clause.
            $this->_having = "HAVING " . implode( ' AND ', $havingClauses );
        }
    }

    function processReportMode( ) {
        $buttonName = $this->controller->getButtonName( );

        $output     = CRM_Utils_Request::retrieve( 'output',
                                                   'String', CRM_Core_DAO::$_nullObject );
        $this->_sendmail = CRM_Utils_Request::retrieve( 'sendmail', 
                                                        'Boolean', CRM_Core_DAO::$_nullObject );
        $this->_absoluteUrl = false;
        $printOnly = false;
        $this->assign( 'printOnly', false );

        if ( $this->_printButtonName == $buttonName || $output == 'print' || $this->_sendmail ) {
            $this->assign( 'printOnly', true );
            $printOnly = true;
            $this->assign( 'outputMode', 'print' );
            $this->_outputMode = 'print';
        } else if ( $this->_pdfButtonName   == $buttonName || $output == 'pdf' ) {
            $this->assign( 'printOnly', true );
            $printOnly = true;
            $this->assign( 'outputMode', 'pdf' );
            $this->_outputMode  = 'pdf';
            $this->_absoluteUrl = true;
        } else if ( $this->_csvButtonName   == $buttonName || $output == 'csv' ) {
            $this->assign( 'printOnly', true );
            $printOnly = true;
            $this->assign( 'outputMode', 'csv' );
            $this->_outputMode  = 'csv';
            $this->_absoluteUrl = true;
        } else if ( $this->_groupButtonName   == $buttonName || $output == 'group' ) {
            $this->assign( 'outputMode', 'group' );
            $this->_outputMode  = 'group';
        } else {
            $this->assign( 'outputMode', 'html' );
            $this->_outputMode = 'html';
        }

        // Get today's date to include in printed reports
        if ( $printOnly ) {
            require_once 'CRM/Utils/Date.php';
            $reportDate = CRM_Utils_Date::customFormat( date('Y-m-d H:i') );
            $this->assign( 'reportDate', $reportDate );
        }
    }

    function beginPostProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );
        if ( empty( $this->_params ) &&
             $this->_force ) {
            $this->_params = $this->_formValues;
        }
        $this->_formValues = $this->_params ;
        if ( CRM_Core_Permission::check( 'administer Reports' ) &&
             isset( $this->_id ) && 
             $this->_instanceButtonName == $this->controller->getButtonName( ).'_save' ) {
            $this->assign( 'updateReportButton', true );
        }
        $this->processReportMode( );
    }

    function buildQuery( $applyLimit = true ) {
        $this->select ( );
        $this->from   ( );
        $this->where  ( );
        $this->groupBy( );
        $this->orderBy( );

        if ( $applyLimit ) {
            $this->limit( );
        }
        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_having} {$this->_orderBy} {$this->_limit}";

        return $sql;
    }

    function groupBy( ) {
        $this->_groupBy = "";
    }

    function orderBy( ) {
        $this->_orderBy = "";
    }

    function buildRows( $sql, &$rows ) {
        $dao  = CRM_Core_DAO::executeQuery( $sql );
        if ( ! is_array($rows) ) {
            $rows = array( );
        }

        // use this method to modify $this->_columnHeaders
        $this->modifyColumnHeaders( );

        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_columnHeaders as $key => $value ) {
                if ( property_exists( $dao, $key ) ) {
                    $row[$key] = $dao->$key;
                }
            }
            $rows[] = $row;
        }

    }

    function modifyColumnHeaders( ) {
        // use this method to modify $this->_columnHeaders
    }

    function doTemplateAssignment( &$rows ) {
        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        $this->assign( 'statistics',  $this->statistics( $rows ) );
    }

    // override this method to build your own statistics
    function statistics( &$rows ) {
        $statistics = array();

        $count = count($rows);
        if ( $this->_rollup && ($this->_rollup != '') ) {
            $count++;
        }

        $this->countStat  ( $statistics, $count );

        $this->groupByStat( $statistics );

        $this->filterStat ( $statistics );

        return $statistics;
    }

    function countStat( &$statistics, $count ) {
        $statistics['counts']['rowCount'] = array( 'title' => ts('Row(s) Listed'),
                                                   'value' => $count );

        if ( $this->_rowsFound && ($this->_rowsFound > $count) ) {
            $statistics['counts']['rowsFound'] = array( 'title' => ts('Total Row(s)'),
                                                        'value' => $this->_rowsFound );
        }
    }

    function groupByStat( &$statistics ) {
        if ( CRM_Utils_Array::value( 'group_bys', $this->_params ) && 
             is_array($this->_params['group_bys']) && 
             !empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                if ( array_key_exists('group_bys', $table) ) {
                    foreach ( $table['group_bys'] as $fieldName => $field ) {
                        if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                            $combinations[] = $field['title'];
                        }
                    }
                }
            }
            $statistics['groups'][] = array( 'title' => ts('Grouping(s)'),
                                             'value' => implode( ' & ', $combinations ) );
        }
    }

    function filterStat( &$statistics ) {
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'type', $field ) & CRM_Utils_Type::T_DATE ) {
                        list($from, $to) = 
                            $this->getFromTo( CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params ), 
                                              CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params ),
                                              CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params ) );
                        $from = CRM_Utils_Date::customFormat( $from, null, array('d') );
                        $to   = CRM_Utils_Date::customFormat( $to,   null, array('d') );
                        
                        if ( $from || $to ) {
                            $statistics['filters'][] = 
                                array( 'title' => $field['title'],
                                       'value' => "Between {$from} and {$to}" );
                        }
                    } else {
                        $op    = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                        $value = null;
                        if ( $op ) {
                            $pair  = self::getOperationPair( CRM_Utils_Array::value( 'operatorType', $field ) );
                            $min   = CRM_Utils_Array::value( "{$fieldName}_min",  $this->_params );
                            $max   = CRM_Utils_Array::value( "{$fieldName}_max",  $this->_params );
                            $val   = CRM_Utils_Array::value( "{$fieldName}_value",$this->_params );
                            if ( in_array($op, array('bw', 'nbw')) && ($min || $max) ) {
                                $value = "{$pair[$op]} " . $min . ' and ' . $max;
                            } else if ( is_array($val) && (!empty($val)) ) {
                                $options = $field['options'];
                                foreach ( $val as $key => $valIds ) {
                                    if ( isset($options[$valIds]) ) {
                                        $val[$key] = $options[$valIds];
                                    }
                                }
                                $pair[$op] = (count($val) == 1) ? ts('Is') : $pair[$op];
                                $val       = implode( ', ', $val );
                                $value     = "{$pair[$op]} " . $val;
                            } else if ( $val ) {
                                $value = "{$pair[$op]} " . $val;
                            }
                        }
                        if ( $value ) {
                            $statistics['filters'][] = 
                                array( 'title' => $field['title'],
                                       'value' => $value );
                        }
                    }
                }
            }
        }
    }

    function endPostProcess( &$rows = null ) {
        if ( $this->_outputMode == 'print' || 
             $this->_outputMode == 'pdf'   ||
             $this->_sendmail              ) {
            $templateFile = parent::getTemplateFileName( );
            
            $content = $this->_formValues['report_header'] .
                CRM_Core_Form::$_template->fetch( $templateFile ) .      
                $this->_formValues['report_footer'] ;

            if ( $this->_sendmail ) {
                if ( CRM_Report_Utils_Report::mailReport( $content, $this->_id,
                                                          $this->_outputMode  ) ) {
                    CRM_Core_Session::setStatus( ts("Report mail has been sent.") );
                } else {
                    CRM_Core_Session::setStatus( ts("Report mail could not be sent.") );
                }
                if ( $this->get( 'instanceId' ) ) {
                    exit();
                } 

                CRM_Utils_System::redirect( CRM_Utils_System::url( CRM_Utils_System::currentPath(), 
                                                                   'reset=1' ) );
         
            } else if ( $this->_outputMode == 'print' ) {
                echo $content;
            } else {
                require_once 'CRM/Utils/PDF/Utils.php';                                
                CRM_Utils_PDF_Utils::html2pdf( $content, "CiviReport.pdf" );
            }
            exit( );
        } else if ( $this->_outputMode == 'csv' ) {
            CRM_Report_Utils_Report::export2csv( $this, $rows );
        } else if ( $this->_outputMode == 'group' ) {
            $group = $this->_params['groups'];
            CRM_Report_Utils_Report::add2group( $this, $group );
        } else if ( $this->_instanceButtonName == $this->controller->getButtonName( ) ) {
            require_once 'CRM/Report/Form/Instance.php';
            CRM_Report_Form_Instance::postProcess( $this );
        }      
    }

    function postProcess( ) {
        // get ready with post process params
        $this->beginPostProcess( );

        // build query
        $sql = $this->buildQuery( );

        // build array of result based on column headers. This method also allows 
        // modifying column headers before using it to build result set i.e $rows.
        $this->buildRows ( $sql, $rows );

        // format result set. 
        $this->formatDisplay( $rows );

        // assign variables to templates
        $this->doTemplateAssignment( $rows );

        // do print / pdf / instance stuff if needed
        $this->endPostProcess( $rows );
    }

    function limit( $rowCount = self::ROW_COUNT_LIMIT ) {
        require_once 'CRM/Utils/Pager.php';
        // lets do the pager if in html mode
        $this->_limit = null;
        if ( $this->_outputMode == 'html' ) {
            $this->_select = str_ireplace( 'SELECT ', 'SELECT SQL_CALC_FOUND_ROWS ', $this->_select );

            $pageId = CRM_Utils_Request::retrieve( 'crmPID', 'Integer', CRM_Core_DAO::$_nullObject );
           
            if ( !$pageId && !empty($_POST) && isset($_POST['crmPID_B']) ) {
                if ( !isset($_POST['PagerBottomButton']) ) {
                    unset( $_POST['crmPID_B'] );
                } else {
                    $pageId = max( (int) @$_POST['crmPID_B'], 1 );
                }
            } 
            
            $pageId = $pageId ? $pageId : 1;
            $this->set( CRM_Utils_Pager::PAGE_ID, $pageId );
            $offset = ( $pageId - 1 ) * $rowCount;

            $this->_limit  = " LIMIT $offset, " . $rowCount;
        }
    }

    function setPager( $rowCount = self::ROW_COUNT_LIMIT ) {
        if ( $this->_limit && ($this->_limit != '') ) {
            require_once 'CRM/Utils/Pager.php';
            $sql    = "SELECT FOUND_ROWS();";
            $this->_rowsFound = CRM_Core_DAO::singleValueQuery( $sql );
            $params = array( 'total'        => $this->_rowsFound,
                             'rowCount'     => $rowCount,
                             'status'       => ts( 'Records %%StatusMessage%%' ),
                             'buttonBottom' => 'PagerBottomButton',
                             'pageID'       => $this->get( CRM_Utils_Pager::PAGE_ID ) );

            $pager = new CRM_Utils_Pager( $params );
            $this->assign_by_ref( 'pager', $pager );
        }
    }

}
