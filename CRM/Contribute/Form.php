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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components generic to Mobile provider
 * 
 */
class CRM_Contribute_Form extends CRM_Core_Form
{
    /**
     * The id of the object being edited / created
     *
     * @var int
     */
    protected $_id;

    /**
     * The name of the BAO object for this form
     *
     * @var string
     */
    protected $_BAOName;

    function preProcess( ) {
        $this->_id      = $this->get( 'id'      );
        $this->_BAOName = $this->get( 'BAOName' );
    }

    /**
     * This function sets the default values for the form. MobileProvider that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) {
        $defaults = array( );
        $params   = array( );
        

        if ( isset( $this->_id ) ) {
            $params = array( 'id' => $this->_id );
            require_once(str_replace('_', DIRECTORY_SEPARATOR, $this->_BAOName) . ".php");
            eval( $this->_BAOName . '::retrieve( $params, $defaults );' );
        }
        if ($this->_action == CRM_Core_Action::DELETE) {
            $this->assign( 'delName', $defaults['name'] );
        } elseif ($this->_action == CRM_Core_Action::ADD) {
            $condition = " AND is_default = 1";
            $values = CRM_Core_OptionGroup::values( 'activity_contacts', false, false, false, $condition );
            $defaults['financial_account_type_id'] = array_keys( $values );
            $defaults['is_active'] = 1;
            $defaults['is_default'] = 1;
        }elseif ($this->_action & CRM_Core_Action::UPDATE){
            $organisationId = CRM_Core_DAO::getFieldValue( 'CRM_Financial_DAO_FinancialAccount', $this->_id, 'contact_id' );
            $this->assign('organisationId', $organisationId );                            
        }
        
        return $defaults;
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
     
        if($this->_action & CRM_Core_Action::DELETE) {
            $this->addButtons(array(
                                    array ('type'      => 'next',
                                           'name'      => ts('Delete'),
                                           'isDefault' => true),
                                    array ('type'      => 'cancel',
                                           'name'      => ts('Cancel')),
                                    )
                              );
        }
   
    }

}


