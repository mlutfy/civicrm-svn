<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * Application Form Base Class
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * Base class for the application form
 * 
 */
class CRM_Quest_Form_App extends CRM_Core_Form
{
    const
        TEST_ACT  = 1,
        TEST_PSAT = 2,
        TEST_SAT  = 4;


    /**
     * This function sets the default values for the form. For edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        return $defaults;
    }
    

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->assign       ( 'displayRecent' , false );
        $this->assign       ( 'welcome_name'  , $this->get('welcome_name'));
        $this->addDefaultButtons(ts('Save & Continue'));        
    }

       
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return void
     */
    public function postProcess() 
    {
    }//end of function

    /**
     * simple shell that derived classes can call to add buttons to
     * the form with a customized title for the main Submit
     *
     * @param string $title title of the main button
     * @param string $type  button type for the form after processing
     * @return void
     * @access public
     */
    function addDefaultButtons( $title, $nextType = 'next', $backType = 'back' ) {
        $this->addButtons( array(
                                 array ( 'type'      => $nextType,
                                         'name'      => $title,
                                         'isDefault' => true   ),
                                 array ( 'type'      => $backType,
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    function addSelect( $id, $label, $prefix = null, $required = null ) {
        if ($prefix) {
            $this->addElement('select', $id . '_id' . $prefix , $label,
                              array('' => ts('- select -')) + CRM_Core_OptionGroup::values($id) );
            if ( $required) {
                $this->addRule($id . '_id' . $prefix, ts("Please select $label"),'required');
            }
        } else {
            $this->addElement('select', $id. '_id' , $label,
                              array('' => ts('- select -')) + CRM_Core_OptionGroup::values($id) );
            if ( $required) {
                $this->addRule($id. '_id' , ts("Please select $label"),'required');
            }

        }
        
    }

    function addCountry( $id, $title ,$required = null ) {
        $this->addElement('select', $id, $title,
                          array('' => ts('- select -')) + CRM_Core_PseudoConstant::country( ) );
        if( $required ) {
            $this->addRule($id , ts("Please select $title"),'required');
        }

    }

    function addSelectOther( $id, $label, $options, &$attributes ,$required = null, $javascriptMethod = null) {
        
        if ($javascriptMethod) {
            $this->addElement('select', $id , $label, $options, $javascriptMethod);
        } else {
            $this->addElement('select', $id , $label, $options);
        }
        
        if( $required ) {
            $this->addRule($id,ts("Please select $label "),'required');
        }

        $this->addElement( 'text', $id . 'other', $label, $attributes[$id . 'other'] );
    }

    function buildAddressBlock( $locationId, $title, $phone, $alternatePhone  = null,$required = null ) {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address');

        $location[$locationId]['address']['street_address']         =
            $this->addElement('text', "location[$locationId][address][street_address]", $title,
                              $attributes['street_address']);
        if( $required ){
            $this->addRule("location[$locationId][address][street_address]" , ts("Please enter the Street Address for %1." , array( 1 => $title)),'required');
        }

        $location[$locationId]['address']['supplemental_address_1'] =
            $this->addElement('text', "location[$locationId][address][supplemental_address_1]", ts('Addt\'l Address 1'),
                              $attributes['supplemental_address_1']);
        $location[$locationId]['address']['supplemental_address_2'] =
            $this->addElement('text', "location[$locationId][address][supplemental_address_2]", ts('Addt\'l Address 2'),
                              $attributes['supplemental_address_2']);

        $location[$locationId]['address']['city']                   =
            $this->addElement('text', "location[$locationId][address][city]", ts('City'),
                              $attributes['city']);
        if( $required ){
            $this->addRule("location[$locationId][address][city]" , ts("Please enter the City for %1." , array( 1 => $title)),'required');
        }
        
        $location[$locationId]['address']['postal_code']            =
            $this->addElement('text', "location[$locationId][address][postal_code]", ts('Zip / Postal Code'),
                              $attributes['postal_code']);
        if( $required ){
            $this->addRule("location[$locationId][address][postal_code]" , ts("Please enter the Zip/Postal Code for %1." , array( 1 => $title)),'required');
        }
        $this->addRule( "location[$locationId][address][postal_code]", ts("Zip/Postal Code not valid for %1.", array( 1 => $title)), 'integer' );
        
        $location[$locationId]['address']['postal_code_suffix']            =
            $this->addElement('text', "location[$locationId][address][postal_code_suffix]", ts('Add-on Code'),
                              array( 'size' => 4, 'maxlength' => 12 ));
        $this->addRule( "location[$locationId][address][postal_code_suffix]", ts('Zip-Plus not valid.'), 'integer' );

        $location[$locationId]['address']['state_province_id']      =
             $this->addElement('select', "location[$locationId][address][state_province_id]", ts('State / Province'),
                               array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince());
        if( $required ){
             $this->addRule("location[$locationId][address][state_province_id]" , ts("Please select the State / Province for %1." , array( 1 => $title)),'required');
        }

        $location[$locationId]['address']['country_id']             =
             $this->addElement('select', "location[$locationId][address][country_id]", ts('Country'),
                               array('' => ts('- select -')) + CRM_Core_PseudoConstant::country());
        if( $required ){
            $this->addRule("location[$locationId][address][country_id]" , ts("Please select the Country for %1." , array( 1 => $title)),'required');
        }
        

         if ( $phone ) {
             $location[$locationId]['phone'][1]['phone_type'] = $this->addElement('select',
                                                                                  "location[$locationId][phone][1][phone_type]",
                                                                                  null,
                                                                                  CRM_Core_SelectValues::phoneType());
             
             $location[$locationId]['phone'][1]['phone']      = $this->addElement('text',
                                                                                  "location[$locationId][phone][1][phone]", 
                                                                                  $phone,
                                                                                  CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                                                                                             'phone'));
             if($required) {
                 $this->addRule("location[$locationId][phone][1][phone]",ts("Please enter a value for $phone"),'required');
             }
             $this->addRule("location[$locationId][phone][1][phone]",ts("Please enter a valid number"),'phone');
         }

         if ( $alternatePhone ) {
             $location[$locationId]['phone'][2]['phone_type'] = $this->addElement('select',
                                                                                  "location[$locationId][phone][2][phone_type]",
                                                                                  null,
                                                                                  CRM_Core_SelectValues::phoneType());
             
             
             $location[$locationId]['phone'][2]['phone']      = $this->addElement('text',
                                                                                  "location[$locationId][phone][2][phone]", 
                                                                                  $phoneTitle,
                                                                                  CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                                                                                             
                                                                                                   'phone'));
             if ($required) {
                 $this->addRule("location[$locationId][phone][2][phone]",ts("Please enter a value for $alternatePhone"),'required');
             }
         }
    }

}

?>