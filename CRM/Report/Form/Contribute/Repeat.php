<?php
// $Id$

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
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
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 *
 */
class CRM_Report_Form_Contribute_Repeat extends CRM_Report_Form {
  protected $_amountClauseWithAND = NULL;

  function __construct() {
    $this->_columns = array(
      'civicrm_contact' =>
      array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'grouping' => 'contact-fields',
        'fields' =>
        array(
          'sort_name' =>
          array('title' => ts('Contact Name'),
            'no_repeat' => TRUE,
            'default' => TRUE,
          ),
          'id' =>
          array(
            'no_display' => TRUE,
            'required' => TRUE,
          ),
        ),
        'filters' =>
        array(
          'percentage_change' =>
          array('title' => ts('Percentage Change'),
            'type' => CRM_Utils_Type::T_INT,
            'operatorType' => CRM_Report_Form::OP_INT,
            'name' => 'percentage_change',
            'dbAlias' => '( ( contribution_civireport2.total_amount_sum - contribution_civireport1.total_amount_sum ) * 100 / contribution_civireport1.total_amount_sum )',
          ),
        ),
        'group_bys' =>
        array(
          'id' =>
          array('title' => ts('Contact'),
            'default' => TRUE,
          ),
        ),
      ),
      'civicrm_email' =>
      array(
        'dao' => 'CRM_Core_DAO_Email',
        'fields' =>
        array(
          'email' =>
          array('title' => ts('Email'),
            'no_repeat' => TRUE,
          ),
        ),
        'grouping' => 'contact-fields',
      ),
      'civicrm_phone' =>
      array(
        'dao' => 'CRM_Core_DAO_Phone',
        'fields' =>
        array(
          'phone' =>
          array('title' => ts('Phone'),
            'no_repeat' => TRUE,
          ),
        ),
        'grouping' => 'contact-fields',
      ),
      'civicrm_address' =>
      array(
        'dao' => 'CRM_Core_DAO_Address',
        'grouping' => 'contact-fields',
        'fields' =>
        array(
          'country_id' =>
          array('title' => ts('Country')),
          'state_province_id' =>
          array('title' => ts('State/Province')),
        ),
        'group_bys' =>
        array(
          'country_id' =>
          array('title' => ts('Country')),
          'state_province_id' =>
          array('title' => ts('State/Province'),
          ),
        ),
      ),
      'civicrm_contribution_type' =>
      array(
        'dao' => 'CRM_Contribute_DAO_ContributionType',
        'fields' =>
        array(
          'contribution_type' => NULL,
        ),
        'grouping' => 'contri-fields',
        'group_bys' =>
        array(
          'contribution_type' =>
          array('name' => 'id',
                'title' => 'Contribution Type' ),
        ),
      ),
      'civicrm_contribution' =>
      array(
        'dao' => 'CRM_Contribute_DAO_Contribution',
        'fields' =>
        array(
          'contribution_source' => NULL,
          'total_amount1' =>
          array(
            'name' => 'total_amount',
            'alias' => 'contribution1',
            'title' => ts('Range One Stat'),
            'type' => CRM_Utils_Type::T_MONEY,
            'default' => TRUE,
            'required' => TRUE,
            'clause' => '
contribution_civireport1.total_amount_count as contribution1_total_amount_count, 
contribution_civireport1.total_amount_sum as contribution1_total_amount_sum',
          ),
          'total_amount2' =>
          array(
            'name' => 'total_amount',
            'alias' => 'contribution2',
            'title' => ts('Range Two Stat'),
            'type' => CRM_Utils_Type::T_MONEY,
            'default' => TRUE,
            'required' => TRUE,
            'clause' => '
contribution_civireport2.total_amount_count as contribution2_total_amount_count, 
contribution_civireport2.total_amount_sum as contribution2_total_amount_sum',
          ),
        ),
        'grouping' => 'contri-fields',
        'filters' =>
        array(
          'receive_date1' =>
          array('title' => ts('Date Range One'),
            'default' => 'previous.year',
            'type' => CRM_Utils_Type::T_DATE,
            'operatorType' => CRM_Report_Form::OP_DATE,
            'name' => 'receive_date',
          ),
          'receive_date2' =>
          array('title' => ts('Date Range Two'),
            'default' => 'this.year',
            'type' => CRM_Utils_Type::T_DATE,
            'operatorType' => CRM_Report_Form::OP_DATE,
            'name' => 'receive_date',
          ),
          'total_amount1' =>
          array('title' => ts('Range One Amount'),
            'type' => CRM_Utils_Type::T_INT,
            'operatorType' => CRM_Report_Form::OP_INT,
            'name' => 'total_amount',
          ),
          'total_amount2' =>
          array('title' => ts('Range Two Amount'),
            'type' => CRM_Utils_Type::T_INT,
            'operatorType' => CRM_Report_Form::OP_INT,
            'name' => 'total_amount',
          ),
          'contribution_type_id' =>
          array('title' => ts('Contribution Type'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::contributionType(),
          ),
          'contribution_status_id' =>
          array('title' => ts('Contribution Status'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::contributionStatus(),
            'default' => array('1'),
          ),
        ),
        'group_bys' =>
        array('contribution_source' => NULL),
      ),
      'civicrm_group' =>
      array(
        'dao' => 'CRM_Contact_DAO_GroupContact',
        'alias' => 'cgroup',
        'filters' =>
        array(
          'gid' =>
          array(
            'name' => 'group_id',
            'title' => ts('Group'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'group' => TRUE,
            'options' => CRM_Core_PseudoConstant::group(),
          ),
        ),
      ),
    );

    $this->_tagFilter = TRUE;

    parent::__construct();
  }

  function preProcess() {
    parent::preProcess();
  }

  function setDefaultValues($freeze = TRUE) {
    return parent::setDefaultValues($freeze);
  }

  function select() {
    $select = array();
    $append = NULL;

    // since contact fields not related to contribution type
    if (array_key_exists('contribution_type', $this->_params['group_bys']) ||
      array_key_exists('contribution_source', $this->_params['group_bys'])
    ) {
      unset($this->_columns['civicrm_contact']['fields']['id']);
    }

    /*         if ( array_key_exists('country_id', $this->_params['group_bys']) ) { */


    /*             $this->_columns['civicrm_contribution']['fields']['total_amount1']['clause'] = ' */


    /* SUM(contribution1_total_amount_count) as contribution1_total_amount_count, */


    /* SUM(contribution1_total_amount_sum)   as contribution1_total_amount_sum'; */


    /*             $this->_columns['civicrm_contribution']['fields']['total_amount2']['clause'] = ' */


    /* SUM(contribution2_total_amount_count) as contribution2_total_amount_count, */


    /* SUM(contribution2_total_amount_sum)   as contribution2_total_amount_sum'; */


    /*             $this->_columns['civicrm_contribution']['fields']['total_amount1']['clause'] = ''; */


    /*             $this->_columns['civicrm_contribution']['fields']['total_amount2']['clause'] = ''; */


    /*         } */



    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (CRM_Utils_Array::value('required', $field) ||
            CRM_Utils_Array::value($fieldName, $this->_params['fields'])
          ) {
            if (isset($field['clause'])) {
              $select[] = $field['clause'];

              // FIXME: dirty hack for setting columnHeaders
              $this->_columnHeaders["{$field['alias']}_{$field['name']}_sum"]['type'] = CRM_Utils_Array::value('type', $field);
              $this->_columnHeaders["{$field['alias']}_{$field['name']}_sum"]['title'] = $field['title'];
              $this->_columnHeaders["{$field['alias']}_{$field['name']}_count"]['type'] = CRM_Utils_Array::value('type', $field);
              $this->_columnHeaders["{$field['alias']}_{$field['name']}_count"]['title'] = $field['title'];
              continue;
            }

            // only include statistics columns if set
            $select[] = "{$field['dbAlias']} as {$field['alias']}_{$field['name']}";
            $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['type'] = CRM_Utils_Array::value('type', $field);
            $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['title'] = CRM_Utils_Array::value('title', $field);
            if (CRM_Utils_Array::value('no_display', $field)) {
              $this->_columnHeaders["{$field['alias']}_{$field['name']}"]['no_display'] = TRUE;
            }
          }
        }
      }
    }

    $this->_select = "SELECT " . implode(', ', $select) . " ";
  }

  function groupBy($tableCol = FALSE) {
    $this->_groupBy = "";
    if (!empty($this->_params['group_bys']) && is_array($this->_params['group_bys'])) {
      foreach ($this->_columns as $tableName => $table) {
        if (array_key_exists('group_bys', $table)) {
          foreach ($table['group_bys'] as $fieldName => $field) {
            if (CRM_Utils_Array::value($fieldName, $this->_params['group_bys'])) {
              if ($tableCol) {
                return array($tableName, $field['alias'], $field['name']);
              }
              else {
                $this->_groupBy[] = "{$field['dbAlias']}";
              }
            }
          }
        }
      }

      $this->_groupBy = "GROUP BY " . implode(', ', $this->_groupBy);

      // Set default sort order
      /*             if(count($this->_params['group_bys']) == 1 && !empty($this->_params['group_bys']['id'])) { */


      /*               $this->_groupBy .= ' ORDER BY contact_civireport.sort_name'; */


      /*             } */
    }
  }

  function from() {
    list($fromTable, $fromAlias, $fromCol) = $this->groupBy(TRUE);
    $from = "$fromTable $fromAlias";

    if ($fromTable == 'civicrm_contact') {
      $contriCol = "contact_id";
      $from .= "
LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id
LEFT JOIN civicrm_email   {$this->_aliases['civicrm_email']}
       ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND {$this->_aliases['civicrm_email']}.is_primary = 1
LEFT JOIN civicrm_phone   {$this->_aliases['civicrm_phone']}
       ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND {$this->_aliases['civicrm_phone']}.is_primary = 1";
    }
    elseif ($fromTable == 'civicrm_contribution_type') {
      $contriCol = "contribution_type_id";
    }
    elseif ($fromTable == 'civicrm_contribution') {
      $contriCol = $fromCol;
    }
    elseif ($fromTable == 'civicrm_address') {
      $from .= "
INNER JOIN civicrm_contact {$this->_aliases['civicrm_contact']} ON {$this->_aliases['civicrm_address']}.contact_id = {$this->_aliases['civicrm_contact']}.id";
      $fromAlias = $this->_aliases['civicrm_contact'];
      $fromCol   = "id";
      $contriCol = "contact_id";
    }

    $this->_from = "
FROM $from
LEFT JOIN civicrm_temp_civireport_repeat1 {$this->_aliases['civicrm_contribution']}1
       ON $fromAlias.$fromCol = {$this->_aliases['civicrm_contribution']}1.$contriCol
LEFT JOIN civicrm_temp_civireport_repeat2 {$this->_aliases['civicrm_contribution']}2
       ON $fromAlias.$fromCol = {$this->_aliases['civicrm_contribution']}2.$contriCol";
  }

  function whereContribution($replaceAliasWith = 'contribution1') {
    $clauses = array("is_test" => "{$this->_aliases['civicrm_contribution']}.is_test = 0");

    foreach ($this->_columns['civicrm_contribution']['filters'] as $fieldName => $field) {
      $clause = NULL;
      if (CRM_Utils_Array::value('type', $field) & CRM_Utils_Type::T_DATE) {
        $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
        $from     = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
        $to       = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);

        $clause = $this->dateClause($field['dbAlias'], $relative, $from, $to, $field['type']);
      }
      else {
        $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
        if ($op) {
          $clause = $this->whereClause($field,
            $op,
            CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
            CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
            CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
          );
        }
      }
      if (!empty($clause)) {
        $clauses[$fieldName] = $clause;
      }
    }

    if (!$this->_amountClauseWithAND) {
      $amountClauseWithAND = array();
      if ($clauses['total_amount1']) {
        $amountClauseWithAND[] = str_replace("{$this->_aliases['civicrm_contribution']}.total_amount", 
                                             "{$this->_aliases['civicrm_contribution']}1.total_amount_sum", $clauses['total_amount1']);
      }
      if ($clauses['total_amount2']) {
        $amountClauseWithAND[] = str_replace("{$this->_aliases['civicrm_contribution']}.total_amount", 
                                             "{$this->_aliases['civicrm_contribution']}2.total_amount_sum", $clauses['total_amount2']);
      }
      $this->_amountClauseWithAND = !empty($amountClauseWithAND) ? implode(' AND ', $amountClauseWithAND) : NULL;
    }

    if ($replaceAliasWith == 'contribution1') {
      unset($clauses['receive_date2'], $clauses['total_amount2']);
    }
    else {
      unset($clauses['receive_date1'], $clauses['total_amount1']);
    }

    $whereClause = !empty($clauses) ? "WHERE " . implode(' AND ', $clauses) : '';

    if ($replaceAliasWith) {
      $whereClause = str_replace($this->_aliases['civicrm_contribution'], $replaceAliasWith, $whereClause);
    }
    return $whereClause;
  }

  function where() {
    if (!$this->_amountClauseWithAND) {
      $this->_amountClauseWithAND = 
        "!({$this->_aliases['civicrm_contribution']}1.total_amount_count IS NULL AND {$this->_aliases['civicrm_contribution']}2.total_amount_count IS NULL)";
    }
    $clauses = array("atleast_one_amount" => $this->_amountClauseWithAND);

    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('filters', $table) && $tableName != 'civicrm_contribution') {
        foreach ($table['filters'] as $fieldName => $field) {
          $clause = NULL;
          $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
          if ($op) {
            $clause = $this->whereClause($field,
              $op,
              CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
              CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
              CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
            );
          }
          if (!empty($clause)) {
            $clauses[$fieldName] = $clause;
          }
        }
      }
    }

    $this->_where = !empty($clauses) ? "WHERE " . implode(' AND ', $clauses) : '';
  }

  function formRule($fields, $files, $self) {

    $errors = $checkDate = $errorCount = array();

    $rules = array(
      'id' => array('sort_name', 'email', 'phone',
        'state_province_id', 'country_id',
      ),
      'country_id' => array('country_id'),
      'state_province_id' => array('country_id', 'state_province_id'),
      'contribution_source' => array('contribution_source'),
      'contribution_type' => array('contribution_type'),
    );
    $idMapping = array(
      'id' => 'Contact',
      'country_id' => 'Country',
      'state_province_id' => 'State/Province',
      'contribution_source' => 'Contribution Source',
      'contribution_type' => 'Contribution Type',
      'sort_name' => 'Contact Name',
      'email' => 'Email',
      'phone' => 'Phone',
    );

    if (empty($fields['group_bys'])) {
      $errors['fields'] = ts('Please select at least one Group by field.');
    }
    elseif ((array_key_exists('contribution_source', $fields['group_bys']) ||
        array_key_exists('contribution_type', $fields['group_bys'])
      ) &&
      (count($fields['group_bys']) > 1)
    ) {

      $errors['fields'] = ts('You can not use other Group by with Contribution type or Contribution source.');
    }
    else {
      foreach ($fields['fields'] as $fld_id => $value) {
        if (!($fld_id == 'total_amount1') && !($fld_id == 'total_amount2')) {
          $found = FALSE;
          $invlidGroups = array();
          foreach ($fields['group_bys'] as $grp_id => $val) {
            $validFields = $rules[$grp_id];
            if (in_array($fld_id, $validFields)) {
              $found = TRUE;
            }
            else {
              $invlidGroups[] = $idMapping[$grp_id];
            }
          }
          if (!$found) {
            $erorrGrps = implode(',', $invlidGroups);
            $tempErrors[] = ts("Do not select field %1 with Group by %2.", array(1 => $idMapping[$fld_id], 2 => $erorrGrps));
          }
        }
      }
      if (!empty($tempErrors)) {
        $errors['fields'] = implode("<br>", $tempErrors);
      }
    }

    if (!empty($fields['gid_value']) && CRM_Utils_Array::value('group_bys', $fields)) {
      if (!array_key_exists('id', $fields['group_bys'])) {
        $errors['gid_value'] = ts("Filter with Group only allow with group by Contact");
      }
    }

    if ($fields['receive_date1_relative'] == '0') {
      $checkDate['receive_date1']['receive_date1_from'] = $fields['receive_date1_from'];
      $checkDate['receive_date1']['receive_date1_to'] = $fields['receive_date1_to'];
    }

    if ($fields['receive_date2_relative'] == '0') {
      $checkDate['receive_date2']['receive_date2_from'] = $fields['receive_date2_from'];
      $checkDate['receive_date2']['receive_date2_to'] = $fields['receive_date2_to'];
    }

    foreach ($checkDate as $date_range => $range_data) {
      foreach ($range_data as $key => $value) {
        if (CRM_Utils_Date::isDate($value)) {
          $errorCount[$date_range][$key]['valid'] = 'true';
          $errorCount[$date_range][$key]['is_empty'] = 'false';
        }
        else {
          $errorCount[$date_range][$key]['valid'] = 'false';
          $errorCount[$date_range][$key]['is_empty'] = 'true';
          if (is_array($value)) {
            foreach ($value as $v) {
              if ($v) {
                $errorCount[$date_range][$key]['is_empty'] = 'false';
              }
            }
          }
          elseif (!isset($value)) {
            $errorCount[$date_range][$key]['is_empty'] = 'false';
          }
        }
      }
    }

    $errorText = ts("Select valid date range");
    foreach ($errorCount as $date_range => $error_data) {

      if (($error_data[$date_range . '_from']['valid'] == 'false') &&
        ($error_data[$date_range . '_to']['valid'] == 'false')
      ) {

        if (($error_data[$date_range . '_from']['is_empty'] == 'true') &&
          ($error_data[$date_range . '_to']['is_empty'] == 'true')
        ) {
          $errors[$date_range . '_relative'] = $errorText;
        }

        if ($error_data[$date_range . '_from']['is_empty'] == 'false') {
          $errors[$date_range . '_from'] = $errorText;
        }

        if ($error_data[$date_range . '_to']['is_empty'] == 'false') {
          $errors[$date_range . '_to'] = $errorText;
        }
      }
      elseif (($error_data[$date_range . '_from']['valid'] == 'true') &&
        ($error_data[$date_range . '_to']['valid'] == 'false')
      ) {
        if ($error_data[$date_range . '_to']['is_empty'] == 'false') {
          $errors[$date_range . '_to'] = $errorText;
        }
      }
      elseif (($error_data[$date_range . '_from']['valid'] == 'false') &&
        ($error_data[$date_range . '_to']['valid'] == 'true')
      ) {
        if ($error_data[$date_range . '_from']['is_empty'] == 'false') {
          $errors[$date_range . '_from'] = $errorText;
        }
      }
    }

    return $errors;
  }

  function statistics(&$rows) {
    $statistics = parent::statistics($rows);
    return $statistics;
  }

  function postProcess() {
    $this->beginPostProcess();

    list($fromTable, $fromAlias, $fromCol) = $this->groupBy(TRUE);
    if ($fromTable == 'civicrm_contact') {
      $contriCol = "contact_id";
    }
    elseif ($fromTable == 'civicrm_contribution_type') {
      $contriCol = "contribution_type_id";
    }
    elseif ($fromTable == 'civicrm_contribution') {
      $contriCol = $fromCol;
    }
    elseif ($fromTable == 'civicrm_address') {
      $contriCol = "contact_id";
    }

    $subWhere = $this->whereContribution();
    $subContributionQuery1 = "
SELECT contribution1.{$contriCol},
       sum( contribution1.total_amount ) AS total_amount_sum,
       count( * ) AS total_amount_count
FROM   civicrm_contribution contribution1
{$subWhere}
GROUP BY contribution1.{$contriCol}";

    $subWhere = $this->whereContribution('contribution2');
    $subContributionQuery2 = "
SELECT contribution2.{$contriCol},
       sum( contribution2.total_amount ) AS total_amount_sum,
       count( * ) AS total_amount_count
FROM   civicrm_contribution contribution2
{$subWhere}
GROUP BY contribution2.{$contriCol}";

    $sql = "
CREATE TEMPORARY TABLE civicrm_temp_civireport_repeat1 ( 
{$contriCol} int unsigned,
total_amount_sum int,
total_amount_count int          
) ENGINE=HEAP DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci";
    CRM_Core_DAO::executeQuery($sql);
    $sql = "INSERT INTO civicrm_temp_civireport_repeat1 {$subContributionQuery1}";
    CRM_Core_DAO::executeQuery($sql);

    $sql = "
CREATE TEMPORARY TABLE civicrm_temp_civireport_repeat2 ( 
{$contriCol} int unsigned,
total_amount_sum int,
total_amount_count int          
) ENGINE=HEAP DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci";
    CRM_Core_DAO::executeQuery($sql);
    $sql = "INSERT INTO civicrm_temp_civireport_repeat2 {$subContributionQuery2}";
    CRM_Core_DAO::executeQuery($sql);

    $this->select();
    $this->from();
    $this->where();
    $this->groupBy();
    $this->limit();

    $count = 0;
    $sql   = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_limit}";
    $dao   = CRM_Core_DAO::executeQuery($sql);
    $rows  = array();
    while ($dao->fetch()) {
      foreach ($this->_columnHeaders as $key => $value) {
        $rows[$count][$key] = $dao->$key;
      }
      $count++;
    }

    // FIXME: calculate % using query
    foreach ($rows as $uid => $row) {
      if ($row['contribution1_total_amount_sum'] && $row['contribution2_total_amount_sum']) {
        $rows[$uid]['change'] = number_format((($row['contribution2_total_amount_sum'] -
              $row['contribution1_total_amount_sum']
            ) * 100) /
          ($row['contribution1_total_amount_sum']), 2
        );
      }
      elseif ($row['contribution1_total_amount_sum']) {
        $rows[$uid]['change'] = ts('Skipped Donation');
      }
      elseif ($row['contribution2_total_amount_sum']) {
        $rows[$uid]['change'] = ts('New Donor');
      }
      if ($row['contribution1_total_amount_count']) {
        $rows[$uid]['contribution1_total_amount_sum'] = $row['contribution1_total_amount_sum'] . " ({$row['contribution1_total_amount_count']})";
      }
      if ($row['contribution2_total_amount_count']) {
        $rows[$uid]['contribution2_total_amount_sum'] = $row['contribution2_total_amount_sum'] . " ({$row['contribution2_total_amount_count']})";
      }
    }
    $this->_columnHeaders['change'] = array(
      'title' => '% Change',
      'type' => CRM_Utils_Type::T_INT,
    );

    // hack to fix title
    list($from1, $to1) = $this->getFromTo(CRM_Utils_Array::value("receive_date1_relative", $this->_params),
      CRM_Utils_Array::value("receive_date1_from", $this->_params),
      CRM_Utils_Array::value("receive_date1_to", $this->_params)
    );
    $from1 = CRM_Utils_Date::customFormat($from1, NULL, array('d'));
    $to1 = CRM_Utils_Date::customFormat($to1, NULL, array('d'));

    list($from2, $to2) = $this->getFromTo(CRM_Utils_Array::value("receive_date2_relative", $this->_params),
      CRM_Utils_Array::value("receive_date2_from", $this->_params),
      CRM_Utils_Array::value("receive_date2_to", $this->_params)
    );
    $from2 = CRM_Utils_Date::customFormat($from2, NULL, array('d'));
    $to2 = CRM_Utils_Date::customFormat($to2, NULL, array('d'));

    $this->_columnHeaders['contribution1_total_amount_sum']['title'] = "$from1 -<br/> $to1";
    $this->_columnHeaders['contribution2_total_amount_sum']['title'] = "$from2 -<br/> $to2";
    unset($this->_columnHeaders['contribution1_total_amount_count'],
      $this->_columnHeaders['contribution2_total_amount_count']
    );

    $this->formatDisplay($rows);

    // assign variables to templates
    $this->doTemplateAssignment($rows);

    $this->endPostProcess($rows);
  }

  function alterDisplay(&$rows) {
    // custom code to alter rows
    list($from1, $to1) = $this->getFromTo(CRM_Utils_Array::value("receive_date1_relative", $this->_params),
      CRM_Utils_Array::value("receive_date1_from", $this->_params),
      CRM_Utils_Array::value("receive_date1_to", $this->_params)
    );
    list($from2, $to2) = $this->getFromTo(CRM_Utils_Array::value("receive_date2_relative", $this->_params),
      CRM_Utils_Array::value("receive_date2_from", $this->_params),
      CRM_Utils_Array::value("receive_date2_to", $this->_params)
    );

    $dateUrl = "";
    if ($from1) {
      $dateUrl .= "receive_date1_from={$from1}&";
    }
    if ($to1) {
      $dateUrl .= "receive_date1_to={$to1}&";
    }
    if ($from2) {
      $dateUrl .= "receive_date2_from={$from2}&";
    }
    if ($to2) {
      $dateUrl .= "receive_date2_to={$to2}&";
    }

    foreach ($rows as $rowNum => $row) {
      // handle country
      if (array_key_exists('address_civireport_country_id', $row)) {
        if ($value = $row['address_civireport_country_id']) {
          $rows[$rowNum]['address_civireport_country_id'] = CRM_Core_PseudoConstant::country($value, FALSE);

          $url = CRM_Report_Utils_Report::getNextUrl('contribute/detail',
            "reset=1&force=1&" .
            "country_id_op=in&country_id_value={$value}&" .
            "$dateUrl",
            $this->_absoluteUrl, $this->_id
          );


          $rows[$rowNum]['address_civireport_country_id_link'] = $url;
          $rows[$rowNum]['address_civireport_country_id_hover'] = ts("View contributions for this Country.");
        }
        $entryFound = TRUE;
      }

      // handle state province
      if (array_key_exists('address_civireport_state_province_id', $row)) {
        if ($value = $row['address_civireport_state_province_id']) {
          $rows[$rowNum]['address_civireport_state_province_id'] = CRM_Core_PseudoConstant::stateProvince($value, FALSE);

          $url = CRM_Report_Utils_Report::getNextUrl('contribute/detail',
            "reset=1&force=1&" .
            "state_province_id_op=in&state_province_id_value={$value}&" .
            "$dateUrl",
            $this->_absoluteUrl, $this->_id
          );
          $rows[$rowNum]['address_civireport_state_province_id_link'] = $url;
          $rows[$rowNum]['address_civireport_state_province_id_hover'] = ts("View repeatDetails for this state.");
        }
        $entryFound = TRUE;
      }

      // convert display name to links
      if (array_key_exists('contact_civireport_sort_name', $row) &&
        array_key_exists('contact_civireport_id', $row)
      ) {
        $url = CRM_Report_Utils_Report::getNextUrl('contribute/detail',
          'reset=1&force=1&id_op=eq&id_value=' . $row['contact_civireport_id'],
          $this->_absoluteUrl, $this->_id
        );
        $rows[$rowNum]['contact_civireport_sort_name_link'] = $url;
        $rows[$rowNum]['contact_civireport_sort_name_hover'] = ts("View Contribution details for this contact");
        $entryFound = TRUE;
      }
    }
    // foreach ends
  }
}

