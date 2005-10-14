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
 | at http://www.openngo.org/faqs/licensing.html                      | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@yahoo.com> 
 * @copyright Donald A. Lobo 01/15/2005 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Profile/Selector/Listings.php';
require_once 'CRM/Core/Selector/Controller.php';
require_once 'CRM/Core/Page.php';

/**
 * This implements the profile page for all contacts. It uses a selector
 * object to do the actual dispay. The fields displayd are controlled by
 * the admin
 */
class CRM_Profile_Page_Listings extends CRM_Core_Page {

    /**
     * all the fields that are listings related
     *
     * @var array
     * @access protected
     */
    protected $_fields;

    /** 
     * the custom fields for this domain
     * 
     * @var array 
     * @access protected 
     */ 
    protected $_customFields;

    /**
     * The input params from the request
     *
     * @var array 
     * @access protected 
     */ 
    protected $_params;

    /**
     * extracts the parameters from the request and constructs information for
     * the selector object to do a query
     *
     * @return void 
     * @access public 
     * 
     */ 
    function preProcess( ) {
        require_once 'CRM/Core/BAO/UFGroup.php';
        $this->_fields = CRM_Core_BAO_UFGroup::getListingFields( CRM_Core_Action::UPDATE,
                                                                 CRM_Core_BAO_UFGroup::LISTINGS_VISIBILITY );
        $this->_customFields = CRM_Core_BAO_CustomField::getFieldsForImport( 'Individual' );

        $this->_params   = array( );
        foreach ( $this->_fields as $name => $field ) {
            $value = CRM_Utils_Request::retrieve( $name, $this, false, null, 'REQUEST' );
            $customField = CRM_Utils_Array::value( $name, $this->_customFields );
            if ( ! empty( $_POST ) && ! CRM_Utils_Array::value( $name, $_POST ) ) {
                if ( $customField ) {
                    // reset checkbox because a form does not send null checkbox values
                    if ( $customField['html_type'] == 'CheckBox' ) {
                        // only reset on a POST submission if we dont see any value
                        $value = null;
                        $this->set( $name, $value );
                    } else if ( ( $customField['html_type'] == 'Select' || $customField['html_type'] == 'Radio' ) &&
                                ( 0 == (int ) $value ) ) {
                        $value = null; 
                        $this->set( $name, $value ); 
                    }
                } else if ( $name == 'group' || $name == 'tag' ) {
                    $value = null;  
                    $this->set( $name, $value );  
                }
            }
            if ( isset( $value ) && $value != null ) {
                $this->_fields[$name]['value'] = $value;
                $this->_params[$name] = $value;
            } 
        }

   }

    /** 
     * run this page (figure out the action needed and perform it). 
     * 
     * @return void 
     */ 
    function run( ) {
        $this->preProcess( );

        $selector =& new CRM_Profile_Selector_Listings( $this->_params, $this->_customFields );
        $controller =& new CRM_Core_Selector_Controller($selector ,
                                                        $this->get( CRM_Utils_Pager::PAGE_ID ),
                                                        $this->get( CRM_Utils_Sort::SORT_ID  ),
                                                        CRM_Core_Action::VIEW, $this, CRM_Core_Selector_Controller::TEMPLATE );
        $controller->setEmbedded( true );
        $controller->run( );

        $formController =& new CRM_Core_Controller_Simple( 'CRM_Profile_Form_Search', ts('Search Profile'), CRM_Core_Action::ADD );
        $formController->setEmbedded( true );
        $formController->process( ); 
        $formController->run( ); 

        return parent::run( );

    }

}

?>
