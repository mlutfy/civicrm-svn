<?php

require_once 'api/v2/GroupContact.php';

class TestOfGroupContactGetAPIV2 extends CiviUnitTestCase 
{
    private   $_group ; 
    protected $_groupId1;
    protected $_groupId2;
    
    function setUp() 
    {
        $this->_contactId = $this->individualCreate();
        $this->_groupId1  = $this->groupCreate( );
        $params = array( 'contact_id.1' => $this->_contactId,
                         'group_id'     =>  $this->_groupId1 );
        
        civicrm_group_contact_add( $params );
        
        $group = array(
                       'name'        => 'Test Group 2',
                       'domain_id'   => 1,
                       'title'       => 'New Test Group2 Created',
                       'description' => 'New Test Group2 Created',
                       'is_active'   => 1,
                       'visibility'  => 'User and User Admin Only',
                       );
        $this->_groupId2  = $this->groupCreate( $group );
        $params = array( 'contact_id.1' => $this->_contactId,
                         'group_id'     =>  $this->_groupId2  );
        
        civicrm_group_contact_add( $params );
        
        $this->_group = array($this->_groupId1  => array( 'title'      => 'New Test Group Created',
                                                          'visibility' => 'Public User Pages and Listings',
                                                          'in_method'  => 'API'),
                              $this->_groupId2  => array( 'title'      => 'New Test Group2 Created',
                                                          'visibility' => 'User and User Admin Only',
                                                          'in_method'  => 'API' ));
        
    }
    
    function tearDown() 
    {
        $this->contactGroupDelete( $this->_contactId );
        $this->groupDelete( $this->_groupId1 );
        $this->groupDelete( $this->_groupId2 );
        $this->contactDelete( $this->_contactId );
    }
    
    function testGetGroupContactsWithEmptyParams( ) 
    {
        $params = array( );
        $groups = civicrm_group_contact_get( $params );
        
        $this->assertEqual( $groups['is_error'], 1 );
        $this->assertEqual( $groups['error_message'], 'contact_id is a required field' );
    }
    
   function testGetGroupContacts( ) 
   {
       $params = array( 'contact_id' => $this->_contactId );
       $groups = civicrm_group_contact_get( $params );
                 
       foreach( $groups as $v  ){ 
           $this->assertEqual( $v['title'], $this->_group[$v['group_id']]['title'] );
           $this->assertEqual( $v['visibility'], $this->_group[$v['group_id']]['visibility'] );
           $this->assertEqual( $v['in_method'], $this->_group[$v['group_id']]['in_method'] );
       }
   }
   
   
}


