<?php

require_once 'api/v2/utils.php';

//require ("api/v2/Relationship.php");

/**
 * Function to update relationship type
 *
 * @param  array $params   Associative array of property name/value pairs to update the relationship type.
 *
 * @return array Array with relationship type information
 *
 * @access public
 *
 * @todo Requires some work
 */
function civicrm_relationship_type_update( $params ) {
    return civicrm_relationship_type_add( $params );
}

/**
 * Function to create relationship type
 *
 * @param  array $params   Associative array of property name/value pairs to insert in new relationship type.
 *
 * @return Newly created Relationship_type object
 *
 * @access public
 *
 */
function civicrm_relationship_type_add( $params ) {
    
    if ( empty( $params ) ) {
        return civicrm_create_error( ts( 'No input parameters present' ) );
    }

    if ( ! is_array( $params ) ) {
        return civicrm_create_error( ts( 'Parameter is not an array' ) );
    }

    if( ! isset( $params['contact_types_a'] ) &&
        ! isset( $params['contact_types_b'] ) && 
        ! isset( $params['name_a_b'] ) &&
        ! isset( $params['name_b_a'] )) { 
        
        return civicrm_create_error( ts('Missing some required parameters (contact_types_a contact_types_b name_a_b name b_a)'));
    }

    if (! isset( $params['label_a_b']) ) 
        $params['label_a_b'] = $params['name_a_b'];
    
    if (! isset( $params['label_b_a']) ) 
        $params['label_b_a'] = $params['name_b_a'];
    
    require_once 'CRM/Utils/Rule.php';
    
    $ids = array( );
    if( isset( $params['id'] ) && ! CRM_Utils_Rule::integer(  $params['id'] ) ) {
        return civicrm_create_error( 'Invalid value for relationship type ID' );
    } else {
        $ids['relationshipType'] = CRM_Utils_Array::value( 'id', $params );
    }
    
    require_once 'CRM/Contact/BAO/RelationshipType.php';
    $relationType = CRM_Contact_BAO_RelationshipType::add( $params, $ids );
    
    $relType = array( );
    _civicrm_object_to_array( $relationType, $relType );
    
    return $relType;
}

/**
 * Function to get all relationship type
 * retruns  An array of Relationship_type
 * * @access  public
 */
function civicrm_relationship_types_get( $params = null ) 
{
    _civicrm_initialize();
    require_once 'CRM/Contact/DAO/RelationshipType.php';
    $relationshipTypes = array();
    $relationshipType  = array();
    $relationType      = new CRM_Contact_DAO_RelationshipType();
    if ( !empty( $params ) && is_array( $params ) ) {
        $properties = array_keys( $relationType->fields() );
        foreach ($properties as $name) {
            if ( array_key_exists( $name, $params ) ) {
                $relationType->$name = $params[$name];
            }
        }
    }
    $relationType->find();
    while( $relationType->fetch() ) {
        _civicrm_object_to_array( clone($relationType), $relationshipType );
        $relationshipTypes[] = $relationshipType; 
    }
    return $relationshipTypes;
}


/**
 * Delete a relationship type delete
 *
 * @param  id of relationship type  $id
 *
 * @return boolean  true if success, else false
 * @static void
 * @access public
 */
function civicrm_relationship_type_delete( &$params ) {

    if( ! CRM_Utils_Array::value( 'id',$params )  ) {
        return civicrm_create_error( 'Missing required parameter' );
    }
    require_once 'CRM/Utils/Rule.php';
    if( $params['id'] != null && ! CRM_Utils_Rule::integer( $params['id'] ) ) {
        return civicrm_create_error( 'Invalid value for relationship type ID' );
    }
    
    $relationTypeBAO = new CRM_Contact_BAO_RelationshipType( );
    return $relationTypeBAO->del( $params['id'] ) ? civicrm_create_success( ts( 'Deleted relationship type successfully' )  ):civicrm_create_error( ts( 'Could not delete relationship type' ) );
}

/**
 * Wrapper to support rest calls, CRM-6860
 * return An array of Relationship_type
 * * @access  public
 */
function civicrm_relationshipType_get( $params = null ) 
{
    return civicrm_relationship_types_get( $params );
}
