<?php
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */

/**
 *
 * @package CRM
 * @author Marshal Newrock <marshal@idealso.com>
 * $Id$
 */

/**
 * Business objects for Line Items generated by monetary transactions
 */
class CRM_Price_BAO_LineItem extends CRM_Price_DAO_LineItem {

  /**
   * Creates a new entry in the database.
   *
   * @param array $params (reference) an assoc array of name/value pairs
   *
   * @return object CRM_Price_DAO_LineItem object
   * @access public
   * @static
   */
  static function create(&$params) {
    //create mode only as we don't support editing line items

    CRM_Utils_Hook::pre('create', 'LineItem', $params['entity_id'], $params);
    
    $lineItemBAO = new CRM_Price_BAO_LineItem();
    $lineItemBAO->copyValues($params);

    $return = $lineItemBAO->save();

    CRM_Utils_Hook::post('create', 'LineItem', $params['entity_id'], $params);

    return $return;
  }

  /**
   * Takes a bunch of params that are needed to match certain criteria and
   * retrieves the relevant objects.  Typically, the valid params are only
   * price_field_id.  This is the inverse function of create.  It also
   * stores all of the retrieved values in the default array.
   *
   * @param array $params   (reference ) an assoc array of name/value pairs
   * @param array $defaults (reference ) an assoc array to hold the flattened values
   *
   * @return object CRM_Price_BAO_LineItem object
   * @access public
   * @static
   */
  static function retrieve(&$params, &$defaults) {
    $lineItem = new CRM_Price_BAO_LineItem();
    $lineItem->copyValues($params);
    if ($lineItem->find(TRUE)) {
      CRM_Core_DAO::storeValues($lineItem, $defaults);
      return $lineItem;
    }
    return NULL;
  }

  /**
   * Given a participant id/contribution id,
   * return contribution/fee line items
   *
   * @param $entityId  int    participant/contribution id
   * @param $entity    string participant/contribution.
   *
   * @return array of line items
   */
  static function getLineItems($entityId, $entity = 'participant', $isQuick = NULL) {
    $selectClause = $whereClause = $fromClause = NULL;
    $selectClause = "
      SELECT    li.id,
      li.label,
      li.qty,
      li.unit_price,
      li.line_total,
      pf.label as field_title,
      pf.html_type,
      pfv.membership_type_id,
      li.price_field_id,
      li.participant_count,
      li.price_field_value_id,
      pfv.description";

    $fromClause = "
      FROM      civicrm_%2 as %2
      LEFT JOIN civicrm_line_item li ON ( li.entity_id = %2.id AND li.entity_table = 'civicrm_%2')
      LEFT JOIN civicrm_price_field_value pfv ON ( pfv.id = li.price_field_value_id )
      LEFT JOIN civicrm_price_field pf ON (pf.id = li.price_field_id )";
    $whereClause = "
      WHERE     %2.id = %1";

    if ($isQuick) {
      $fromClause .= " LEFT JOIN civicrm_price_set cps on cps.id = pf.price_set_id ";
      $whereClause .= " and cps.is_quick_config = 0";
    }
    $lineItems = array();

    if (!$entityId || !$entity || !$fromClause) {
      return $lineItems;
    }

    $params = array(
      1 => array($entityId, 'Integer'),
      2 => array($entity, 'Text'),
    );

    $dao = CRM_Core_DAO::executeQuery("$selectClause $fromClause $whereClause", $params);
    while ($dao->fetch()) {
      if (!$dao->id) {
        continue;
      }
      $lineItems[$dao->id] = array(
        'qty' => $dao->qty,
        'label' => $dao->label,
        'unit_price' => $dao->unit_price,
        'line_total' => $dao->line_total,
        'price_field_id' => $dao->price_field_id,
        'participant_count' => $dao->participant_count,
        'price_field_value_id' => $dao->price_field_value_id,
        'field_title' => $dao->field_title,
        'html_type' => $dao->html_type,
        'description' => $dao->description,
        'entity_id' => $entityId,
        'membership_type_id' => $dao->membership_type_id,
      );
    }
    return $lineItems;
  }

  /**
   * This method will create the lineItem array required for
   * processAmount method
   *
   * @param  int   $fid       price set field id
   * @param  array $params    referance to form values
   * @param  array $fields    referance to array of fields belonging
   *                          to the price set used for particular event
   * @param  array $values    referance to the values array(
     this is
   *                          lineItem array)
   *
   * @return void
   * @access static
   */
  static function format($fid, &$params, &$fields, &$values) {
    if (empty($params["price_{$fid}"])) {
      return;
    }

    $optionIDs = implode(',', array_keys($params["price_{$fid}"]));

    //lets first check in fun parameter,
    //since user might modified w/ hooks.
    $options = array();
    if (array_key_exists('options', $fields)) {
      $options = $fields['options'];
    }
    else {
      CRM_Price_BAO_FieldValue::getValues($fid, $options, 'weight', TRUE);
    }
    $fieldTitle = CRM_Utils_Array::value('label', $fields);
    if (!$fieldTitle) {
      $fieldTitle = CRM_Core_DAO::getFieldValue('CRM_Price_DAO_Field', $fid, 'label');
    }

    foreach ($params["price_{$fid}"] as $oid => $qty) {
      $price = $options[$oid]['amount'];

      // lets clean the price in case it is not yet cleant
      // CRM-10974
      $price = CRM_Utils_Rule::cleanMoney($price);

      $participantsPerField = CRM_Utils_Array::value('count', $options[$oid], 0);

      $values[$oid] = array(
        'price_field_id' => $fid,
        'price_field_value_id' => $oid,
        'label' => CRM_Utils_Array::value('label', $options[$oid]),
        'field_title' => $fieldTitle,
        'description' => CRM_Utils_Array::value('description', $options[$oid]),
        'qty' => $qty,
        'unit_price' => $price,
        'line_total' => $qty * $price,
        'participant_count' => $qty * $participantsPerField,
        'max_value' => CRM_Utils_Array::value('max_value', $options[$oid]),
        'membership_type_id' => CRM_Utils_Array::value('membership_type_id', $options[$oid]),
        'membership_num_terms' => CRM_Utils_Array::value('membership_num_terms', $options[$oid]),
        'auto_renew' => CRM_Utils_Array::value('auto_renew', $options[$oid]),
        'html_type' => $fields['html_type'],
        'financial_type_id' => CRM_Utils_Array::value( 'financial_type_id', $options[$oid]),
        
      );
      if ($values[$oid]['membership_type_id'] && !isset($values[$oid]['auto_renew'])) {
        $values[$oid]['auto_renew'] = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipType', $values[$oid]['membership_type_id'], 'auto_renew');                                      
      }
    }
  }

  /**
   * Delete line items for given entity.
   *
   * @param int $entityId
   * @param int $entityTable
   *
   * @access public
   * @static
   */
  public static function deleteLineItems($entityId, $entityTable) {
    $result = FALSE;
    if (!$entityId || !$entityTable) {
      return $result;
    }

    if ($entityId && !is_array($entityId)) {
      $entityId = array($entityId);
    }

    $query = "DELETE FROM civicrm_line_item where entity_id IN ('" . implode("','", $entityId) . "') AND entity_table = '$entityTable'";
    $dao = CRM_Core_DAO::executeQuery($query);
    return $result;
  }

  /**
   * Function to process price set and line items.
   * @param int $contributionId contribution id
   * @param array $lineItem line item array
   * @param object $contributionDetails
   * @param decimal $initAmount amount
   * @param string $entityTable entity table
   *
   * @access public
   * @return void
   * @static
   */
  static function processPriceSet($entityId, $lineItem, $contributionDetails = NULL, $entityTable = 'civicrm_contribution', $update = FALSE) {
    if (!$entityId || !is_array($lineItem)
      || CRM_Utils_system::isNull($lineItem)
    ) {
      return;
    }
    
    foreach ($lineItem as $priceSetId => $values) {
      if (!$priceSetId) {
        continue;
      }

      foreach ($values as $line) {
        $line['entity_table'] = $entityTable;
        $line['entity_id'] = $entityId;
        // if financial type is not set and if price field value is NOT NULL
        // get financial type id of price field value
        if (CRM_Utils_Array::value('price_field_value_id', $line) && !CRM_Utils_Array::value('financial_type_id', $line)) {
          $line['financial_type_id'] = CRM_Core_DAO::getFieldValue('CRM_Price_DAO_FieldValue', $line['price_field_value_id'], 'financial_type_id');
        }
        $lineItems = CRM_Price_BAO_LineItem::create($line);
        if (!$update && $contributionDetails) {
          CRM_Financial_BAO_FinancialItem::add($lineItems, $contributionDetails);
        }
      }
    }
  } 

  public static function syncLineItems($entityId, $entityTable = 'civicrm_contribution', $amount, $otherParams = NULL) {
    if (!$entityId || CRM_Utils_System::isNull($amount))
      return;

    $from = " civicrm_line_item li
      LEFT JOIN   civicrm_price_field pf ON pf.id = li.price_field_id
      LEFT JOIN   civicrm_price_set ps ON ps.id = pf.price_set_id ";

    $set = " li.unit_price = %3, 
      li.line_total = %3 ";

    $where = " li.entity_id = %1 AND 
      li.entity_table = %2 ";

    $params = array(
      1 => array($entityId, 'Integer'),
      2 => array($entityTable, 'String'),
      3 => array($amount, 'Float'),
    );

    if ($entityTable == 'civicrm_contribution') {
      $entityName = 'default_contribution_amount';
      $where .= " AND ps.name = %4 ";
      $params[4] = array($entityName, 'String'); 
    } 
    elseif ($entityTable == 'civicrm_participant') {
      $from .= "
        LEFT JOIN civicrm_price_set_entity cpse ON cpse.price_set_id = ps.id
        LEFT JOIN civicrm_price_field_value cpfv ON cpfv.price_field_id = pf.id and cpfv.label = %4 ";
      $set .= " ,li.label = %4,
        li.price_field_value_id = cpfv.id ";
      $where .= " AND cpse.entity_table = 'civicrm_event' AND cpse.entity_id = %5 ";
      $amount = empty($amount) ? 0: $amount;
      $params += array(
        4 => array($otherParams['fee_label'], 'String'),
        5 => array($otherParams['event_id'], 'String'),
      );
    }

    $query = "                                                                                                                                                                                             
      UPDATE $from
      SET    $set
      WHERE  $where    
      ";

    CRM_Core_DAO::executeQuery($query, $params);
  }

   /**
   * Function to build line items array.
   * @param int $params form values
   *
   * @param string $entityId entity id
   *
   * @param string $entityTable entity Table
   *
   * @access public
   * @return void
   * @static
   */
  static function getLineItemArray(&$params, $entityId = NULL, $entityTable = 'contribution') {
    
    if (!$entityId) {
      $priceSetDetails = CRM_Price_BAO_Set::getDefaultPriceSet();
      foreach ($priceSetDetails as $values) {
        $params['line_item'][$values['setID']][$values['priceFieldID']] = array(
          'price_field_id' => $values['priceFieldID'],
          'price_field_value_id' => $values['priceFieldValueID'],
          'label' => $values['label'],
          'qty' => 1,
          'unit_price' => $params['total_amount'],
          'line_total' => $params['total_amount'],
          'financial_type_id' => $params['financial_type_id']
        );
      }
    } 
    else {
      $setID = NULL;
      $lineItems = CRM_Price_BAO_LineItem::getLineItems($entityId, $entityTable);
      foreach ($lineItems as $key => $values) {
        if (!$setID) {
          $setID = CRM_Core_DAO::getFieldValue('CRM_Price_DAO_Field', $values['price_field_id'], 'price_set_id');
          $params['is_quick_config'] = CRM_Core_DAO::getFieldValue('CRM_Price_DAO_Set', $setID, 'is_quick_config');
        }
        if (CRM_Utils_Array::value('is_quick_config', $params) && array_key_exists('total_amount', $params)) {
          $values['line_total'] = $values['unit_price'] = $params['total_amount'];
        }
        $values['id'] = $key;
        $params['line_item'][$setID][$key] = $values;
      }
    }
  }
}
