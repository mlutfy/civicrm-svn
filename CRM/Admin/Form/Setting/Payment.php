<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Admin/Form/Setting.php';

/**
 * This class generates form components for Online Payment
 * 
 */
class CRM_Admin_Form_Setting_Payment extends CRM_Admin_Form_Setting
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        CRM_Utils_System::setTitle(ts('Settings - Payment Processor'));

        $this->addYesNo( 'enableSSL', ts( 'Force Secure URLs (SSL)' ));
        $processor = CRM_Core_SelectValues::paymentProcessor();
        $extra1 = array( 'onclick' => "return showHideByValue('paymentProcessor', 'PayPal|PayPal_Express', 'certificate_path', 'block', 'select', false);" );
        $this->addElement('select','paymentProcessor', ts('Payment Processor'), array('select' => '- select -') + $processor, $extra1);  
        $this->addElement('text','paymentExpressButton', ts('PayPal Express Button'));  
        $this->addElement('text','paymentUsername_test', ts('Username - TEST')); 
        $this->addElement('text','paymentCertPath_test', ts('Certificate Path - TEST')); 
        $this->addElement('text','paymentPayPalExpressTestUrl', ts('Paypal Url - TEST')); 
        $this->addElement('text','paymentUsername_live', ts('Username - LIVE')); 
        $this->addElement('text','paymentCertPath_live', ts('Certificate Path - LIVE')); 
        $this->addElement('text','paymentPayPalExpressUrl', ts('Paypal Url - LIVE'));
        
        parent::buildQuickForm();
    }
}

?>