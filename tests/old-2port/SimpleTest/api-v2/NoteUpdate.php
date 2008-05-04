<?php

require_once 'api/v2/Note.php';

class TestOfNoteUpdateAPIV2 extends CiviUnitTestCase 
{

    protected $_contactID;
    protected $_noteID;
    protected $_noteArray = array( );

    function setUp( ) 
    {
        $this->_contactID = $this->organizationCreate( );
        $this->_note      = $this->noteCreate( $this->_contactID );
        $this->_noteID    = $this->_note['id'];
    }
    
    function testNoteUpdateEmpty( )
    {
        $params = array();        
        $note   = & civicrm_note_update( $params );
        $this->assertEqual( $note['is_error'], 1 );
        $this->assertEqual( $note['error_message'], 'Required parameter missing' );
    }

    function testNoteUpdateMissingContactId( )
    {
        $params = array(
                        'entity_id'    => $this->_contactID,
                        'entity_table' => 'civicrm_contact'                
                        );        
        $note   = & civicrm_note_update( $params );
        $this->assertEqual( $note['is_error'], 1 );
        $this->assertEqual( $note['error_message'], 'Required parameter missing' );
    }
    
    function testNoteUpdate( )
    {
        $params = array(
                        'id'           => $this->_noteID,
                        'contact_id'   => $this->_contactID,
                        'entity_id'    => $this->_contactID,
                        'entity_table' => 'civicrm_contribution',
                        'note'         => 'Note1',
                        'subject'      => 'Hello World'
                        );
        
        //Update Note
        $note = & civicrm_note_update( $params );
        
        $this->assertEqual( $note['id'],$this->_noteID );
        $this->assertEqual( $note['entity_id'],$this->_contactID );
        $this->assertEqual( $note['entity_table'],'civicrm_contribution' );
    }
    
    function tearDown( ) 
    {
        $this->noteDelete( $this->_note );
        $this->contactDelete( $this->_contactID );
    }
    
}