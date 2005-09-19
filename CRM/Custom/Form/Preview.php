<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Social Source Foundation (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for previewing custom data
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Custom_Form_Preview extends CRM_Core_Form
{
    /**
     * the group tree data
     *
     * @var array
     */
    protected $_groupTree;

    /**
     * pre processing work done here.
     *
     * gets session variables for group or field id
     *
     * @param none
     * @return none
     *
     * @access public
     *
     */
    function preProcess()
    {
        // get the controller vars
        $groupId  = $this->get('groupId');
        $fieldId  = $this->get('fieldId');
        
        if ($fieldId) {
            // field preview
            $defaults = array();
            $params = array('id' => $fieldId);
            $fieldDAO =& new CRM_Core_DAO_CustomField();                    
            CRM_Core_DAO::commonRetrieve('CRM_Core_DAO_CustomField', $params, $defaults);
            $this->_groupTree = array();
            $this->_groupTree[0]['id'] = 0;
            $this->_groupTree[0]['fields'] = array();
            $this->_groupTree[0]['fields'][$fieldId] = $defaults;
            $this->assign('preview_type', 'field');
        } else {
            // group preview
            $this->_groupTree  = CRM_Core_BAO_CustomGroup::getGroupDetail($groupId);        
            $this->assign('preview_type', 'group');
        }
    }


    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues()
    {
        $defaults = array();

        foreach ($this->_groupTree as $group) {
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
                $fieldId = $field['id'];
                $elementName = $groupId . '_' . $fieldId . '_' . $field['name'];
                $defaults[$elementName] = CRM_Utils_Array::value( 'default_value', $field );
                
                //handle checkboxes default checked
                if($field['html_type'] == 'CheckBox') {
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id']);
                    
                    $defaults[$elementName] = array();
                    $defaultCheckValue = CRM_Utils_Array::value( 'default_value', $field );
                    $checkedValue = explode(CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $defaultCheckValue);
                    foreach($customOption as $val) {
                        if ( in_array($val['value'], $checkedValue) ) {
                            $defaults[$elementName][$val['value']] = 1;
                        } else {
                            $defaults[$elementName][$val['value']] = 0;
                        }
                    }                            
                }              
            }
        }
        return $defaults;
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->assign('groupTree', $this->_groupTree);

        // add the form elements
        foreach ($this->_groupTree as $group) {
            $groupId = $group['id'];
            foreach ($group['fields'] as $field) {
                $fieldId = $field['id'];                
                $elementName = $groupId . '_' . $fieldId . '_' . $field['name']; 

                $elementData = CRM_Utils_Array::value( 'default_value', $field );

                switch($field['html_type']) {

                case 'Text':
                case 'TextArea':
                    $element = $this->add(strtolower($field['html_type']), $elementName, $field['label'], 
                                          CRM_Utils_Array::value( 'attributes', $field ),
                                          $field['is_required']);
                    break;

                case 'Select Date':
                    $this->add('date', $elementName, $field['label'], CRM_Core_SelectValues::date('custom'), $field['is_required']);
                    break;

                case 'Radio':
                     $choice = array();
                     if($field['data_type'] == "String" || $field['data_type'] == "Int" ||
                        $field['data_type'] == "Float"|| $field['data_type'] == "Money") {
                         $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id']);
                         foreach ($customOption as $v) {
                             $choice[] = $this->createElement(strtolower($field['html_type']), null, '',
                                                              $v['label'], $v['value'],
                                                              CRM_Utils_Array::value( 'attributes', $field ) );
                         }
                         $this->addGroup($choice, $elementName, $field['label']);
                     } else {
                         $choice[] = $this->createElement(strtolower($field['html_type']), null, '', 
                                                          ts('Yes'), 1,
                                                          CRM_Utils_Array::value( 'attributes', $field ) );
                         $choice[] = $this->createElement(strtolower($field['html_type']), null, '', 
                                                          ts('No') , 0,
                                                          CRM_Utils_Array::value( 'attributes', $field ) );
                         $this->addGroup($choice, $elementName, $field['label']);
                     }
                     if ($field['is_required']) {
                         $this->addRule($elementName, ts('%1 is a required field.', array(1 => $field['label'])) , 'required');
                     }
                     break;

                case 'Select':
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id']);
                    $selectOption = array();
                    foreach ($customOption as $v) {
                        $selectOption[$v['value']] = $v['label'];
                    }
                    $this->add('select', $elementName,$field['label'], $selectOption, $field['is_required']);
                    break;

                case 'CheckBox':
                    $customOption = CRM_Core_BAO_CustomOption::getCustomOption($field['id']);
                    $check = array();
                    foreach ($customOption as $v) {
                        $checked = array();
                        if ( $elementData == $v['value'] ) {
                            $checked = array('checked' => 'checked');
                        }
                            $check[] = $this->createElement('checkbox', $v['value'], null, $v['label'], $checked);                        
                    }
                    $this->addGroup($check, $elementName, $field['label']);
                    if ($field['is_required']) {
                         $this->addRule($elementName, ts('%1 is a required field.', array(1 => $field['label'])) , 'required');
                    }
                    break;
                    
                case 'Select State/Province':
                    $stateOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince();
                    $this->add('select', $elementName, $field['label'], $stateOption, $field['is_required']);
                    break;

                case 'Select Country':
                    $countryOption = array('' => ts('- select -')) + CRM_Core_PseudoConstant::country();
                    $this->add('select', $elementName, $field['label'], $countryOption, $field['is_required']);
                    break;
                }
                
                switch ( $field['data_type'] ) {
                case 'Int':
                    // integers will have numeric rule applied to them.
                    $this->addRule($elementName, ts('%1 must be an integer (whole number).', array(1 => $field['label'])), 'integer');
                    break;

                case 'Date':
                    $this->addRule($elementName, ts('%1 is not a valid date.', array(1 => $field['label'])), 'qfDate');
                    break;

                case 'Float':
                case 'Money':
                    $this->addRule($elementName, ts('%1 must be a number (with or without decimal point).', array(1 => $field['label'])), 'numeric');
                    break;
                }
            }
        }

        $this->addButtons(array(
                                array ('type'      => 'cancel',
                                       'name'      => ts('Done with Preview'),
                                       'isDefault' => true),
                                )
                          );
    }
}
?>
