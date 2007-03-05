<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | copyright CiviCRM LLC (c) 2004-2007                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                 |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of CRM API for Event.
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/utils.php';

/**
 * Create a Event
 *  
 * This API is used for creating a Event
 * 
 * @param   array  $params  an associative array of title/value property values of civicrm_event
 * 
 * @return array of newly created event property values.
 * @access public
 */
function crm_create_event( $params ) 
{
    _crm_initialize();
    if ( ! is_array($params) ) {
        return _crm_error('Params is not an array.');
    }
    
    if (!$params["title"] || ! $params['event_type_id'] || ! $params['start_date']) {
        return _crm_error('Missing require fileds ( title, event type id,start date)');
    }
    
    if ( !$params['domain_id'] ) {
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton();
        $params['domain_id'] = $config->domainID();
    }
    
    $error = _crm_check_required_fields( $params, 'CRM_Event_DAO_Event');
    if ( is_a($error, 'CRM_Core_Error')  ) {
        return $error;
    }
    
    $ids['event'      ] = $params['id'];
    $ids['eventTypeId'] = $params['event_type_id'];
    $ids['startDate'  ] = $params['start_date'];
    
    require_once 'CRM/Event/BAO/Event.php';
    $eventBAO = CRM_Event_BAO_Event::add($params, $ids);
    
    $event = array();
    _crm_object_to_array($eventBAO, $event);
    
    return $event;
}

/**
 * Get a Event.
 * 
 * This api is used for finding an existing Event.
 * Required parameters : id of event
 * 
 * @params  array $params  an associative array of title/value property values of civicrm_event
 * 
 * @return  Array of all found event property values.
 * @access public
 */
function crm_get_events( $params ) 
{
    _crm_initialize();
    if ( ! is_array($params) ) {
        return _crm_error('Params is not an array.');
    }
    
    if ( ! isset($params['id'])) {
        return _crm_error('Required parameters missing.');
    }
    
    require_once 'CRM/Event/BAO/Event.php';
    $eventBAO = new CRM_Event_BAO_Event();
    
    $properties = array_keys($eventBAO->fields());
    
    foreach ($properties as $name) {
        if (array_key_exists($name, $params)) {
            $eventBAO->$name = $params[$name];
        }
    }
    
    if ( $eventBAO->find() ) {
        $events = array();
        while ( $eventBAO->fetch() ) {
            _crm_object_to_array( clone($eventBAO), $event );
            $events[$eventBAO->id] = $event;
        }
    } else {
        return _crm_error('Exact match not found');
    }
    return $events;
}

/**
 * Update an existing event
 *
 * This api is used for updating an existing event.
 * Required parrmeters : id of a event
 * 
 * @param  Array   $params  an associative array of title/value property values of civicrm_event
 * 
 * @return array of updated event property values
 * @access public
 */
function &crm_update_event( $params ) {
    if ( !is_array( $params ) ) {
        return _crm_error( 'Params is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        return _crm_error( 'Required parameter missing' );
    }
    
    require_once 'CRM/Event/BAO/Event.php';
    $eventBAO =& new CRM_Event_BAO_Event( );
    $eventBAO->id = $params['id'];
    if ($eventBAO->find(true)) {
        $fields = $eventBAO->fields( );
        foreach ( $fields as $name => $field) {
            if (array_key_exists($name, $params)) {
                $eventBAO->$name = $params[$name];
            }
        }
        $eventBAO->save();
    }
    
    $event = array();
    _crm_object_to_array( $eventBAO, $event );
    return $event;
}

/**
 * Deletes an existing event
 * 
 * This API is used for deleting a event
 * 
 * @param  Int  $eventID    ID of event to be deleted
 * 
 * @return null if successfull, object of CRM_Core_Error otherwise
 * @access public
 */
function &crm_delete_event( $eventID ) {
    if ( ! $eventID ) {
        return _crm_error( 'Invalid value for eventID' );
    }
    require_once 'CRM/Event/BAO/Event.php';
    return CRM_Event_BAO_Event::del($eventID);
}
?>