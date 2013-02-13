<?php
// $Id$

/*
  +--------------------------------------------------------------------+
  | CiviCRM version 4.3                                                |
  +--------------------------------------------------------------------+
  | Copyright CiviCRM LLC (c) 2004-2013                                |
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

/**
 * File for the CiviCRM APIv3 Case functions
 * Developed by woolman.org
 *
 * @package CiviCRM_APIv3
 * @subpackage API_Case
 * @copyright CiviCRM LLC (c) 2004-2013
 *
 */


/**
 * Open a new case, add client and manager roles, and add standard timeline
 *
 * @param  array(
    //REQUIRED:
 * 'case_type_id' => int OR
 * 'case_type' => str (provide one or the other)
 * 'contact_id' => int // case client
 * 'subject' => str
 *
 * //OPTIONAL
 * 'medium_id' => int // see civicrm option values for possibilities
 * 'creator_id' => int // case manager, default to the logged in user
 * 'status_id' => int // defaults to 1 "ongoing"
 * 'location' => str
 * 'start_date' => str datestamp // defaults to: date('YmdHis')
 * 'duration' => int // in minutes
 * 'details' => str // html format
 *
 * @return sucessfully opened case
 *
 * @access public
 * {@getfields case_create}
 */
function civicrm_api3_case_create($params) {

  if (!empty($params['id'])) {
    return civicrm_api3_case_update($params);
  }

  civicrm_api3_verify_mandatory($params, NULL, array('contact_id', 'subject', array('case_type', 'case_type_id')));
  _civicrm_api3_case_format_params($params);

  // If format_params didn't find what it was looking for, return error
  if (empty($params['case_type_id'])) {
    return civicrm_api3_create_error('Invalid case_type. No such case type exists.');
  }
  if (empty($params['case_type'])) {
    return civicrm_api3_create_error('Invalid case_type_id. No such case type exists.');
  }

  $caseBAO = CRM_Case_BAO_Case::create($params);

  if (!$caseBAO) {
    return civicrm_api3_create_error('Case not created. Please check input params.');
  }

  foreach ((array) $params['contact_id'] as $cid) {
    $contactParams = array('case_id' => $caseBAO->id, 'contact_id' => $cid);
    CRM_Case_BAO_Case::addCaseToContact($contactParams);
  }

  // Initialize XML processor with $params
  $xmlProcessor = new CRM_Case_XMLProcessor_Process();
  $xmlProcessorParams = array(
    'clientID' => $params['contact_id'],
    'creatorID' => $params['creator_id'],
    'standardTimeline' => 1,
    'activityTypeName' => 'Open Case',
    'caseID' => $caseBAO->id,
    'subject' => $params['subject'],
    'location' => CRM_Utils_Array::value('location', $params),
    'activity_date_time' => $params['start_date'],
    'duration' => CRM_Utils_Array::value('duration', $params),
    'medium_id' => CRM_Utils_Array::value('medium_id', $params),
    'details' => CRM_Utils_Array::value('details', $params),
    'custom' => array(),
  );

  // Do it! :-D
  $xmlProcessor->run($params['case_type'], $xmlProcessorParams);

  // return case
  $values = array();
  _civicrm_api3_object_to_array($caseBAO, $values[$caseBAO->id]);

  return civicrm_api3_create_success($values, $params, 'case', 'create', $caseBAO);
}

/*
 * Adjust Metadata for Get Action
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_case_get_spec(&$params) {
  $params['contact_id']['api.aliases'] = array('client_id');
  $params['contact_id']['title'] = 'Case Client';
}

/*
 * Adjust Metadata for Create Action
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_case_create_spec(&$params) {
  $params['contact_id']['api.aliases'] = array('client_id');
  $params['contact_id']['title'] = 'Case Client';
  $params['contact_id']['api.required'] = 1;
  $params['status_id']['api.default'] = 1;
}

/*
 * Adjust Metadata for Update action
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_case_update_spec(&$params) {
  $params['id']['api.required'] = 1;
}

/*
 * Adjust Metadata for Delete action
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_case_delete_spec(&$params) {
  $params['id']['api.required'] = 1;
}


/**
 * Get details of a particular case, or search for cases, depending on params
 *
 * Please provide one (and only one) of the four get/search parameters:
 *
 * @param array(
    'id' => if set, will get all available info about a case, including contacts and activities
 *
 * // if no case_id provided, this function will use one of the following search parameters:
 * 'client_id' => finds all cases with a specific client
 * 'activity_id' => returns the case containing a specific activity
 * 'contact_id' => finds all cases associated with a contact (in any role, not just client)
 *
 * {@getfields case_get}
 *
 * @return (get mode, case_id provided): Array with case details, case roles, case activity ids, (search mode, case_id not provided): Array of cases found
 * @access public
 * @todo Erik Hommel 16 dec 2010 check if all DB fields are returned
 */
function civicrm_api3_case_get($params) {
  $options = _civicrm_api3_get_options_from_params($params);

  // Get by id
  $caseId = CRM_Utils_Array::value('id', $params);
  if ($caseId) {
    // Validate param
    if (!is_numeric($caseId)) {
      return civicrm_api3_create_error('Invalid parameter: case_id. Must provide a numeric value.');
    }
    // For historic reasons we always return these when an id is provided
    $options['return'] = array('contacts' => 1, 'activities' => 1);
    $case = _civicrm_api3_case_read($caseId, $options);

    if ($case) {
      return civicrm_api3_create_success(array($caseId => $case));
    }
    else {
      return civicrm_api3_create_success(array());
    }
  }

  //search by client
  if (!empty($params['client_id'])) {
    $ids = array();
    foreach ((array) $params['client_id'] as $cid) {
      if (is_numeric($cid)) {
        $ids = array_merge($ids, CRM_Case_BAO_Case::retrieveCaseIdsByContactId($cid, TRUE));
      }
    }
    $cases = array();
    foreach ($ids as $id) {
      if ($case = _civicrm_api3_case_read($id, $options)) {
        $cases[$id] = $case;
      }
    }
    return civicrm_api3_create_success($cases);
  }

  //search by activity
  if (!empty($params['activity_id'])) {
    if (!is_numeric($params['activity_id'])) {
      return civicrm_api3_create_error('Invalid parameter: activity_id. Must provide a numeric value.');
    }
    $caseId = CRM_Case_BAO_Case::getCaseIdByActivityId($params['activity_id']);
    if (!$caseId) {
      return civicrm_api3_create_success(array());
    }
    $case = array($caseId => _civicrm_api3_case_read($caseId, $options));
    return civicrm_api3_create_success($case);
  }

  //search by contacts
  if ($contact = CRM_Utils_Array::value('contact_id', $params)) {
    if (!is_numeric($contact)) {
      return civicrm_api3_create_error('Invalid parameter: contact_id.  Must provide a numeric value.');
    }

    $sql = "
SELECT DISTINCT case_id
  FROM civicrm_relationship
 WHERE (contact_id_a = $contact
    OR contact_id_b = $contact)
   AND case_id IS NOT NULL";
    $dao = &CRM_Core_DAO::executeQuery($sql);

    $cases = array();
    while ($dao->fetch()) {
      $cases[$dao->case_id] = _civicrm_api3_case_read($dao->case_id, $options);
    }
    return civicrm_api3_create_success($cases);
  }

  return civicrm_api3_create_error('Missing required parameter. Must provide case_id, client_id, activity_id, or contact_id.');
}

/**
 * Deprecated. Use activity API instead
 */
function civicrm_api3_case_activity_create($params) {
  return civicrm_api3_activity_create($params);
}

/**
 * Update a specified case.
 *
 * @param  array(
    //REQUIRED:
 * 'case_id' => int
 *
 * //OPTIONAL
 * 'status_id' => int
 * 'start_date' => str datestamp
 * 'contact_id' => int // case client
 *
 * @return Updated case
 *
 * @access public
 *
 */
function civicrm_api3_case_update($params) {
  //check parameters
  civicrm_api3_verify_mandatory($params, NULL, array('id'));

  // return error if modifing creator id
  if (array_key_exists('creator_id', $params)) {
    return civicrm_api3_create_error(ts('You cannot update creator id'));
  }

  $mCaseId = array();
  $origContactIds = array();

  // get original contact id and creator id of case
  if ($params['contact_id']) {
    $origContactIds = CRM_Case_BAO_Case::retrieveContactIdsByCaseId($params['case_id']);
    $origContactId = $origContactIds[1];
  }

  if (count($origContactIds) > 1) {
    // check valid orig contact id
    if ($params['orig_contact_id'] && !in_array($params['orig_contact_id'], $origContactIds)) {
      return civicrm_api3_create_error('Invalid case contact id (orig_contact_id)');
    }
    elseif (!$params['orig_contact_id']) {
      return civicrm_api3_create_error('Case is linked with more than one contact id. Provide the required params orig_contact_id to be replaced');
    }
    $origContactId = $params['orig_contact_id'];
  }

  // check for same contact id for edit Client
  if ($params['contact_id'] && !in_array($params['contact_id'], $origContactIds)) {
    $mCaseId = CRM_Case_BAO_Case::mergeCases($params['contact_id'], $params['case_id'], $origContactId, NULL, TRUE);
  }

  if (CRM_Utils_Array::value('0', $mCaseId)) {
    $params['case_id'] = $mCaseId[0];
  }

  $dao = new CRM_Case_BAO_Case();
  $dao->id = $params['id'];

  $dao->copyValues($params);
  $dao->save();

  $case = array();

  _civicrm_api3_object_to_array($dao, $case);

  return civicrm_api3_create_success($case);
}

/**
 * Delete a specified case.
 *
 * @param  array(
    //REQUIRED:
 * 'id' => int
 *
 * //OPTIONAL
 * 'move_to_trash' => bool (defaults to false)
 *
 * @return boolean: true if success, else false
 * {@getfields case_delete}
 * @access public
 */
function civicrm_api3_case_delete($params) {
  //check parameters
  civicrm_api3_verify_mandatory($params, NULL, array('id'));

  if (CRM_Case_BAO_Case::deleteCase($params['id'], CRM_Utils_Array::value('move_to_trash', $params, FALSE))) {
    return civicrm_api3_create_success($params);
  }
  else {
    return civicrm_api3_create_error('Could not delete case.');
  }
}

/***********************************/
/*                                 */
/*     INTERNAL FUNCTIONS          */
/*                                 */
/***********************************/

/**
 * Internal function to retrieve a case.
 *
 * @param int $caseId
 *
 * @return array (reference) case object
 *
 */
function _civicrm_api3_case_read($caseId, $options) {
  $return = CRM_Utils_Array::value('return', $options, array());
  $dao = new CRM_Case_BAO_Case();
  $dao->id = $caseId;
  if ($dao->find(TRUE)) {
    $case = array();
    _civicrm_api3_object_to_array($dao, $case);
    $case['client_id'] = $dao->retrieveContactIdsByCaseId($caseId);

    //handle multi-value case type
    $sep = CRM_Core_DAO::VALUE_SEPARATOR;
    $case['case_type_id'] = trim(str_replace($sep, ',', $case['case_type_id']), ',');

    if (!empty($return['contacts'])) {
      //get case contacts
      $contacts = CRM_Case_BAO_Case::getcontactNames($caseId);
      $relations = CRM_Case_BAO_Case::getRelatedContacts($caseId);
      $case['contacts'] = array_merge($contacts, $relations);
    }
    if (!empty($return['activities'])) {
      //get case activities
      $case['activities'] = array();
      $query = "SELECT activity_id FROM civicrm_case_activity WHERE case_id = $caseId AND is_current_revision = 1";
      $dao = CRM_Core_DAO::executeQuery($query);
      while ($dao->fetch()) {
        $case['activities'][] = $dao->activity_id;
      }
    }
    return $case;
  }
}

/**
 * Internal function to format create params for processing
 */
function _civicrm_api3_case_format_params(&$params) {
  if (!array_key_exists('creator_id', $params)) {
    $session = CRM_Core_Session::singleton();
    $params['creator_id'] = $session->get('userID');
  }
  if (empty($params['start_date'])) {
    $params['start_date'] = date('YmdHis');
  }
  // figure out case type id from case type and vice-versa
  $caseTypes = CRM_Case_PseudoConstant::caseType('name', FALSE);
  if (empty($params['case_type_id'])) {
    $params['case_type_id'] = array_search($params['case_type'], $caseTypes);
  }
  elseif (empty($params['case_type'])) {
    $params['case_type'] = $caseTypes[$params['case_type_id']];
  }
  // format input with value separators
  $sep = CRM_Core_DAO::VALUE_SEPARATOR;
  $params['case_type_id'] = $sep . implode($sep, (array) $params['case_type_id']) . $sep;
}
