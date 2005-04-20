<?php
/*
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 * Files required
 */
require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/PseudoConstant.php';
require_once 'CRM/Selector/Controller.php';
require_once 'CRM/Contact/Selector.php';

/**
 * Base Search / View form for *all* listing of multiple 
 * contacts
 */
class CRM_Contact_Form_Search extends CRM_Form {

    /**
     * Class construtor
     *
     * @param string    $name  name of the form
     * @param CRM_State $state State object that is controlling this form
     * @param int       $mode  Mode of operation for this form
     *
     * @return CRM_Contact_Form_Search
     * @access public
     */
    function __construct($name, $state, $mode = self::MODE_NONE)
    {
        CRM_Error::le_method();
        CRM_Error::debug_var('mode', $mode);
        parent::__construct($name, $state, $mode);
        CRM_Error::ll_method();
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        
        CRM_Error::le_method();
        CRM_Error::debug_var('this->_mode', $this->_mode);
        switch($this->_mode) {
        case CRM_Form::MODE_SEARCH_BASIC:
            CRM_Error::debug_log_message('building basic search form');
            $this->buildBasicSearchForm();
            break;
        case CRM_Form::MODE_SEARCH_ADVANCED:
            CRM_Error::debug_log_message('building advanced search form');
            $this->buildAdvancedSearchForm();
            break;        
        }

    }

    /**
     * Build the basic search form
     *
     * @access public
     * @return void
     */
    function buildBasicSearchForm( ) 
    {
        // add select for contact type
        $contactType = CRM_PseudoConstant::$contactType;
        $contactType = array('any' => ' - any contact - ') + $contactType;
        $this->add('select', 'contact_type', 'Show me.... ', $contactType);

        // add select for groups
        $group = CRM_PseudoConstant::getGroup();
        $group = array('any' => ' - any group - ') + $group;
        $this->add('select', 'group', 'in', $group);

        // add select for categories
        $category = CRM_PseudoConstant::getCategory();
        $category = array('any' => ' - any category - ') + $category;
        $this->add('select', 'category', 'Category', $category);

        // text for sort_name
        $this->add('text', 'sort_name', 'Name:', CRM_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        
        // some actions.. what do we want to do with the selected contacts ?
        $actions = array( '' => '- actions -',
                          1  => 'Add Contacts to a Group',
                          2  => 'Tag Contacts (assign category)',
                          3  => 'Add to Household',
                          4  => 'Delete',
                          5  => 'Print',
                          6  => 'Export' );
        $this->add('select', 'action_id'   , 'Actions: '    , $actions    );

        // add buttons
        $this->addButtons( array(
                                 array ( 'type'      => 'refresh',
                                         'name'      => 'Search' ,
                                         'isDefault' => true     )
                                 )        
                           );
        
        /*
         * added one extra button, this is needed as per the design of the action form
         */
        $this->add('submit', 'go', 'Go');
        CRM_Error::ll_method();
    }

    /**
     * Build the advanced search form
     *
     * @access public
     * @return void
     */
    function buildAdvancedSearchForm() 
    {
        // add checkboxes for contact type
        $cb_contact_type = array( );
        foreach (CRM_PseudoConstant::$contactType as $key => $value) {
            $cb_contact_type[] = HTML_QuickForm::createElement('checkbox', $key, null, $value);
        }
        $this->addGroup($cb_contact_type, 'cb_contact_type', 'Show Me....', '<br />');
        
        // checkboxes for groups
        $cb_group = array();
        $group = CRM_PseudoConstant::getGroup();
        foreach ($group as $groupID => $groupName) {
            $this->addElement('checkbox', "cb_group[$groupID]", null, $groupName);
        }

        // checkboxes for categories
        $cb_category = array();
        $category = CRM_PseudoConstant::getCategory();
        foreach ($category as $categoryID => $categoryName) {
            $cb_category[] = $this->addElement('checkbox', "cb_category[$categoryID]", null, $categoryName);
        }

        // add text box for last name, first name, street name, city
        $this->addElement('text', 'sort_name', 'Contact Name', CRM_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name') );
        $this->addElement('text', 'street_name', 'Street Name:', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'street_name'));
        $this->addElement('text', 'city', 'City:',CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'city'));

        // select for state province
        $stateProvince = CRM_PseudoConstant::getStateProvince();
        $stateProvince = array('' => ' - any state/province - ') + $stateProvince;
        $this->addElement('select', 'state_province', 'State/Province', $stateProvince);

        // select for country
        $country = CRM_PseudoConstant::getCountry();
        $country = array('' => ' - any country - ') + $country;
        $this->addElement('select', 'country', 'Country', $country);

        // add text box for postal code
        $this->addElement('text', 'postal_code', 'Postal Code', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );
        $this->addElement('text', 'postal_code_low', 'Postal Code Range From', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );
        $this->addElement('text', 'postal_code_high', 'To', CRM_DAO::getAttribute('CRM_Contact_DAO_Address', 'postal_code') );

        // checkboxes for location type
        $cb_location_type = array();
        $locationType = CRM_PseudoConstant::getLocationType();
        $locationType['any'] = 'Any Locations';
        foreach ($locationType as $locationTypeID => $locationTypeName) {
            $cb_location_type[] = HTML_QuickForm::createElement('checkbox', $locationTypeID, null, $locationTypeName);
        }
        $this->addGroup($cb_location_type, 'cb_location_type', 'Include these locations', '&nbsp;');
        
        // checkbox for primary location only
        $this->addElement('checkbox', 'cb_primary_location', null, 'Search for primary locations only');        

        // add components for saving the search
        $this->addElement('checkbox', 'cb_ss', null, 'Save Search ?');
        $this->addElement('text', 'ss_name', 'Name', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'name') );
        $this->addElement('text', 'ss_description', 'Description', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'description') );

        // add the buttons
        $this->addButtons(array(
                                array ( 'type'      => 'refresh',
                                        'name'      => 'Search',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'reset',
                                        'name'      => 'Reset'),
                                )
                          );
    }


    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues( ) {
        $defaults = array( );
        return $defaults;
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) {
    }

    function preProcess( ) {
        /*
         * since all output is generated by postProcess which will never be invoked by a GET call
         * we need to explicitly call it if we are invoked by a GET call
         *
         * Scenarios where we make a GET call include
         *  - pageID/sortID change
         *  - user hits reload
         *  - user clicks on menu
         */
        if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
            $this->postProcess( );
        }
    }

    function postProcess() 
    {
        CRM_Error::le_method();
        if($_GET['reset'] != 1) {
            $formValues = $this->controller->exportValues($this->_name);
            CRM_Error::debug_var("formValues", $formValues);
            CRM_Error::debug_var('this->_mode', $this->_mode);
            switch($this->_mode) {
            case CRM_Form::MODE_SEARCH_BASIC:
                CRM_Error::debug_log_message('processing basic search form');
                $selector = new CRM_Contact_Selector($formValues, CRM_Contact_Selector::TYPE_BASIC);
                break;
            case CRM_Form::MODE_SEARCH_ADVANCED:
                CRM_Error::debug_log_message('processing advanced search form');
                $selector = new CRM_Contact_Selector($formValues, CRM_Contact_Selector::TYPE_ADVANCED);
                break;        
            }
            $controller = new CRM_Selector_Controller($selector , null, null, CRM_Action::VIEW, $this);
            $controller->run();
        }
        CRM_Error::ll_method();
    }
}
?>