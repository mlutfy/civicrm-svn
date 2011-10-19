<?php

class CRM_Event_Cart_Form_Checkout_ConferenceEvents extends CRM_Event_Cart_Form_Checkout
{
  public $conference_event = null;
  public $events_by_slot = array();
  public $main_participant = null;
  public $contact_id = null;

  function preProcess( )
  {
	parent::preProcess( );
	$matches = array();
	preg_match( "/.*_(\d+)_(\d+)/", $this->getAttribute('name'), $matches );
	$event_id = $matches[1];
	$participant_id = $matches[2];
        $event_in_cart = $this->cart->get_event_in_cart_by_event_id($event_id);
        $this->conference_event = $event_in_cart->event;
        $this->main_participant = $event_in_cart->get_participant_by_id($participant_id);
        $this->contact_id = $this->main_participant->contact_id;

	$events = new CRM_Event_BAO_Event();
	$query = <<<EOS
	  SELECT * FROM civicrm_event
	  WHERE
		parent_event_id = {$this->conference_event->id}
                AND civicrm_event.is_active = 1
                AND civicrm_event.is_public = 1
                AND COALESCE(civicrm_event.is_template, 0) = 0
	  ORDER BY
		slot_label, start_date
EOS;
	$events->query($query);
	while ( $events->fetch() ) {
	  if ( !array_key_exists( $events->slot_label, $this->events_by_slot ) ) {
		$this->events_by_slot[$events->slot_label] = array();
	  }
	  $this->events_by_slot[$events->slot_label][] = clone($events);
	}
  }

  function buildQuickForm( )
  {
	//drupal_add_css(drupal_get_path('module', 'jquery_ui') . '/jquery.ui/themes/base/jquery-ui.css');
	//variable_set('jquery_update_compression_type', 'none');
	//jquery_ui_add('ui.dialog');

	$slot_index = -1;
	$slot_fields = array( );
	$session_options = array( );
	$defaults = array( );
	$previous_event_choices = $this->cart->get_events_by_contact_id($this->contact_id);
	foreach ( $this->events_by_slot as $slot_name => $events ) {
	  $slot_index++;
	  $slot_buttons = array( );
	  $group_name = "slot_$slot_index";
	  foreach ( $events as $event ) {
	    $seats_available = $this->checkEventCapacity( $event->id );
	    $event_is_full = ($seats_available === null) ? false : ($seats_available < 1);
	    $radio = $this->createElement('radio', null, null, $event->title, $event->id);
	    $slot_buttons[] = $radio;
	    $event_description = ($event_is_full ? $event->event_full_text."<p>" : '')
		. $event->description;

	    $session_options[$radio->getAttribute('id')] = array (
	    	'session_title' => $event->title,
		'session_description' => $event_description,
		'session_full' => $event_is_full,
		'event_id' => $event->id
	    );
	    foreach ( $previous_event_choices as $choice ) {
		if ($choice->id == $event->id) {
		    $defaults[$group_name] = $event->id;
		}
	    }
	  }
	  $this->addGroup( $slot_buttons, $group_name, $slot_name);
	  $slot_fields[$slot_name] = $group_name;
	  if (!isset($defaults[$group_name])) {
	      $defaults[$group_name] = $events[0]->id;
	  }
	}
	$this->setDefaults( $defaults );

	$this->assign( 'mer_participant', $this->main_participant );
	$this->assign( 'events_by_slot', $this->events_by_slot );
	$this->assign( 'slot_fields', $slot_fields );
	$this->assign( 'session_options', json_encode($session_options) );

	$buttons = array( );
	$buttons[] = array(
	  'name' => ts('<< Go Back'),
	  'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp',
	  'type' => 'back',
	);
	$buttons[] = array(
	   'isDefault' => true,
	   'name' => ts('Continue >>'),
	   'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
	   'type' => 'next',
	 );
	$this->addButtons( $buttons );
  }

  function postProcess( )
  {
	$params = $this->controller->exportValues( $this->_name );

	$main_event_in_cart = $this->cart->get_event_in_cart_by_event_id( $this->conference_event->id );

	foreach ( $this->cart->events_in_carts as $event_in_cart ) {
	    if ($event_in_cart->event->parent_event_id == $this->conference_event->id) {
		$event_in_cart->remove_participant_by_contact_id($this->contact_id);
                if (empty( $event_in_cart->participants )) {
                    $this->cart->remove_event_in_cart( $event_in_cart->id );
                }
	    }
	}

	$slot_index = -1;
	foreach ( $this->events_by_slot as $slot_name => $events ) {
	  $slot_index++;
	  $field_name = "slot_$slot_index";
	  $session_event_id = $params[$field_name];
          $event_in_cart = $this->cart->add_event( $session_event_id );

	  $values = array( );
	  CRM_Core_DAO::storeValues( $this->main_participant, $values );
	  $values['id'] = null;
	  $values['event_id'] = $event_in_cart->event_id;
	  require_once 'CRM/Event/Cart/BAO/MerParticipant.php';
	  $participant = CRM_Event_Cart_BAO_MerParticipant::create( $values );
          $event_in_cart->add_participant( $participant );
	}
	$this->cart->save( );
  }
}
