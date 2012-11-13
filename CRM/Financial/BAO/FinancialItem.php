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

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

class CRM_Financial_BAO_FinancialItem extends CRM_Financial_DAO_FinancialItem {

  /**
   * class constructor
   */
  function __construct( ) {
    parent::__construct( );
  }

  /**
   * Takes a bunch of params that are needed to match certain criteria and
   * retrieves the relevant objects. Typically the valid params are only
   * contact_id. We'll tweak this function to be more full featured over a period
   * of time. This is the inverse function of create. It also stores all the retrieved
   * values in the default array
   *
   * @param array $params   (reference ) an assoc array of name/value pairs
   * @param array $defaults (reference ) an assoc array to hold the flattened values
   *
   * @return object CRM_Contribute_BAO_FinancialItem object
   * @access public
   * @static
   */
  static function retrieve( &$params, &$defaults ) {
    $financialItem = new CRM_Financial_DAO_FinancialItem( );
    $financialItem->copyValues( $params );
    if ( $financialItem->find( true ) ) {
      CRM_Core_DAO::storeValues( $financialItem, $defaults );
      return $financialItem;
    }
    return null;
  }

  /**
   * function to add the financial Items
   *
   * @param array $params reference array contains the values submitted by the form
   * @param array $ids    reference array contains the id
   * 
   * @access public
   * @static 
   * @return object
   */
  static function add( $lineItem, $contribution ) {
    $params = array(
      'transaction_date'  => CRM_Utils_Date::isoToMysql($contribution->receive_date),
      'contact_id'        => $contribution->contact_id, 
      'amount'            => $lineItem->line_total,
      'currency'          => $contribution->currency,
      'status_id'         => 3,
      'entity_table'      => 'civicrm_line_item',
      'entity_id'         => $lineItem->id,
      'description'       => ( $lineItem->qty != 1 ? $lineItem->qty . ' of ' : ''). ' ' . $lineItem->label
    );
    
    if ($lineItem->financial_type_id) {
      $searchParams = array( 
        'entity_table'         => 'civicrm_financial_type',
        'entity_id'            => $lineItem->financial_type_id,
        'account_relationship' => 1
      );

      $result = array( );
      CRM_Financial_BAO_FinancialTypeAccount::retrieve( $searchParams, $result );
      $params['financial_account_id'] = CRM_Utils_Array::value( 'financial_account_id', $result );
    }

    $trxn = CRM_Core_BAO_FinancialTrxn::getFinancialTrxnIds( $contribution->id );
    $trxnId['id'] = $trxn['financialTrxnId']; 
    $int_name = 'txt-price_'.$lineItem->price_field_id;
    $params['init_amount'] =  $lineItem->int_name;
    self::create( $params,null, $trxnId);    
  } 

  static function create( &$params, $ids = null, $trxnId = null  ) {
    $financialItem = new CRM_Financial_DAO_FinancialItem( );
    $financialItem->copyValues( $params );
    if (CRM_Utils_Array::value( 'id', $ids )) {
      $financialItem->id = $ids['id']; 
    }

    $financialItem->save( );
    if (CRM_Utils_Array::value('id', $trxnId)) {
      $entity_financial_trxn_params = array(
        'entity_table'      => "civicrm_financial_item",
        'entity_id'         => $financialItem->id,
        'financial_trxn_id' => $trxnId['id'],
        'amount'            => array_key_exists('init_amount',$params)?$params['init_amount']:$params['amount'],
      );

      if (CRM_Utils_Array::value('init_amount', $params) && $params['init_amount'] != 'NaN') {
        $entity_financial_trxn_params['amount'] = $params['init_amount'];
      }
      else{
        $entity_financial_trxn_params['amount'] = $params['amount'];
      }
      
      $entity_trxn = new CRM_Financial_DAO_EntityFinancialTrxn();
      $entity_trxn->copyValues( $entity_financial_trxn_params );
      if ( CRM_Utils_Array::value( 'entityFinancialTrxnId', $ids ) ) {
        $entity_trxn->id = $ids['entityFinancialTrxnId'];
      }
      $entity_trxn->save();
    }

    $entity_params = array(
      'entity_id' => $financialItem->id,
      'entity_table' => 'civicrm_financial_item',
    );

    $entity_trxn = new CRM_Financial_DAO_EntityFinancialTrxn();
    $entity_trxn->copyValues( $entity_params );
    $entity_trxn->find();
    $line_amount =0;
    while ($entity_trxn->fetch()) {
      $line_amount += $entity_trxn->amount;
    }

    if ($line_amount < $financialItem->amount && $line_amount != 0) {
      $financialItem->status_id = 2;
    }
    elseif ($line_amount == 0) {
      $financialItem->status_id = 3;
    }
    elseif ($line_amount == $financialItem->amount) {
      $financialItem->status_id = 1;
    }

    $financialItem->transaction_date = null;
    $financialItem->save();

    return $financialItem;
  }   

  /**
   * takes an associative array and creates a entity financial transaction object
   *
   * @param array  $params (reference ) an assoc array of name/value pairs
   *
   * @return object CRM_Core_BAO_FinancialTrxn object
   * @access public
   * @static
   */
  static function createEntityTrxn(&$params) {
    $entity_trxn = new CRM_Financial_DAO_EntityFinancialTrxn();
    if (CRM_Utils_Array::value('id', $params)) {
      $entity_trxn->id = $params['id'];
      $entity_trxn->find(true);
    }
    $entity_trxn->copyValues($params);
    $entity_trxn->save();
    return $entity_trxn;
  }

  static function retrieveEntityFinancialTrxn( &$params ) {
    $financialItem = new CRM_Financial_DAO_EntityFinancialTrxn( );
    $financialItem->copyValues( $params );
    $financialItem->find();
    while ( $financialItem->fetch() ) {
      $financialItems[$financialItem->id] = array(
        'id'                => $financialItem->id,
        'entity_table'      => $financialItem->entity_table,
        'entity_id'         => $financialItem->entity_id,
        'financial_trxn_id' => $financialItem->financial_trxn_id,
        'amount'            => $financialItem->amount,
      );
    } 
    if (!empty($financialItems)) {
      return $financialItems;
    }
    else {
      return null;
    }
  }

  static function retrieveMaxEntityFinancialTrxn( &$params ) {
    $query = "select * from civicrm_entity_financial_trxn where id = (Select max(id) from civicrm_entity_financial_trxn where ";
    $where = "";
    foreach($params as $field=>$value) {
      if (empty($where)) {
        $where .="$field = '$value'";
      }
      else {
        $where .=" AND $field = '$value'";
      }
    }

    $where .= ');';
    $query .=$where;
    $dao = CRM_Core_DAO::executeQuery($query);
    $dao->fetch();
    return $dao;
  } 

  static function retrievePreviousAmount( &$params ) {
    $entity_trxn = new CRM_Financial_DAO_EntityFinancialTrxn();
    $entity_trxn->copyValues( $params );
    $entity_trxn->find();
    $line_amount =0;
    while($entity_trxn->fetch()) {
      $line_amount += $entity_trxn->amount;
    }
    return $line_amount;
  }
}