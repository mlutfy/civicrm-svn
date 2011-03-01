<?php

/**
 * File for the CiviCRM APIv3 API wrapper
 *
 * @package CiviCRM_APIv3
 * @subpackage API
 *
 * @copyright CiviCRM LLC (c) 2004-2010
 * @version $Id: api.php 30486 2010-11-02 16:12:09Z shot $
 */

/*
 * 
usage
$result = civicrm_api_legacy('civicrm_contact_get', 'Contact', $params);
@TODO the class is generated by our code. TO be verified
 * @param string $function name of API function
 * @param string $class name of file
 * @param array $params array to be passed to function
 */
function civicrm_api_legacy($function, $class, $params){

  $version = civicrm_get_api_version($params);
  require_once 'CRM/Utils/String.php';
  // clean up. they should be alphanumeric and _ only
  $class = CRM_Utils_String::munge( $class );
  $function = CRM_Utils_String::munge( $function );
  
  require_once 'api/v' . $version . '/' . $class .'.php';
  $result = $function($params);
  return $result;
}


/*
 * @param string $entity
 *   type of entities to deal with
 * @param string $action
 *   create, get, delete or some special action name.
 * @param array $params
 *   array to be passed to function
 */
function civicrm_api($entity, $action, $params, $extra = NULL) {
  require_once 'CRM/Utils/String.php';
  $entity = CRM_Utils_String::munge($entity);
  $action = CRM_Utils_String::munge($action);
  $version = civicrm_get_api_version($params);
  $function = civicrm_api_get_function_name($entity, $action);
  civicrm_api_include($entity);
  if ( !function_exists ($function )) {
    if ( strtolower($action) == "getfields" && $version ==3) {
      require_once ('api/v3/utils.php');
      $dao = civicrm_get_DAO ($entity);
      if (empty($dao)) 
        return civicrm_create_error( "API for $entity does not exist (join the API team and implement $function" );
      $file = str_replace ('_','/',$dao).".php";
      require_once ($file); 
      $d = new $dao();
      return $d->fields();
    }
    if ( strtolower($action) == "update") {
      //$key_id = strtolower ($entity)."_id";
      $key_id = "id";
      if (!array_key_exists ($key_id,$params)) 
        return civicrm_create_error( "Mandatory parameter missing $key_id" );
      $seek = array ($key_id => $params[$key_id], 'version' => $version);
      $existing = civicrm_api ($entity, 'get',$seek);
      if ($existing['is_error'])
        return $existing;
      if ($existing['count'] > 1)
        return civicrm_create_error( "More than one $entity with id ".$params[$key_id] );
      if ($existing['count'] == 0)
        return civicrm_create_error( "No $entity with id ".$params[$key_id] );
       
      $existing= array_pop($existing['values'] ); 
      $p = array_merge ($params, $existing );
      return civicrm_api ($entity, 'create',$p);

    }
    return civicrm_create_error( "API ($entity,$action) does not exist (join the API team and implement $function" );
  }
  $result = isset($extra) ? $function($params, $extra) : $function($params);
  return $result;
}


function civicrm_api_get_function_name($entity, $action) {
  static $_map;
  if (!isset($_map)) {
    $_map = array();
    $version = civicrm_get_api_version();
    if ($version === 2) {
      $_map['event']['get'] = 'civicrm_event_search';
      $_map['group_roles']['create'] = 'civicrm_group_roles_add_role';
      $_map['group_contact']['create'] = 'civicrm_group_contact_add';
      $_map['group_contact']['delete'] = 'civicrm_group_contact_remove';
      $_map['entity_tag']['create'] = 'civicrm_entity_tag_add';
      $_map['entity_tag']['delete'] = 'civicrm_entity_tag_remove';
      $_map['group']['create'] = 'civicrm_group_add';
      $_map['contact']['create'] = 'civicrm_contact_add';
      $_map['relationship_type']['get'] = 'civicrm_relationship_types_get';
      $_map['uf_join']['create'] = 'civicrm_uf_join_add';
      if (isset($_map[$entity][$action])) {
        return $_map[$entity][$action];
      }
    }
  }
  $function = strtolower(str_replace('U_F','uf', preg_replace('/(?=[A-Z])/','_$0', $entity)));// That's CamelCase, beside an odd UFCamel that is expected as uf_camel
  return 'civicrm_'. $function .'_'. $action;
}


/**
 * We must be sure that every request uses only one version of the API.
 *
 * @param $desired_version : array or integer
 *   One chance to set the version number.
 *   After that, this version number will be used for the remaining request.
 *   This can either be a number, or an array(.., 'version' => $version, ..).
 *   This allows to directly pass the $params array.
 */
function civicrm_get_api_version($desired_version = NULL) {
  static $_version;
  if (!isset($_version)) {
    if (is_array($desired_version)) {
      // someone gave the full $params array.
      $params = $desired_version;
      $desired_version = empty($params['version']) ? NULL : (int) $params['version'];
    }
    if (isset($desired_version)) {
      $_version = $desired_version;
      // echo "\n".'version: '. $_version ." (parameter)\n";
    }
    else if (defined('CIVICRM_API_VERSION')) {
      $_version = CIVICRM_API_VERSION;
      // echo "\n".'version: '. $_version ." (CIVICRM_API_VERSION)\n";
    }
    else {
      // we will set the default to version 3 as soon as we find that it works.
      $_version = 2;
      // echo "\n".'version: '. $_version ." (default)\n";
    }
  }
  return $_version;
}


/**
 * @param $entity
 * @param $rest_interface : boolean
 *   In case of TRUE, we need to set the base path explicitly.
 */
function civicrm_api_include($entity, $rest_interface = FALSE) {
  $version = civicrm_get_api_version();
  $camel_name = civicrm_api_get_camel_name($entity);
  $file = 'api/v'. $version .'/'. $camel_name .'.php';
  
  if ( $rest_interface ) {
      $apiPath = substr( $_SERVER['SCRIPT_FILENAME'], 0, -15 );
      // check to ensure file exists, else die
      if ( ! file_exists( $apiPath . $apiFile ) ) {
          return self::error( 'Unknown function invocation.' );
      }
      $file = $apiPath . $file;
  }
  
  require_once $file;
}


function civicrm_api_get_camel_name($entity) {
  static $_map = NULL;
  if (!isset($_map)) {
    $_map = array();
    $_map['utils'] = 'utils';
    $version = civicrm_get_api_version();
    if ($version === 2) {
      // TODO: Check if $_map needs to contain anything.
      $_map['contribution'] = 'Contribute';
      $_map['custom_field'] = 'CustomGroup';
    }
    else {
      // assume $version == 3.
    }
  }
  if (isset($_map[strtolower($entity)])) {
    return $_map[strtolower($entity)];
  }
  $fragments = explode('_', $entity);
  foreach ($fragments as &$fragment) {
    $fragment = ucfirst($fragment);
  }
  // Special case: UFGroup, UFJoin, UFMatch, UFField
  if ($fragments[0] === 'Uf') {
    $fragments[0] = 'UF';
  }
  return implode('', $fragments);
}



