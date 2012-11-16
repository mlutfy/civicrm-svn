<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
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
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id: $
 *
 */

/**
 *
 */
class CRM_SMS_Form_Provider extends CRM_Core_Form {
  protected $_id = NULL; function preProcess() {

    $this->_id = $this->get('id');

    CRM_Utils_System::setTitle(ts('Manage - SMS Providers'));

    if ($this->_id) {
      $refreshURL = CRM_Utils_System::url('civicrm/admin/sms/provider',
        "reset=1&action=update&id={$this->_id}",
        FALSE, NULL, FALSE
      );
    }
    else {
      $refreshURL = CRM_Utils_System::url('civicrm/admin/sms/provider',
        "reset=1&action=add",
        FALSE, NULL, FALSE
      );
    }

    $this->assign('refreshURL', $refreshURL);
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    if ($this->_action & CRM_Core_Action::DELETE) {
      $this->addButtons(array(
          array(
            'type' => 'next',
            'name' => ts('Delete'),
            'isDefault' => TRUE,
          ),
          array(
            'type' => 'cancel',
            'name' => ts('Cancel'),
          ),
        )
      );
      return;
    }
    else {
      $this->addButtons(array(
          array(
            'type' => 'next',
            'name' => ts('Save'),
            'isDefault' => TRUE,
          ),
          array(
            'type' => 'cancel',
            'name' => ts('Cancel'),
          ),
        )
      );
    }

    $attributes = CRM_Core_DAO::getAttribute('CRM_SMS_DAO_Provider');

    $providerNames = CRM_Core_OptionGroup::values('sms_provider_name', FALSE, FALSE, FALSE, NULL, 'label');
    $apiTypes = CRM_Core_OptionGroup::values('sms_api_type', FALSE, FALSE, FALSE, NULL, 'label');

    $this->add('select', 'name', ts('Name'), array('' => '- select -') + $providerNames, TRUE, array('onchange' => "reload(true)"));

    $this->add('text', 'title', ts('Title'),
      $attributes['title'], TRUE
    );

    $this->addRule('title', ts('This Title already exists in Database.'), 'objectExists', array('CRM_SMS_DAO_Provider', $this->_id));

    $this->add('text', 'username', ts('Username'),
      $attributes['username'], TRUE
    );

    $this->add('password', 'password', ts('Password'),
      $attributes['password'], TRUE
    );

    $this->add('select', 'api_type', ts('API Type'), $apiTypes, TRUE);

    $this->add('text', 'api_url', ts('API Url'), $attributes['api_url'], TRUE);

    $this->add('textarea', 'api_params', ts('API Parameters'),
      "cols=50 rows=6", TRUE
    );

    $this->add('checkbox', 'is_active', ts('Is this provider active?'));

    $this->add('checkbox', 'is_default', ts('Is this a default provider?'));
  }

  function setDefaultValues() {
    $defaults = array();

    $name = CRM_Utils_Request::retrieve('key', 'String', $this, FALSE, NULL);
    if ($name) {
      $defaults['name'] = $name;
      $info = CRM_Extension_System::singleton()->getMapper()->keyToInfo($name);
      if (is_a($info, 'CRM_Extension_Info'))
        $defaults['api_url'] = $info->typeInfo['api_url'];
    }

    if (!$this->_id) {
      $defaults['is_active'] = $defaults['is_default'] = 1;
      return $defaults;
    }

    $dao = new CRM_SMS_DAO_Provider();
    $dao->id = $this->_id;

    if ($name)
      $dao->name = $name;

    if (!$dao->find(TRUE)) {
      return $defaults;
    }

    CRM_Core_DAO::storeValues($dao, $defaults);

    return $defaults;
  }

  /**
   * Function to process the form
   *
   * @access public
   *
   * @return None
   */
  public function postProcess() {

    CRM_Utils_System::flushCache('CRM_SMS_DAO_Provider');

    if ($this->_action & CRM_Core_Action::DELETE) {
      CRM_SMS_BAO_Provider::del($this->_id);
      CRM_Core_Session::setStatus(ts('Selected Provider has been deleted.'), ts('Deleted'), 'success');
      return;
    }

    $recData = $values = $this->controller->exportValues($this->_name);
    $recData['is_active'] = CRM_Utils_Array::value('is_active', $recData, 0);
    $recData['is_default'] = CRM_Utils_Array::value('is_default', $recData, 0);

    if ($this->_action & CRM_Core_Action::UPDATE) {
      CRM_SMS_BAO_Provider::updateRecord($recData, $this->_id);
    }
    elseif ($this->_action & CRM_Core_Action::ADD) {
      CRM_SMS_BAO_Provider::saveRecord($recData);
    }
  }
}

