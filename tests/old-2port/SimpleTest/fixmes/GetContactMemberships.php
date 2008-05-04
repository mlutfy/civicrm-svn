<?php

require_once 'api/crm.php';

class TestOfGetContactMemberships extends UnitTestCase 
{
    protected $_membership1   = array();
    protected $_membership2   = array();
    protected $_individual;
        
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateContact() 
    {
        $params = array(
                        'first_name' => 'Apoorva',
                        'last_name'  => 'Mehta'
                        );
        $this->_individual =& crm_create_contact($params,'Individual');
        $this->assertIsA($this->_individual, 'CRM_Contact_DAO_Contact');
        $this->assertEqual($this->_individual->contact_type, 'Individual');
    }

    function testCreateMembership()
    {
        $contactId = $this->_individual->id;
        $params1 = array(
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'status_id'          => '2'                       
                        );
	
        $membership1 = & crm_create_contact_membership($params1,$contactId);                 
        $this->_membership1 = $membership1 ;   

   
        $params2 = array(
                        'membership_type_id' => '2',
                        'join_date'          => '2007-01-21',
                        'start_date'         => '2007-01-21',
                        'end_date'           => '2007-12-21',
                        'source'             => 'Donation',
                        'status_id'          => '2'                       
                        );	
        $membership2 = & crm_create_contact_membership($params2,$contactId);                                      
        $this->_membership2 = $membership2 ;
    }
    
    // function testGetWrongMembershipWithoutId()
//     {
//         $params = array();                       
//         $membership = & crm_get_contact_memberships($params);
//         $this->assertIsA($membership,'CRM_Core_Error');
//     }

    function testGetMemberships()
    {
        $contactId = $this->_individual->id;
        
        $membership = & crm_get_contact_memberships($contactId);
        
        $this->assertEqual($membership[$contactId][$this->_membership1['id']]['id'],$this->_membership1['id']); 
        $this->assertEqual($membership[$contactId][$this->_membership1['id']]['membership_type_id'],'1');
        $this->assertEqual($membership[$contactId][$this->_membership1['id']]['join_date'],'2006-01-21');
        $this->assertEqual($membership[$contactId][$this->_membership1['id']]['start_date'],'2006-01-21');
        $this->assertEqual($membership[$contactId][$this->_membership1['id']]['end_date'],'2006-12-21');
        $this->assertEqual($membership[$contactId][$this->_membership1['id']]['source'],'Payment');
        $this->assertEqual($membership[$contactId][$this->_membership1['id']]['status_id'],'2');   
        $this->assertEqual($membership[$contactId][$this->_membership2['id']]['id'],$this->_membership2['id']);
        $this->assertEqual($membership[$contactId][$this->_membership2['id']]['membership_type_id'],'2');
        $this->assertEqual($membership[$contactId][$this->_membership2['id']]['join_date'],'2007-01-21');
        $this->assertEqual($membership[$contactId][$this->_membership2['id']]['start_date'],'2007-01-21');
        $this->assertEqual($membership[$contactId][$this->_membership2['id']]['end_date'],'2007-12-21');
        $this->assertEqual($membership[$contactId][$this->_membership2['id']]['source'],'Donation');
        $this->assertEqual($membership[$contactId][$this->_membership2['id']]['status_id' ],'2'); 
        
    }
    
    function testDeleteMembership()
    {
        $val1 = &crm_delete_membership($this->_membership1['id']);
        $this->assertNull($val1);

        $val2 = &crm_delete_membership($this->_membership2['id']);
        $this->assertNull($val2);
    }

    function testDeleteContact()
    {
        $val = &crm_delete_contact(& $this->_individual,102);
        $this->assertNull($val);
    }
    
}

