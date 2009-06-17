<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
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

require_once 'CRM/Core/BAO/OptionValue.php';
require_once 'CRM/Core/BAO/OptionGroup.php';

class CRM_Core_OptionValue 
{
    /**
     * static field for all the option value information that we can potentially export
     *
     * @var array
     * @static
     */
    static $_exportableFields = null;

    /**
     * static field for all the option value information that we can potentially export
     *
     * @var array
     * @static
     */
    static $_importableFields = null;
    
    /**
     * static field for all the option value information that we can potentially export
     *
     * @var array
     * @static
     */
    static $_fields = null;

    /**
     * Function to return option-values of a particular group
     *
     * @param  array     $groupParams   Array containing group fields whose option-values is to retrieved.
     * @param  string    $orderBy       for orderBy clause
     * @param  array     $links         has links like edit, delete, disable ..etc
     *
     * @return array of option-values     
     * 
     * @access public
     * @static
     */
    static function getRows( $groupParams, $links, $orderBy = 'weight' ) 
    {
        $optionValue = array();
        
        if (! isset( $groupParams['id'] ) || ! $groupParams['id'] ) {
            if ( $groupParams['name'] ) {
                $config =& CRM_Core_Config::singleton( );
                
                $optionGroup = CRM_Core_BAO_OptionGroup::retrieve($groupParams, $dnc);
                $optionGroupID = $optionGroup->id;
            }
        } else {
            $optionGroupID = $groupParams['id'];
        }
        
        $dao =& new CRM_Core_DAO_OptionValue();
        
        if ( $optionGroupID ) {
            $dao->option_group_id = $optionGroupID;
            $dao->orderBy($orderBy);
            $dao->find();
        }
        
        require_once 'CRM/Core/Component.php';
        $componentNames = CRM_Core_Component::getNames();
        $visibilityLabels = CRM_Core_PseudoConstant::visibility( );
        while ( $dao->fetch() ) {
            $optionValue[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $optionValue[$dao->id] );
            // form all action links
            $action = array_sum(array_keys($links));
         
            if( $dao->is_default ) {
                $optionValue[$dao->id]['default_value'] = '[x]';
            }
            
            // update enable/disable links depending on if it is is_reserved or is_active
            if ( $dao->is_reserved ) {
                $action = CRM_Core_Action::UPDATE;
            } else {
                if ( $dao->is_active ) {
                    $action -= CRM_Core_Action::ENABLE;
                } else {
                    $action -= CRM_Core_Action::DISABLE;
                }
            }
            $optionValue[$dao->id]['label']  = htmlspecialchars( $optionValue[$dao->id]['label'] );
            $optionValue[$dao->id]['action'] = CRM_Core_Action::formLink($links, $action, 
                                                                         array('id'    => $dao->id,
                                                                               'gid'   => $optionGroupID,
                                                                               'value' => $dao->value ) );
            
            if ( CRM_Utils_Array::value( 'component_id', $optionValue[$dao->id] ) ) {
                $optionValue[$dao->id]['component_name'] = $componentNames[$optionValue[$dao->id]['component_id']];
            } else {
                $optionValue[$dao->id]['component_name'] = 'Contact';
            }
            
            if (  CRM_Utils_Array::value( 'visibility_id', $optionValue[$dao->id] ) ) {
                $optionValue[$dao->id]['visibility_label'] = $visibilityLabels[$optionValue[$dao->id]['visibility_id']];
            }

        }
        return $optionValue;
    }

    /**
     * Function to add/edit option-value of a particular group
     *
     * @param  array     $params           Array containing exported values from the invoking form.
     * @param  array     $groupParams      Array containing group fields whose option-values is to retrieved/saved.
     * @param  string    $orderBy          for orderBy clause
     * @param  integer   $optionValueID    has the id of the optionValue being edited, disabled ..etc
     *
     * @return array of option-values     
     * 
     * @access public
     * @static
     */
    static function addOptionValue( &$params, &$groupParams, &$action, &$optionValueID ) 
    {
        $params['is_active'] =  CRM_Utils_Array::value( 'is_active', $params, false );
        // checking if the group name with the given id or name (in $groupParams) exists
        if (! empty($groupParams)) {
            $config =& CRM_Core_Config::singleton( );
            $groupParams['is_active']   = 1;
            $optionGroup = CRM_Core_BAO_OptionGroup::retrieve($groupParams, $defaults);
        }

        // if the corresponding group doesn't exist, create one, provided $groupParams has 'name' in it.
        if (! $optionGroup->id) {
            if ( $groupParams['name'] ) {
                $newOptionGroup = CRM_Core_BAO_OptionGroup::add($groupParams, $defaults);
                $params['weight'] = 1;
                $optionGroupID = $newOptionGroup->id;
            }
        } else {
            $optionGroupID = $optionGroup->id;
            $oldWeight = null;
            if ($optionValueID) {
                $oldWeight = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $optionValueID, 'weight', 'id' );
            }
            $fieldValues = array('option_group_id' => $optionGroupID);
            require_once 'CRM/Utils/Weight.php';        
            $params['weight'] =
                CRM_Utils_Weight::updateOtherWeights('CRM_Core_DAO_OptionValue', $oldWeight, $params['weight'], $fieldValues);
        }
        $params['option_group_id'] = $optionGroupID;
        
        require_once 'CRM/Core/Action.php';  
        if ( ($action & CRM_Core_Action::ADD) && !CRM_Utils_Array::value( 'value', $params ) ) {
            $fieldValues = array('option_group_id' => $optionGroupID);
            // use the next available value
            /* CONVERT(value, DECIMAL) is used to convert varchar
               field 'value' to decimal->integer                    */
            $params['value'] = (int) CRM_Utils_Weight::getDefaultWeight('CRM_Core_DAO_OptionValue', 
                                                                        $fieldValues, 
                                                                        'CONVERT(value, DECIMAL)');
        }
        if ( ! $params['label'] && $params['name'] ) {
            $params['label'] = $params['name'];
        }

        // set name to label if it's not set - but *only* for ADD action (CRM-3522)
        if (($action & CRM_Core_Action::ADD) && !CRM_Utils_Array::value( 'name', $params ) && $params['label'] ) {
            $params['name'] = $params['label'];
        }
        if ( $action & CRM_Core_Action::UPDATE ) {
            $ids['optionValue'] = $optionValueID;
        }
        $optionValue = CRM_Core_BAO_OptionValue::add($params, $ids);
        return $optionValue;
    }

    /**
     * Check if there is a record with the same name in the db
     *
     * @param string $value     the value of the field we are checking
     * @param string $daoName   the dao object name
     * @param string $daoID     the id of the object being updated. u can change your name
     *                          as long as there is no conflict
     * @param string $fieldName the name of the field in the DAO
     *
     * @return boolean     true if object exists
     * @access public
     * @static
     */
    static function optionExists( $value, $daoName, $daoID, $optionGroupID, $fieldName = 'name' ) 
    {
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
        eval( '$object =& new ' . $daoName . '( );' );
        $object->$fieldName      = $value;
        $object->option_group_id = $optionGroupID;

        if ( $object->find( true ) ) {
            return ( $daoID && $object->id == $daoID ) ? true : false;
        } else {
            return true;
        }
    }

    /**
     * Check if there is a record with the same name in the db
     *
     * @param string $value     the value of the field we are checking
     * @param string $daoName   the dao object name
     * @param string $daoID     the id of the object being updated. u can change your name
     *                          as long as there is no conflict
     * @param string $fieldName the name of the field in the DAO
     *
     * @return boolean     true if object exists
     * @access public
     * @static
     */
    static function getFields( $mode = '') 
    {
        if ( !self::$_fields || ! CRM_Utils_Array::value( $mode, self::$_fields ) || $mode) {
            if ( !self::$_fields ) {
                self::$_fields = array();
            }
            require_once "CRM/Core/DAO/OptionValue.php";
            $option = CRM_Core_DAO_OptionValue::import( );
            
            foreach (array_keys( $option ) as $id ) {
                $optionName = $option[$id];
            }
            
            if( $mode == 'contribute' ) {
                $nameTitle = array('payment_instrument' => array('name' =>'payment_instrument',
                                                                 'title'=> 'Payment Instrument',
                                                                 'headerPattern' => '/^payment|(p(ayment\s)?instrument)$/i'
                                                                 )
                                   );
            } else if ( $mode == '' ) {
                $nameTitle = array('gender'            => array('name' => 'gender',
                                                                'title'=> 'Gender',
                                                                'headerPattern' => '/^gender$/i'
                                                                ),
                                   'individual_prefix' => array('name' => 'individual_prefix',
                                                                'title'=> 'Individual Prefix',
                                                                'headerPattern' => '/^(prefix|title)/i'
                                                                ),
                                   'individual_suffix' => array('name' => 'individual_suffix',
                                                                'title'=> 'Individual Suffix',
                                                                'headerPattern' => '/^suffix$/i'
                                                                ),
                                   'greeting_type' => array('name' => 'greeting_type',
                                                            'title'=> 'Greeting Type'
                                                                )                                     
                                   );
            }

            if ( is_array( $nameTitle ) ) {
                foreach ( $nameTitle as $name => $attribs ) {
                    self::$_fields[$mode][$name] = $optionName;
                    list( $tableName, $fieldName ) = explode( '.', $optionName['where'] );  
                    // not sure of this fix, so keeping it commented for now
                    // this is from CRM-1541
                    // self::$_fields[$mode][$name]['where'] = $name . '.' . $fieldName;
                    self::$_fields[$mode][$name]['where'] = "{$name}.label";
                    foreach ( $attribs as $key => $val ) {
                        self::$_fields[$mode][$name][$key] = $val;
                    }
                }
            }
        }

        return self::$_fields[$mode];
    }
    
    /** 
     * build select query in case of option-values
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {
        if ( ! empty( $query->_params ) || ! empty( $query->_returnProperties ) ) {
            $field =& self::getFields();
            foreach ( $field as $name => $title ) {
                list( $tableName, $fieldName ) = explode( '.', $title['where'] ); 
                if ( CRM_Utils_Array::value( $name, $query->_returnProperties ) ) {
                    $query->_select["{$name}_id"]  = "{$name}.value as {$name}_id";
                    $query->_element["{$name}_id"] = 1;
                    $query->_select[$name] = "{$name}.{$fieldName} as $name";
                    $query->_tables[$tableName] = 1;
                    $query->_element[$name] = 1;
                }
            }
        }
    }
    
    /**
     * Function to return option-values of a particular group
     *
     * @param  array     $groupParams   Array containing group fields
     *                                  whose option-values is to retrieved.
     * @param  array     $values        (referance) to the array which
     *                                  will have the values for the group
     * @param  string    $orderBy       for orderBy clause
     * 
     * @param  boolean   $isActive      do you want only active option values?
     * @return array of option-values     
     * 
     * @access public
     * @static
     */
    static function getValues( $groupParams, &$values, $orderBy = 'weight', $isActive = false ) 
    {
        $select = "
SELECT 
   option_value.id          as id,
   option_value.label       as label,
   option_value.value       as value,
   option_value.name        as name,
   option_value.description as description,
   option_value.weight      as weight,
   option_value.is_active   as is_active,
   option_value.is_default  as is_default";
        
        $from = "
FROM
   civicrm_option_value  as option_value,
   civicrm_option_group  as option_group ";
        
        $where = " WHERE option_group.id = option_value.option_group_id ";
        
        if ( $isActive) {
            $where .= " AND option_value.is_active = " . $isActive;
        }
                
        $order = " ORDER BY " . $orderBy;
        
        if ( CRM_Utils_Array::value( 'id', $groupParams ) ) {
            $where .= " AND option_group.id = %1";
            $params[1] = array( $groupParams['id'], 'Integer' );
        }
        
        if ( CRM_Utils_Array::value( 'name', $groupParams ) ) {
            $where .= " AND option_group.name = %2";
            $params[2] = array( $groupParams['name'], 'String' );
        }
        
        $query = $select . $from . $where . $order;
        
        $dao =& CRM_Core_DAO::executeQuery( $query, $params );
        
        while( $dao->fetch( ) ) {
            $values[$dao->id] = array( 'id'          => $dao->id, 
                                       'label'       => $dao->label,
                                       'value'       => $dao->value,
                                       'name'        => $dao->name,
                                       'description' => $dao->description,
                                       'weight'      => $dao->weight,
                                       'is_active'   => $dao->is_active,
                                       'is_default'  => $dao->is_default );
        }
    }
}
