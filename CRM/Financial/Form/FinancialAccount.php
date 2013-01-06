<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
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
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

/**
 * This class generates form components for Financial Account
 * 
 */
class CRM_Financial_Form_FinancialAccount extends CRM_Contribute_Form {

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm( ) {
    parent::buildQuickForm( );
    $dataURL = CRM_Utils_System::url( 'civicrm/ajax/rest',
      'className=CRM_Contact_Page_AJAX&fnName=getContactList&json=1&context=contact&org=1', false, null, false );
    $this->assign('dataURL', $dataURL);

    if ($this->_action & CRM_Core_Action::DELETE) {
      return;
    }
    
    $this->applyFilter('__ALL__', 'trim');
    $attributes = CRM_Core_DAO::getAttribute('CRM_Financial_DAO_FinancialAccount');
    $this->add('text', 'name', ts('Name'), $attributes['name'],true);
    $this->addRule('name', ts('A financial type with this name already exists. Please select another name.'),
      'objectExists', array('CRM_Financial_DAO_FinancialAccount', $this->_id));
    
    $this->add('text', 'description', ts('Description'), $attributes['description']);
    $this->add('text', 'accounting_code', ts('Accounting Code'), $attributes['accounting_code']);
    $this->add('text', 'contact_name', ts('Owner'), $attributes['name']);
    $this->add('hidden', 'contact_id', '', array('id' => 'contact_id'));
    $this->add('text', 'tax_rate', ts('Tax Rate'), $attributes['tax_rate']);
    $this->add('checkbox', 'is_deductible', ts('Tax-Deductible?'));
    $this->add('checkbox', 'is_active', ts('Enabled?'));
    $this->add('checkbox', 'is_tax', ts('Is Tax?'));
    $this->add('checkbox', 'is_default', ts('Default?'));
    
    $financialAccountType = CRM_Core_PseudoConstant::accountOptionValues('financial_account_type');
    if (!empty($financialAccountType)) {
      $this->add('select', 'financial_account_type_id', ts('Financial Account Type'),
        array('select' => '- select -') + $financialAccountType);
    }
    
    if ($this->_action == CRM_Core_Action::UPDATE &&
      CRM_Core_DAO::getFieldValue('CRM_Financial_DAO_FinancialAccount', $this->_id, 'is_reserved')) {
      $this->freeze(array('name', 'description', 'is_active'));
    }
    $this->addFormRule(array('CRM_Financial_Form_FinancialAccount', 'formRule'), $this);
  }
  
  /**
   * global validation rules for the form
   *
   * @param array $fields posted values of the form
   *
   * @return array list of errors to be posted back to the form
   * @static
   * @access public
   */
  static function formRule( $values, $files, $self ) {
    $errorMsg = array( );
    if (!empty( $values['tax_rate'])) {
      if ($values['tax_rate'] <= 0 || $values['tax_rate'] > 100) {
        $errorMsg['tax_rate'] = ts('Tax Rate Should be between 0 - 100');
      }
    }
    return CRM_Utils_Array::crmIsEmptyArray( $errorMsg ) ? true : $errorMsg;
  }
  
  /**
   * This function sets the default values for the form.
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @return None
   */
  function setDefaultValues() {
    $defaults = parent::setDefaultValues();
    if ($this->_action & CRM_Core_Action::ADD) {
      $defaults['contact_id'] = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Domain', CRM_Core_Config::domainID(), 'contact_id');
      $defaults['contact_name'] = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $defaults['contact_id'], 'sort_name');
    }
    return $defaults;
  }
  
  /**
   * Function to process the form
   *
   * @access public
   * @return None
   */
  public function postProcess() {
    if ($this->_action & CRM_Core_Action::DELETE) {
      CRM_Financial_BAO_FinancialAccount::del($this->_id);
      CRM_Core_Session::setStatus( ts('Selected Financial Account has been deleted.') );
    }
    else {
      $ids = array( );
      // store the submitted values in an array
      $params = $this->exportValues();
      
      if ($this->_action & CRM_Core_Action::UPDATE) {
        $ids['contributionType'] = $this->_id;
      }
      
      $contributionType = CRM_Financial_BAO_FinancialAccount::add($params, $ids);
      CRM_Core_Session::setStatus(ts('The Financial Account \'%1\' has been saved.', array(1 => $contributionType->name)));
    }
  }
}


