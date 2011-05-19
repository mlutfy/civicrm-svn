<?php  
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.4                                                |
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

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'api/v3/Membership.php';
require_once 'api/v3/MembershipType.php';
require_once 'api/v3/MembershipStatus.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v3_MembershipTest extends CiviUnitTestCase
{
    protected $_apiversion;
    protected $_contactID;
    protected $_membershipTypeID;
    protected $_membershipStatusID ;
    protected $__membershipID;
    protected $_entity;
    protected $_params;
            public $DBResetRequired = true;     
    public function setUp()
    {
 
        //  Connect to the database
        parent::setUp();
        $this->_apiversion =3;
        $this->_contactID           = $this->individualCreate( ) ;
        $this->_membershipTypeID    = $this->membershipTypeCreate( $this->_contactID  );        
        $this->_membershipStatusID  = $this->membershipStatusCreate( 'test status' );
        
        require_once 'CRM/Member/PseudoConstant.php';
        CRM_Member_PseudoConstant::membershipType( null, true );
        CRM_Member_PseudoConstant::membershipStatus( null, null, 'name', true );

        
        $this->_entity = 'membership';
        $this->_params = array(
                        'contact_id'         => $this->_contactID,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2009-01-21',
                        'start_date'         => '2009-01-21',
                        'end_date'           => '2009-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID,
                        'version'						 => 3,
                        );
  
    }
    function tearDown( ) 
    {
        $this->membershipStatusDelete( $this->_membershipStatusID );
        $this->membershipTypeDelete( array('id' => $this->_membershipTypeID) );
        $this->contactDelete( $this->_contactID );   

    }
    /**
     *  Test civicrm_membership_delete()
     */
    function testMembershipDelete()
    {
        $membershipID = $this->contactMembershipCreate( $this->_params );
        $params=array('id' 			 => $membershipID,
                      'version'  => $this->_apiversion,);
        $result = civicrm_api3_membership_delete($params);
        $this->documentMe($params,$result,__FUNCTION__,__FILE__); 
        $this->assertEquals( $result['is_error'], 0,
                             "In line " . __LINE__ );      
  
    }

    
    function testMembershipDeleteEmpty( ) 
    {
        $params = array( );
        $result = civicrm_api3_membership_delete( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }



    /**
     *  Test civicrm_membership_delete() with invalid Membership Id
     */
    function testMembershipDeleteWithInvalidMembershipId( )
    {
        $membershipId = 'membership';
        $result = civicrm_api3_membership_delete($membershipId);
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }

    /**
     *  All other methods calls MembershipType and MembershipContact
     *  api, but putting simple test methods to control existence of
     *  these methods for backwards compatibility, also verifying basic
     *  behaviour is the same as new methods.
     */
     
     function testContactMembershipsGet()
     {
         $this->_membershipID = $this->contactMembershipCreate( $this->_params );
       
         $this->assertTrue( function_exists(civicrm_api3_membership_get) );
         $params = array('version' => $this->_apiversion);
         $result = & civicrm_api3_membership_get( $params );
         $this->assertEquals( 0, $result['is_error'],  "In line " . __LINE__ );
         $result = civicrm_api('Membership','Delete',array('id'=> $this->_membershipID,
         																						'version'  => $this->_apiversion,));
    }
     
     function testContactMembershipCreate()
     {

         $this->assertTrue( function_exists(civicrm_api3_membership_create) );
         $params = array('version' => $this->_apiversion);
         $result = civicrm_api3_membership_create( $params );
         $this->assertEquals( 1, $result['is_error'],  "In line " . __LINE__ );
     }




    /**
     * Test civicrm_membership_get with params not array.
     * Gets treated as contact_id, memberships expected.
     */
    function testGetWithParamsContactId()
    {
        $this->_membershipID = $this->contactMembershipCreate( $this->_params );
        $params = array('contact_id' => $this->_contactID ,
                         'version'   => $this->_apiversion );
        $membership = civicrm_api3_membership_get( $params );

        $result = $membership['values'][$this->_membershipID];

        $this->assertEquals($result['contact_id'],         $this->_contactID, "In line " . __LINE__);
        $this->assertEquals($result['membership_type_id'], $this->_membershipTypeID, "In line " . __LINE__);
        $this->assertEquals($result['status_id'],          $this->_membershipStatusID, "In line " . __LINE__);
        $this->assertEquals($result['join_date'],          '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['start_date'],         '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['end_date'],           '2009-12-21', "In line " . __LINE__);
        $this->assertEquals($result['source'],             'Payment', "In line " . __LINE__);
        $this->assertEquals($result['is_override'],         1, "In line " . __LINE__);        
        $result = civicrm_api('Membership','Delete',array('id'=> $this->_membershipID,
         																						'version'  => $this->_apiversion,));
    }
   /**
     * Test civicrm_membership_get with params not array.
     * Gets treated as contact_id, memberships expected.
     */
    function testGetWithParamsMemberShipTypeId()
    {
         $result = civicrm_api($this->_entity,'create',  $result );
        $params = array('membership_type_id' =>   $this->_membershipTypeID ,
                         'version'   => $this->_apiversion );
        $membership = civicrm_api3_membership_get( $params );
        $result = $membership['values'][$membership['id']];
print_r($result);
print_r($membership);
        $this->assertEquals($result['contact_id'],         $this->_contactID, "In line " . __LINE__);
        $this->assertEquals($result['membership_type_id'], $this->_membershipTypeID, "In line " . __LINE__);
        $this->assertEquals($result['status_id'],          $this->_membershipStatusID, "In line " . __LINE__);
        $this->assertEquals($result['join_date'],          '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['start_date'],         '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['end_date'],           '2009-12-21', "In line " . __LINE__);
        $this->assertEquals($result['source'],             'Payment', "In line " . __LINE__);
        $this->assertEquals($result['is_override'],         1, "In line " . __LINE__);
        $result = civicrm_api('Membership','Delete',array('id'=>   $result['id'] ,
         																						'version'  => $this->_apiversion,));     
    }      

       /**
     * Test civicrm_membership_get with params not array.
     * Gets treated as contact_id, memberships expected.
     */
    function testGetWithNonExistantMemberShipTypeId()
    {
        $this->_membershipID = $this->contactMembershipCreate( $this->_params );
        $params = array('membership_type_id' => 465653 ,
                         'version'   => $this->_apiversion );
        $result = civicrm_api3_membership_get( $params );
        $this->assertEquals($result['is_error'],0, "In line " . __LINE__);
        $this->assertEquals($result['count'],0, "In line " . __LINE__);
        $result = civicrm_api('Membership','Delete',array('id'=> $this->_membershipID,
         																						'version'  => $this->_apiversion,));
  
         
    }   
    
    
        /**
     * check with complete array + custom field 
     * Note that the test is written on purpose without any
     * variables specific to participant so it can be replicated into other entities
     * and / or moved to the automated test suite
     */
    function testGetWithParamsMemberShipIdAndCustom()
    {
        $ids = $this->entityCustomGroupWithSingleFieldCreate( __FUNCTION__,__FILE__);
        
        $params = $this->_params;
        $params['custom_'.$ids['custom_field_id']]  =  "custom string";
 
        $result = civicrm_api($this->_entity,'create', $params);
        $this->documentMe($params,$result  ,__FUNCTION__,__FILE__);
        $this->assertNotEquals( $result['is_error'],1 ,$result['error_message'] . ' in line ' . __LINE__);

        $check = civicrm_api($this->_entity,'get',array('version' =>3, 'id' => $result['id']));
        $this->assertEquals("custom string", $check['values'][$result['id']]['custom_' .$ids['custom_field_id'] ],' in line ' . __LINE__);

        $this->customFieldDelete($ids['custom_field_id']);
        $this->customGroupDelete($ids['custom_group_id']);   
        $result = civicrm_api('Membership','Delete',array('id'=>   $result['id'] ,
         																						'version'  => $this->_apiversion,));     

    }
    /**
     * Test civicrm_membership_get with proper params.
     * Memberships expected.
     */
    function testGet()
    {
        $params = array ( 'contact_id' => $this->_contactID,
                          'version'		=> $this->_apiversion, );

        $membership =& civicrm_api3_membership_get( $params );
        $this->documentMe($params,$membership,__FUNCTION__,__FILE__);
        $result = $membership['values'][$this->_membershipID];
        $this->assertEquals($result['join_date'],          '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['contact_id'],         $this->_contactID, "In line " . __LINE__);
        $this->assertEquals($result['membership_type_id'], $this->_membershipTypeID, "In line " . __LINE__);
        $this->assertEquals($result['status_id'],          $this->_membershipStatusID, "In line " . __LINE__);

        $this->assertEquals($result['start_date'],         '2009-01-21', "In line " . __LINE__);
        $this->assertEquals($result['end_date'],           '2009-12-21', "In line " . __LINE__);
        $this->assertEquals($result['source'],             'Payment', "In line " . __LINE__);
        $this->assertEquals($result['is_override'],         1, "In line " . __LINE__);
        $result = civicrm_api('Membership','Delete',array('id'=>   $result['id'] ,
         																						'version'  => $this->_apiversion,));           
    }

    /**
     * Test civicrm_membership_get for only active.
     * Memberships expected.
     */
    function testGetOnlyActive()
    {
        $this->_membershipID = $this->contactMembershipCreate( $this->_params );
        $params = array ( 'contact_id'  => $this->_contactID,
                          'active_only' => 1,
                          'version'    => $this->_apiversion);

        $membership =& civicrm_api3_membership_get( $params );
        $result = $membership['values'][$this->_membershipID];
        $this->assertEquals($membership['values'][$this->_membershipID]['status_id'], $this->_membershipStatusID, "In line " . __LINE__);
        $this->assertEquals($membership['values'][$this->_membershipID]['contact_id'], $this->_contactID, "In line " . __LINE__);
        $result = civicrm_api('Membership','Delete',array('id'=>   $membership['id'] ,
         																						'version'  => $this->_apiversion,));  
    }

    /**
     * Test civicrm_membership_get for non exist contact.
     * empty Memberships.
     */
    function testGetNoContactExists()
    {
        $params = array ( 'contact_id'  => 55555,
                           'version'   => $this->_apiversion );
                          
        $membership = civicrm_api3_membership_get( $params );
        $this->assertEquals($membership['count'], 0, "In line " . __LINE__);
    }

    /**
     * Test civicrm_membership_get with relationship.
     * get Memberships.
     */
    function testGetWithRelationship()
    {

        $membershipOrgId = $this->organizationCreate(null  );
        $memberContactId = $this->individualCreate(null ) ;

        $relTypeParams = array(
                               'name_a_b'       => 'Relation 1',
                               'name_b_a'       => 'Relation 2',
                               'description'    => 'Testing relationship type',
                               'contact_type_a' => 'Organization',
                               'contact_type_b' => 'Individual',
                               'is_reserved'    => 1,
                               'is_active'      => 1,
                               'version'				=> $this->_apiversion,
                               );
        $relTypeID = $this->relationshipTypeCreate( $relTypeParams );

        $params = array( 'name'                   => 'test General',
                         'duration_unit'          => 'year',
                         'duration_interval'      => 1,
                         'period_type'            => 'rolling',
                         'member_of_contact_id'   => $membershipOrgId,
                         'domain_id'		  => 1,
                         'contribution_type_id'   => 1,
                         'relationship_type_id'   => $relTypeID,
                         'relationship_direction' => 'b_a',
                         'is_active'              => 1,
                         'version'				=> $this->_apiversion, );        
        $memType = civicrm_api3_membership_type_create( $params );
        // in order to reload static caching -
        CRM_Member_PseudoConstant::membershipType( null, true );

        $params = array(
                        'contact_id'         => $memberContactId,
                        'membership_type_id' => $memType['id'],
                        'join_date'          => '2009-01-21',
                        'start_date'         => '2009-01-21',
                        'end_date'           => '2009-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID,
                        'version'				=> $this->_apiversion,
                        );
        $membershipID = $this->contactMembershipCreate( $params );

        $params = array ( 'contact_id'  => $memberContactId ,
                          'membership_type_id' => $memType['id'],
                          'version'				=> $this->_apiversion, );
                          
        $result =& civicrm_api3_membership_get( $params );

        $membership = $result['values'][$membershipID];
        $this->assertEquals( $this->_membershipStatusID, $membership['status_id'], 
                             "In line " . __LINE__);
        $result = civicrm_api('Membership','Delete',array('id'=>   $membership['id'] ,
         																						'version'  => $this->_apiversion,));  
  
        
    }

///////////////// civicrm_membership_create methods

    /**
     * Test civicrm_contact_memberships_create with empty params.
     * Error expected.
     */    
    function testCreateWithEmptyParams() 
    {
        $params = array();
        $result = civicrm_api3_membership_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }

    /**
     * Test civicrm_contact_memberships_create with params with wrong type.
     * Error expected.
     */
    function testCreateWithParamsString()
    {
        $params = 'a string';
        $result = & civicrm_api3_membership_create( $params );
        $this->assertEquals( $result['is_error'], 1,
                             "In line " . __LINE__ );
    }

    function testMembershipCreateMissingRequired( ) 
    {
        $params = array(
                        'membership_type_id' => '1',
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'status_id'          => '2' ,
                        'version'				=> $this->_apiversion,                      
                        );
        
        $result = civicrm_api3_membership_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }
    
    function testMembershipCreate( ) 
    {
        $params = array(
                        'contact_id'         => $this->_contactID,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID ,                      
                        'version'				    => $this->_apiversion,                        );

        $result = civicrm_api3_membership_create( $params );
        $this->documentMe($params,$result,__FUNCTION__,__FILE__); 
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertNotNull( $result['id'] );
        $result = civicrm_api('Membership','Delete',array('id'=>   $result['id'] ,
         																						'version'  => $this->_apiversion,));  
    }
        /**
     * check with complete array + custom field 
     * Note that the test is written on purpose without any
     * variables specific to participant so it can be replicated into other entities
     * and / or moved to the automated test suite
     */
    function testCreateWithCustom()
    {
        $ids = $this->entityCustomGroupWithSingleFieldCreate( __FUNCTION__,__FILE__);
        
        $params = $this->_params;
        $params['custom_'.$ids['custom_field_id']]  =  "custom string";
 
        $result = civicrm_api($this->_entity,'create', $params);
        $this->documentMe($params,$result  ,__FUNCTION__,__FILE__);
        $this->assertNotEquals( $result['is_error'],1 ,$result['error_message'] . ' in line ' . __LINE__);

        $check = civicrm_api($this->_entity,'get',array('version' =>3, 'id' => $result['id'], 'contact_id' => $this->_contactID));
        $this->assertEquals("custom string", $check['values'][$result['id']]['custom_' .$ids['custom_field_id'] ],' in line ' . __LINE__);

        $this->customFieldDelete($ids['custom_field_id']);
        $this->customGroupDelete($ids['custom_group_id']); 
        $result = civicrm_api('Membership','Delete',array('id'=>   $result['id'] ,
         																						'version'  => $this->_apiversion,));       

    }
    /**
     * Test civicrm_contact_memberships_create with membership id (edit
     * membership).
     * success expected.
     */
    function testMembershipCreateWithId( ) 
    {
        $params = array(
                        'id'                 => $this->_membershipID,
                        'contact_id'         => $this->_contactID,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2006-01-21',
                        'start_date'         => '2006-01-21',
                        'end_date'           => '2006-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID,
                       'version'				=> $this->_apiversion,                       
                        );

        $result = civicrm_api3_membership_create( $params );
                civicrm_api('Membership','Delete',array('id'=>   $result['id'] ,
         																						'version'  => $this->_apiversion,)); 
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertEquals( $result['id'] , $this->_membershipID );
 
    }

    /**
     * Test civicrm_contact_memberships_create Invalid membership data
     * Error expected.
     */
    function testMembershipCreateInvalidMemData( ) 
    {
        //membership_contact_id as string
        $params = array(
                        'membership_contact_id' => 'Invalid',
                        'contact_id'         => $this->_contactID,  
                        'membership_type_id' => $this->_membershipTypeID,
                        'join_date'          => '2011-01-21',
                        'start_date'         => '2010-01-21',
                        'end_date'           => '2008-12-21',
                        'source'             => 'Payment',
                        'is_override'        => 1,
                        'status_id'          => $this->_membershipStatusID,
                        'version'				=> $this->_apiversion,                       
                        );

        $result = civicrm_api3_membership_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
        
        //membership_contact_id which is no in contact table
        $params['membership_contact_id'] = 999;
        $result = civicrm_api3_membership_create( $params );
        $this->assertEquals( $result['is_error'], 1 );

        //invalid join date
        unset( $params['membership_contact_id'] );
        $params['join_date'] = "invalid";
        $result = civicrm_api3_membership_create( $params );
        $this->assertEquals( $result['is_error'], 1 );
    }
    
    /**
     * Test civicrm_contact_memberships_create with membership_contact_id
     * membership).
     * Success expected.
     */
    function testMembershipCreateWithMemContact( ) 
    {
            
        $params = array(
                        'membership_contact_id' => $this->_contactID,
                        'contact_id'            => $this->_contactID,  
                        'membership_type_id'    => $this->_membershipTypeID,
                        'join_date'             => '2011-01-21',
                        'start_date'            => '2010-01-21',
                        'end_date'              => '2008-12-21',
                        'source'                => 'Payment',
                        'is_override'           => 1,
                        'status_id'             => $this->_membershipStatusID   ,                    
                        'version'				=> $this->_apiversion,                        );

        $result = civicrm_api3_membership_create( $params );

        $this->assertEquals( $result['is_error'], 0 );
        $result = civicrm_api('Membership','Delete',array('id'=>   $result['id'] ,
         																						'version'  => $this->_apiversion,));  
        
    }

///////////////// civicrm_membership_delete methods

 




 ///////////////// _civicrm_membership_format_params with $create 
 
    function testMemebershipFormatParamsWithCreate( ) 
    {

        $params = array(
                        'contact_id'            => $this->_contactID,  
                        'membership_type_id'    => $this->_membershipTypeID,
                        'join_date'             => '2006-01-21',
                        'membership_start_date' => '2006-01-21',
                        'membership_end_date'   => '2006-12-21',
                        'source'                => 'Payment',
                        'is_override'           => 1,
                        'status_id'             => $this->_membershipStatusID ,
                        'version'				=> $this->_apiversion,                      
                        );

        $values = array( );
        _civicrm_api3_membership_format_params( $params , $values, true);
        
        $this->assertEquals( $values['start_date'], $params['membership_start_date'] );
        $this->assertEquals( $values['end_date'], $params['membership_end_date'] );
    }

}

     
