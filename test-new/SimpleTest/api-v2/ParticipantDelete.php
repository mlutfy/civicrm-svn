<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantDeleteAPIV2 extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_participantID;
    protected $_failureCase;
    protected $_eventID;
    
    
    function setUp() 
    {

        $event = $this->eventCreate();
        $this->_eventID = $event['event_id'];
        
        $this->_contactID = $this->individualCreate( ) ;
        $this->_participantID = $this->participantCreate( array('contactID' => $this->_contactID,'eventID' => $this->_eventID  ));

        $this->_failureCase = 0;
    }
    
    function tearDown()
    {       
        // Cleanup test contact.
        $result = $this->contactDelete( $this->_contactID );
        
    }
    
    
    function testParticipantDelete()
    {
        $params = array(
                        'id' => $this->_participantID,
                        );
        $participant = & civicrm_participant_delete($params);
        $this->assertNotEqual( $participant['is_error'],1 );
        $this->assertDBState( 'CRM_Event_DAO_Participant', $this->_participantID, NULL, true ); 

    }
    
   
    // This should return an error because required param is missing.. 
    function testParticipantDeleteMissingID()
    {
        $params = array(
                        'event_id'      => $this->_eventID,
                        );
        $participant = & civicrm_participant_delete($params);
        $this->assertEqual( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
        $this->_failureCase = 1;
    }
    
    
}
?>
