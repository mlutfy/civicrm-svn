<?php

/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */


require_once 'CRM/Form.php';
require_once 'CRM/SelectValues.php';
require_once 'CRM/Contact/Form/Contact.php';
require_once 'CRM/Contact/Form/Location.php';

/**
 * This class is used for building the Add Organization form. This class also has the actions that should be done when form is processed.
 * 
 * This class extends the variables and methods provided by the class CRM_Form which by default extends the HTML_QuickForm_SinglePage.
 * @package CRM.  
 */
class CRM_Contact_Form_Organization extends CRM_Form 
{
    
    /**
     * This is the constructor of the class.
     *
     * This function calls the constructor for the parent class CRM_Form and implements a switch strategy to identify the mode type 
     * of form to be displayed based on the values contained in the mode parameter.
     *
     * @access public
     * @param string $name Contains name of the form.
     * @param string $state Used to access the current state of the form.
     * @param constant $mode Used to access the type of the form. Default value is MODE_NONE.
     * @return None
     */
    function __construct($name, $state, $mode = self::MODE_NONE) 
    {
        parent::__construct($name, $state, $mode);
    }
    


    /**
     * In this function we build the Organization.php. All the quickform components are defined in this function.
     * 
     * This function implements a switch strategy using the form public variable $mode to identify the type of mode rendered by the 
     * form. The types of mode could be either Contact CREATE, VIEW, UPDATE, DELETE or SEARCH. 
     * CREATE and SEARCH forms can be implemented in a mini format using mode MODE_ADD_MINI and MODE_SEARCH_MINI. 
     * This function adds a default text element to the form whose value indicates the mode rendered by the form. It further calls the 
     * corresponding function build<i>mode</i>Form() to provide dynamic HTML content and is passed to the renderer in an  array format.
     * 
     * @access public
     * @return None
     * @see _buildAddForm( ) 
     */
    function buildQuickForm( )
    {
        switch ($this->_mode) {
        case self::MODE_ADD:
            $this->addElement('text', 'mode', self::MODE_ADD);
            $this->_buildAddForm();
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            

        } // end of switch
    }


    /**
     * This function sets the default values to the specified form element.
     * 
     * The function uses the $default array to load default values for element names provided as keys. It further calls the setDefaults 
     * method of the HTML_QuickForm by passing to it the array. 
     * This function differentiates between different mode types of the form by implementing the switch strategy based on the form mode 
     * variable.
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        $defaults = array();

        switch ($this->_mode) {
        case self::MODE_ADD:
        $defaults['organization_name'] = 'CRM Organization';
        $this->setDefaults($defaults);
        break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            
        }
    }



    /**
     * This function is used to validate the contact.
     * 
     * The function implements a server side validation of the entered contact_id. This value must have an entry in the contact_id
     * field of the crm_contact table for the validation to succed.
     *
     * @param string $value The value of the contact_id entered in the text field.
     * @return Boolean value true or false depending on whether the contact_id is valid or invalid. 
     * @see addRules( )     
     */
    function valid_contact($value) 
    {
        $contact = new CRM_Contact_DAO_Contact();
        if ($contact->get('id', $value)) {
            return true;
        } else {
            return false;
        }    
    }



    /**
     * This function is used to add the rules for form.
     * 
     * This function is used to add filters using applyFilter(), which filters the element value on being submitted. 
     * Rules of validation for form elements are established using addRule() or addGroupRule() QuickForm methods. Validation can either
     * be at the client by default javascript added by QuickForm, or at the server.  
     * Any custom rule of validation is set here using the registerRule() method. In this file, the custom validation function for 
     * contact_id is set by registering the rule check_contactid which calls the valid_contact function for date validation.
     * This function differentiates between different mode types of the form by implementing the switch functionality based on the
     * value of the class variable $mode.  
     * 
     * @return None
     * @access public
     * @see valid_date 
     */
    function addRules( ) 
    {
        $this->applyFilter('_ALL_', 'trim');

        switch ($this->_mode) {
        case self::MODE_ADD:
        $this->applyFilter('organization_name', 'trim');
        $this->addRule('organization_name', t(' Organization name is a required feild.'), 'required', null, 'client');
        $this->addRule('primary_contact_id', t(' Contact id is a required feild.'), 'required', null, 'client');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'numeric', null, 'client');
        $this->registerRule('check_contactid', 'callback', 'valid_contact','CRM_Contact_Form_Organization');
        $this->addRule('primary_contact_id', t(' Enter valid contact id.'), 'check_contactid');
        
        $this->addGroupRule('location', array('email_1' => array( 
                                                               array(t( 'Please enter valid email for location'), 'email', null, 'client')),
                                              'email_2' => array( 
                                                                         array(t( ' Please enter valid secondary email for location'), 'email', null, 'client')),
                                              'email_3' => array( 
                                                                        array(t( ' Please enter valid tertiary email for location' ), 'email', null, 'client'))
                                              )
                            ); 

        break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            
        }    
        
    }

       

    /**
     * This function provides the HTML form elements for the add operation of individual contact form.
     * 
     * This function is called by the buildQuickForm method, when the value of the $mode class variable is set to MODE_ADD
     * The addElement and addGroup method of HTML_QuickForm is used to add HTML elements to the form which is referenced using the $this 
     * form handle. Also the default values for the form elements are set in this function.
     * 
     * @access private
     * @return None 
     * @uses CRM_SelectValues Used to obtain static array content for setting select values for select element.
     * @uses CRM_Contact_Form_Location::buildLocationBlock($this, 3) Used to obtain the HTML element for plugging the Location block. 
     * @uses CRM_Contact_Form_Contact::buildCommunicationBlock($this) Used to obtain elements for plugging the Communication preferences.
     * @see buildQuickForm()         
     * @see _buildMiniAddForm()
     * 
     */
    function _buildAddForm()
    {

        $form_name = $this->getName();

        $this->addDefaultButtons( array(
                                        array ( 'type'      => 'next'  ,
                                                'name'      => 'Save'  ,
                                                'isDefault' => true     ),
                                        array ( 'type'      => 'reset' ,
                                                'name'      => 'Reset'  ),
                                        array ( 'type'       => 'cancel',
                                                'name'      => 'Cancel' ),
                                       )
                                 );

        // organization_name
        $this->addElement('text', 'organization_name', 'Organization Name:', array('maxlength' => 64));

        // legal_name
        $this->addElement('text', 'legal_name', 'Legal Name:', array('maxlength' => 64));

        // nick_name
        $this->addElement('text', 'nick_name', 'Nick Name:', array('maxlength' => 64));

        // primary_contact_id
        $this->addElement('text', 'primary_contact_id', 'Primary Contact Id:', array('maxlength' => 10));
        
        // sic_code
        $this->addElement('text', 'sic_code', 'SIC Code:', array('maxlength' => 8));
 
        // Implementing the communication preferences block
        CRM_Contact_Form_Contact::buildCommunicationBlock($this);

        // Implementing the location block :
        $location = CRM_Contact_Form_Location::buildLocationBlock($this, 1);
        $this->addGroup($location[1],'location');
        /* End of locations */

        $java_script = "<script type = \"text/javascript\">
                        frm = document." . $form_name .";</script>";

        $this->addElement('static', 'my_script', $java_script);

    }//ENDING BUILD FORM 


    /**
     * This function is used to call appropriate process function when a form is submitted.
     * 
     * The function implements a switch functionality to differentiate between different mode types of the form. It is based on the 
     * value of the $mode form class variable. 
     * @internal Possible mode options being MODE_ADD, MODE_VIEW, MODE_UPDATE, MODE_DELETE, MODE_ADD_MINI, MODE_SEARCH_MINI.
     * The process works as follows:
     * <code>
     * switch ($this->_mode) {
     *        case self::MODE_ADD:
     *             $this->_Add_postProcess();
     *             break;
     *        case self::MODE_ADD_MINI:
     *             $this->_MiniAdd_postProcess();
     *             break; 
     *        case ..
     *        ..
     *        ..
     * }         
     * </code>
     * 
     * @access public
     * @return None
     */
     function postProcess(){

        switch ($this->_mode) {
        case self::MODE_ADD:
            $this->_Add_postProcess();
            break;
        case self::MODE_VIEW:
            break;
        case self::MODE_UPDATE:
            break;
        case self::MODE_DELETE:
            break;            
        }    
    }
    

    /**
     * This function does all the processing of the form for New Contact Organization.
     * @access private
     */
    private function _Add_postProcess() 
    { 
        $str_error = ""; // error is recorded  if there are any errors while inserting in database         
        
        // create a object for inserting data in contact table 
        $contact = new CRM_Contact_DAO_Contact();
        
        $contact->domain_id = 1;
        // $contact->contact_type = $this->exportValue('contact_type');
        $contact->contact_type = 'Organization';
        $contact->sort_name = $this->exportValue('organization_name');
        //$contact->source = $this->exportValue('source');
        $contact->preferred_communication_method = $this->exportValue('preferred_communication_method');
        $contact->do_not_phone = $this->exportValue('do_not_phone');
        $contact->do_not_email = $this->exportValue('do_not_email');
        $contact->do_not_mail = $this->exportValue('do_not_mail');
        //$contact->hash = $this->exportValue('hash');
        
        $contact->query('BEGIN'); //begin the database transaction
        
        if (!$contact->insert()) {
            $str_error = mysql_error();
        }
        
        if(!strlen($str_error)){ //proceed if there are no errors
            // create a object for inserting data in contact organization table 
            $contact_organization = new CRM_Contact_DAO_Contact_Organization();

            $contact_organization->contact_id = $contact->id;
            $contact_organization->organization_name = $this->exportValue('organization_name');
            $contact_organization->legal_name = $this->exportValue('legal_name');
            $contact_organization->legal_identifier = $this->exportValue('legal_identifier');
            $contact_organization->nick_name = $this->exportValue('nick_name');
            $contact_organization->sic_code = $this->exportValue('sic_code');
            $contact_organization->primary_contact_id = $this->exportValue('primary_contact_id');
           
            if(!$contact_organization->insert()) {
                $str_error = mysql_error();
            }
        }
                 
                 
        if(!strlen($str_error)){ //proceed if there are no errors  
            // create a object for inserting data in crm_location, crm_email, crm_im, crm_phone table 

            //create a object of location class
            $varname = "contact_location";
            $varname1 = "location";
                         
            $a_Location =  $this->exportValue($varname1);
                         
            if (strlen(trim($a_Location['street_address'])) > 0  || strlen(trim($a_Location['email_1'])) > 0 || strlen(trim($a_Location['phone_1'])) > 0) {
                             
                if(!strlen($str_error)){ //proceed if there are no errors
                    // create a object of crm location
                    $$varname = new CRM_Contact_DAO_Location();
                    $$varname->contact_id = $contact->id;
                    $$varname->is_primary = $a_Location['is_primary'];
                    $$varname->location_type_id = $a_Location['location_type_id'];
                    
                    if(!$$varname->insert()) {
                        $str_error = mysql_error();
                    }
                }
                
                if(!strlen($str_error)){ //proceed if there are no errors
                    if (strlen(trim($a_Location['street_address'])) > 0) {
                        //create the object of crm address
                        $varaddress = "contact_address".$lngi;
                        $$varaddress = new CRM_Contact_DAO_Address();
                        
                        $$varaddress->location_id = $$varname->id;
                        $$varaddress->street_address = $a_Location['street_address'];
                        $$varaddress->supplemental_address_1 = $a_Location['supplemental_address_1'];
                        $$varaddress->city = $a_Location['city'];
                        // $$varaddress->county_id = $a_Location['county_id'];
                        $$varaddress->county_id = 1;
                        $$varaddress->state_province_id = $a_Location['state_province_id'];
                        $$varaddress->postal_code = $a_Location['postal_code'];
                        $$varaddress->usps_adc = $a_Location['usps_adc'];
                        $$varaddress->country_id = $a_Location['country_id'];
                        $$varaddress->geo_code1 = $a_Location['geo_code1'];
                        $$varaddress->geo_code2 = $a_Location['geo_code2'];
                        $$varaddress->address_note = $a_Location['address_note'];
                        $$varaddress->timezone = $a_Location['timezone'];
                        
                        if(!$$varaddress->insert()) {
                            $str_error = mysql_error();
                        }
                    }              
                }
                
                
                if(!strlen($str_error)){ //proceed if there are no errors
                    //create the object of crm email
                    for ($lng_i= 1; $lng_i <= 3; $lng_i++) {
                        $varemail = "email_".$lng_i;
                        if (strlen(trim($a_Location[$varemail])) > 0) {
                            $var_email = "contact_email".$lng_i;
                            $$var_email = new CRM_Contact_DAO_Email();
                            
                            if($lng_i == 1) { //make first email entered primary
                                $$var_email->is_primary = 1;
                            } else {
                                $$var_email->is_primary = 0;
                            }
                            
                            $$var_email->location_id = $$varname->id;
                            $$var_email->email = $a_Location[$varemail];
                            
                            if(!$$var_email->insert()) {
                                $str_error = mysql_error();
                                break;
                            }    
                        }  
                    }
                }
                
                if(!strlen($str_error)){ //proceed if there are no errors
                    //create the object of crm phone
                    for ($lng_i= 1; $lng_i <= 3; $lng_i++) {
                        $varphone = "phone_".$lng_i;
                        $varphone_type = "phone_type_".$lng_i;
                        $varmobile_prov_id = "mobile_provider_id_".$lng_i;
                        if (strlen(trim($a_Location[$varphone])) > 0) {
                            $var_phone = "contact_phone".$lng_i;
                            $$var_phone = new CRM_Contact_DAO_Phone();
                            
                            if($lng_i == 1) { //make first phone entered primary
                                $$var_phone->is_primary = 1;
                            } else {
                                $$var_phone->is_primary = 0;
                            }
                            
                            $$var_phone->location_id = $$varname->id;
                            $$var_phone->phone = $a_Location[$varphone];
                            $$var_phone->phone_type = $a_Location[$varphone_type];
                            // $$var_phone->mobile_provider_id = $a_Location[$varmobile_prov_id];
                            $$var_phone->mobile_provider_id = 1;
                            
                            
                            if(!$$var_phone->insert()) {
                                $str_error = mysql_error();
                                break;
                            }    
                        }  
                    }
                }
                
                
                if(!strlen($str_error)){ //proceed if there are no errors
                    //create the object of crm im
                    for ($lng_i= 1; $lng_i <= 3; $lng_i++) {
                        $var_service = "im_service_id_".$lng_i;
                        $var_screenname = "im_screenname_".$lng_i;
                        if (strlen(trim($a_Location[$var_screenname])) > 0) {
                            $var_im = "contact_im" . $lng_i;
                            $$var_im = new CRM_Contact_DAO_IM();
                            
                            if ($lng_i == 1) { //make first im entered primary
                                $$var_im->is_primary = 1;
                            } else {
                                $$var_im->is_primary = 0;
                            }
                            
                            $$var_im->location_id = $$varname->id;
                            $$var_im->im_service_id = $a_Location[$var_service];
                            $$var_im->im_screenname = $a_Location[$var_screenname];
                            
                            if (!$$var_im->insert()) {
                                $str_error = mysql_error();
                                break;
                            }    
                        }  
                    }
                }  
                
            }// end of if block    
            
        } 
        // check if there are any errors while inserting in database
        
        if(strlen($str_error)){ //commit if there are no errors else rollback
            $contact->query('ROLLBACK');
            form_set_error('organization_name', t($str_error));
        } else {
            $contact->query('COMMIT');
            form_set_error('organization_name', t('Contact Organization has been added successfully.'));
        }
        
    }//end of function

}

?>