<?php

require_once 'CRM/Base.php';
require_once 'CRM/Controller/Simple.php';
require_once 'CRM/DAO/Domain.php';
require_once 'CRM/Contacts/BAO/Contact_Individual.php';
require_once 'CRM/Contacts/Contacts_Datagrid.php'; 

class CRM_Contacts_List_Contacts extends CRM_Base 
{
    
    protected $_controller;
    protected $_contact_individual;
    protected $_contact_pager;

    function __construct() 
    {
        parent::__construct();
    }
    
    function run( $mode, $id = 0 ) {
        $session = CRM_Session::singleton();
        $config  = CRM_Config::singleton();
        
        // store the return url. Note that this is typically computed by the framework at runtime
        // based on multiple things (typically where the link was clicked from / http_referer
        // since we are just starting and figuring out navigation, we are hard coding it here
        $session->pushUserContext( $config->httpBase . "crm/contact/list?reset=1" );
        
        $this->_controller = new CRM_Controller_Simple( 'CRM_Contacts_Form_CLIST', 'Contact CLIST Page', $mode );
        
        $this->_controller->process();
        $this->_controller->run();

        /**
    $contact    = new CRM_Contacts_BAO_Contact_Individual();

    $contact->domain_id = 1;
    $contact->find();
    while ( $contact->fetch() ) {
    // CRM_Log::debug( 'contactInd', $contact );
    }

    $contact = new CRM_Contacts_BAO_Contact_Individual();
    $contact->contact_type = 'Individual';
    $contact->sort_name    = 'Donald Lobo';
    $contact->hash         = 9876543;
    $contact->domain_id    = 1;
    $contact->first_name   = 'Donald';
    $contact->last_name    = 'Lobo';
    $contact->insert();
        **/
    }
    
    function list_contact()
    {
        $_contact_individual = new CRM_Contacts_DAO_Contact_Individual();
        $_contact_pager = new CRM_Paging();
        $a_pager_arr = $_contact_pager->f_Paging($_contact_individual); 
        $_current_pagevalue =  $a_pager_arr[1];
        $limit_startvalue = ($_current_pagevalue * 3) - 3;
        $_contact_individual->limit( $limit_startvalue, 3);
        $_contact_individual->find();
        $a_render_arr[0] =  $a_pager_arr[0];
        $a_render_arr[1] = $_contact_pager->f_Rendering( $_contact_individual);
        return $a_render_arr;
    }
    
    /*function display() 
    {
        return $this->_controller->getContent();
    }*/

    function getContent()
    {
        return $this->_controller->getContent();
    }    
}

?>
