<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.0                                                |
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

/*
 *DRAFT CODE WRITTEN BY EILEEN still dev version (pre-ALPHA)
 *Starting point was Contribute API & some portions are still just that with
 *contribute replaced by pledge & not yet tested
 * have only been using create, delete functionality
 */

/**
 * File for the CiviCRM APIv2 Pledge functions
 *
 * @package CiviCRM_APIv2
 * @subpackage API_Pledge_Payment
 *
 * @copyright CiviCRM LLC (c) 2004-2011
 * @version $Id: PledgePayment.php
 *
 */

/**
 * Include utility functions
 */
require_once 'api/v3/utils.php';
require_once 'CRM/Utils/Rule.php';

/**
 * Add or update a plege payment. Pledge Payment API doesn't actually add a pledge 
 *  if the request is to 'create' and 'id' is not passed in
 * the oldest pledge with no associated contribution is updated
 *
 * @todo possibly add ability to add payment if there are less payments than pledge installments
 * @todo possibily add ability to recalc dates if the schedule is changed
 * 
 * @param  array   $params           (reference ) input parameters
 *
 * @return array (reference )        pledge_id of created or updated record
 * @static void
 * @access public
 */
function civicrm_api3_pledge_payment_create( $params ) {
  _civicrm_api3_initialize(true );
  try{
    civicrm_api3_verify_mandatory($params,null,array('pledge_id','status_id'));

    require_once 'CRM/Pledge/BAO/Payment.php';
    if (empty($params['id'])){
      $paymentDetails = CRM_Pledge_BAO_Payment::getOldestPledgePayment($params['pledge_id']);
      $paymentParams = array_merge($params,$paymentDetails);
    } else{
      $paymentParams =$params;
    }
   
    $dao = CRM_Pledge_BAO_Payment::add( $paymentParams );
     _civicrm_api3_object_to_array($dao, $result[$dao->id]);
    
   
    //update pledge status
     CRM_Pledge_BAO_Payment::updatePledgePaymentStatus( $params['pledge_id']);
    
    return civicrm_api3_create_success( $result ,$params,$dao);
  } catch (PEAR_Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  } catch (Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  }
   
}

/**
 * Retrieve a specific pledge, given a set of input params
 * If more than one pledge exists, return an error, unless
 * the client has requested to return the first found contact
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return array (reference )        array of properties, if error an array with an error id and error message
 * @static void
 * @access public

 function &civicrm_pledge_payment_get( $params ) {
 _civicrm_initialize( );
 // copied from contribute code - not touched at all to make work for pledge or tested
 $values = array( );
 if ( empty( $params ) ) {
 return civicrm_create_error( ts( 'No input parameters present' ) );
 }

 if ( ! is_array( $params ) ) {
 return civicrm_create_error( ts( 'Input parameters is not an array' ) );
 }

 $pledges =& civicrm_pledge_search( $params );
 if ( civicrm_error( $pledges ) ) {
 return $pledges;
 }

 if ( count( $pledges ) != 1 &&
 ! $params['returnFirst'] ) {
 return civicrm_create_error( ts( '%1 pledges matching input params', array( 1 => count( $pledges ) ) ),
 $pledges );
 }

 $payments = array_values( $pledges );
 return $pledges[0];
 }
 */
/**
 * Delete a pledge Payment - Note this deletes the contribution not just the link
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return boolean        true if success, else false
 * @static void
 * @access public
 */
function civicrm_api3_pledge_payment_delete( $params ) {
  _civicrm_api3_initialize(true );
  try{
    civicrm_api3_verify_mandatory($params,null,array('id'));
    $id = CRM_Utils_Array::value( 'id', $params );
    require_once 'CRM/Pledge/BAO/Pledge.php';
    if ( CRM_Pledge_BAO_Payment::deletePayments( $id ) ) {
      return civicrm_api3_create_success( array('id' => $id),$params);
    } else {
      return civicrm_api3_create_error(  'Could not delete payment'  );
    }

  } catch (PEAR_Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  } catch (Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  }
}

/**
 * Retrieve a set of pledges, given a set of input params
 *
 * @param  array   $params           (reference ) input parameters
 * @param array    $returnProperties Which properties should be included in the
 *                                   returned pledge object. If NULL, the default
 *                                   set of properties will be included.
 *
 * @return array (reference )        array of pledges, if error an array with an error id and error message
 * @static void
 * @access public
 */
function civicrm_api3_pledge_payment_get( $params ) {

try {
  _civicrm_api3_initialize( true );

    civicrm_api3_verify_mandatory($params);
    require_once 'CRM/Pledge/BAO/Payment.php';
    $bao = new CRM_Pledge_BAO_Payment();
    $fields = array_keys($bao->fields());
    foreach ( $fields as $name) {
        if (array_key_exists($name, $params)) {
            $bao->$name = $params[$name];
        }
    }

    if(empty($params['pledge_payment_id']) && isset($params['id'])){
      $bao->id = $params['id'];   
    }

    if ( $bao->find() ) {
      $results = array();
      while ( $bao->fetch() ) {
        _civicrm_api3_object_to_array( $bao, $result );
        $results[$bao->id] = $result;
      }
 
      return civicrm_api3_create_success($results,$params,$bao);
    } else {
      return civicrm_api3_create_success(array(),$params,$bao);
    }

  } catch (PEAR_Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  } catch (Exception $e) {
    return civicrm_api3_create_error( $e->getMessage() );
  }
}


function updatePledgePayments( $pledgeId, $paymentStatusId, $paymentIds  ){
  _civicrm_api3_initialize(true );
  require_once 'CRM/Pledge/BAO/Pledge.php';
  $result = updatePledgePayments( $pledgeId, $paymentStatusId, $paymentIds = null );
  return $result;

}

