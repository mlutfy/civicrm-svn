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
require_once 'CRM/Core/Session.php';
require_once 'CRM/Core/PseudoConstant.php';

/**
 * Base Search / View form for *all* listing of multiple 
 * contacts
 */
class CRM_Contact_Form_SavedSearch extends CRM_Form {

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
        //CRM_Error::le_method();
        parent::__construct($name, $state, $mode);
        //CRM_Error::ll_method();
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        //CRM_Error::le_method();

        $session = CRM_Session::singleton( );
        $asfv = unserialize($session->get("fv", CRM_SESSION::SCOPE_AS));

        //CRM_Error::debug_var('asfv', $asfv);

        $qill = CRM_Contact_Selector::getQILL($asfv, CRM_Form::MODE_ADVANCED);

        $template = SmartyTemplate::singleton($config->templateDir, $config->templateCompileDir);
        $template->assign('qill' , $qill);
        
        $this->addElement('text', 'name', 'Name', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'name') );
        $this->addElement('text', 'description', 'Description', CRM_DAO::getAttribute('CRM_Contact_DAO_SavedSearch', 'description') );
        
        // add the buttons
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => 'Save Search',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'reset',
                                        'name'      => 'Reset'),
                                )
                          );

        CRM_Error::ll_method();
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
            // $this->postProcess( );
        }
    }

    function postProcess() 
    {
        CRM_Error::le_method();

        $session = CRM_Session::singleton();
            
        // advanced search form values
        $asfv = unserialize($session->get("fv", CRM_Session::SCOPE_AS));
        
        // saved search form values
        $fv = $this->controller->exportValues($this->_name);

        // create saved search BAO and insert the SS
        $ssBAO = new CRM_Contact_BAO_SavedSearch();
        $ssBAO->domain_id = 1;   // hack for now
        $ssBAO->name = $fv['name'];
        $ssBAO->description = $fv['description'];
        $ssBAO->search_type = CRM_Form::MODE_ADVANCED;
        $ssBAO->form_values = serialize($asfv);
        $ssBAO->insert();
        
        CRM_Session::setStatus( 'Your search has been saved as "' . $fv['name'] . '"' );

        CRM_Error::ll_method();
    }
}
?>