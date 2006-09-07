<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Admin/Form.php';
require_once 'CRM/Core/BAO/OptionValue.php';
require_once 'CRM/Core/BAO/OptionGroup.php';

/**
 * This class generates form components for Gender
 * 
 */
class CRM_Admin_Form_Gender extends CRM_Admin_Form
{

    /**
     * Function to for pre-processing
     *
     * @return None
     * @access public
     */
    public function preProcess( ) {
        parent::preProcess( );
        $groupParams = array( 'name' => 'gender' );
        $optionGroup = CRM_Core_BAO_OptionGroup::retrieve($groupParams, $dnc);
        $this->_gid = $optionGroup->id;
    }

    /**
     * This function sets the default values for the form. 
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = parent::setDefaultValues( );
        
        if (! $defaults['weight']) {
            if ($this->_gid) {
                $query = "SELECT max( `weight` ) as weight FROM `civicrm_option_value` where option_group_id=" . $this->_gid;
                $dao =& new CRM_Core_DAO( );
                $dao->query( $query );
                if ($dao->fetch()) {
                    $defaults['weight'] = ($dao->weight + 1);
                }
            } else {
                $defaults['weight'] = 1;
            }
        }
        return $defaults;
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }
        
        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_OptionValue', 'name' ) );
        $this->addRule( 'name', ts('Please enter a valid gender name.'), 'required' );
        $this->addRule( 'name', ts('Name already exists in Database.'), 'optionExists', array( 'CRM_Core_DAO_OptionValue', $this->_id, $this->_gid ) );
        
        $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_OptionValue', 'weight'), true);
        $this->addRule('weight', ts(' is a numeric field') , 'numeric');
        
        $this->add('checkbox', 'is_active', ts('Enabled?'));
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        if($this->_action & CRM_Core_Action::DELETE) {
            if(CRM_Core_BAO_OptionValue::del($this->_id)) {
                CRM_Core_Session::setStatus( ts('Selected Gender type has been deleted.') );
            } else {
                CRM_Core_Session::setStatus( ts('Selected Gender type has not been deleted.') );
            }
        } else {
            $params = $ids = array( );
            $params = $this->exportValues();
            
            $groupParams = array( 'name' => 'gender' );
            
            require_once 'CRM/Core/OptionValue.php';
            $optionValue = CRM_Core_OptionValue::addOptionValue($params, $groupParams, $this->_action, $this->_id);

            CRM_Core_Session::setStatus( ts('The Gender "%1" has been saved.', array( 1 => $optionValue->name )) );
        }
    }
}

?>
