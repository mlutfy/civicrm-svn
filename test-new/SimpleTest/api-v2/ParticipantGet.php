<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantGetAPIV2 extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_contactID2;
    protected $_participantID;
    protected $_participantID2;
    protected $_event;
    
    
    function setUp() 
    {
        $event = $this->eventCreate();
        $this->_eventID = $event['event_id'];

        $this->_contactID = $this->individualCreate( ) ;
        $this->_participantID = $this->participantCreate( array('contactID' => $this->_contactID,'eventID' => $this->_eventID  ));
        $this->_contactID2 = $this->individualCreate( ) ;
        $this->_participantID2 = $this->participantCreate( array('contactID' => $this->_contactID2,'eventID' => $this->_eventID ));
    }
    
    function tearDown()
    {
        // Cleanup created participant records.
        $result = $this->participantDelete( $this->_participantID );
        $result = $this->participantDelete( $this->_participantID2 );

        // Cleanup test contacts.
        $result = $this->contactDelete( $this->_contactID );
        $result = $this->contactDelete( $this->_contactID2 );


        // Cleanup test event.
        $result = $this->eventDelete($this->_eventID);
    }
    
    
    function testParticipantGetParticipantIdOnly()
    {
        $params = array(
                        'participant_id'      => $this->_participantID,
                        );
        $participant = & civicrm_participant_get($params);
        $this->assertEqual($participant['event_id'], $this->_eventID);
        $this->assertEqual($participant['participant_status_id'],2);
        $this->assertEqual($participant['participant_role_id'],1);
        $this->assertEqual($participant['participant_register_date'], '2007-02-19 00:00:00');
        $this->assertEqual($participant['participant_source'],'Wimbeldon');
        $this->assertEqual($participant['event_level'],'Payment');
    }

    function testParticipantGetContactIdOnly()
    {
        $params = array(
                        'contact_id'      => $this->_contactID,
                        );
        $participant = & civicrm_participant_get($params);
        $this->assertEqual($participant['participant_id'],$this->_participantID);
        $this->assertEqual($participant['event_id'], $this->_eventID);
        $this->assertEqual($participant['participant_status_id'],2);
        $this->assertEqual($participant['participant_role_id'],1);
        $this->assertEqual($participant['participant_register_date'], '2007-02-19 00:00:00');
        $this->assertEqual($participant['participant_source'],'Wimbeldon');
        $this->assertEqual($participant['event_level'],'Payment');
    }
    

    function testParticipantGetMultiMatchReturnFirst()
    {
        $params = array(
                        'event_id'      => $this->_eventID,
                        'returnFirst'   => 1,
                        );
      
        $participant = & civicrm_participant_get($params);
      
        $this->assertNotNull($participant['participant_id']);
       
    }

    // This should return an error because there will be at least 2 participants. 
    function testParticipantGetMultiMatchNoReturnFirst()
    {
        $params = array(
                        'event_id'      => $this->_eventID,
                        );
        $participant = & civicrm_participant_get($params);
      
        $this->assertEqual( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
    }

    
}

