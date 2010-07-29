<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
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
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Dedupe/DAO/Rule.php';

/**
 * The CiviCRM duplicate discovery engine is based on an
 * algorithm designed by David Strauss <david@fourkitchens.com>.
 */
class CRM_Dedupe_BAO_Rule extends CRM_Dedupe_DAO_Rule
{

    /**
     * ids of the contacts to limit the SQL queries (whole-database queries otherwise)
     */
    var $contactIds = array();

    /**
     * params to dedupe against (queries against the whole contact set otherwise)
     */
    var $params = array();

    /**
     * Return the SQL query for the given rule - either for finding matching 
     * pairs of contacts, or for matching against the $params variable (if set).
     *
     * @return string  SQL query performing the search
     */
    function sql() {

        // we need to initialise WHERE, ON and USING here, as some table types 
        // extend them; $where is an array of required conditions, $on and 
        // $using are arrays of required field matchings (for substring and 
        // full matches, respectively)
        $where = array();
        $on = array("SUBSTR(t1.{$this->rule_field}, 1, {$this->rule_length}) = SUBSTR(t2.{$this->rule_field}, 1, {$this->rule_length})");
        $using = array($this->rule_field);

        switch ($this->rule_table) {
        case 'civicrm_contact':
            $id = 'id';
            break;
        case 'civicrm_address':
            $id = 'contact_id';
            $on[] = 't1.location_type_id = t2.location_type_id';
            $using[] = 'location_type_id';
            if ($this->params['civicrm_address']['location_type_id']) {
                $locTypeId = CRM_Utils_Type::escape($this->params['civicrm_address']['location_type_id'], 'Integer', false);
                if ($locTypeId) {
                    $where[] = "location_type_id = $locTypeId";
                }
            }
            break;
        case 'civicrm_email':
        case 'civicrm_im':
        case 'civicrm_openid':
        case 'civicrm_phone':
            $id = 'contact_id';
            break;
        case 'civicrm_note':
            $id = 'entity_id';
            if ($this->params) {
                $where[] = "entity_table = 'civicrm_contact'";
            } else {
                $where[] = "t1.entity_table = 'civicrm_contact'";
                $where[] = "t2.entity_table = 'civicrm_contact'";
            }
            break;
        default:
            // custom data tables
            if (preg_match('/^civicrm_value_/', $this->rule_table)) {
                $id = 'entity_id';
            } else {
                CRM_Core_Error::fatal("Unsupported rule_table for civicrm_dedupe_rule.id of {$this->id}");
            }
            break;
        }

        // build SELECT based on the field names containing contact ids
        // if there are params provided, id1 should be 0
        if ($this->params) {
            $select = "$id id, {$this->rule_weight} weight";
        } else {
            $select = "t1.$id id1, t2.$id id2, {$this->rule_weight} weight";
        }

        // build FROM (and WHERE, if it's a parametrised search)
        // based on whether the rule is about substrings or not
        if ($this->params) {
            $from = $this->rule_table;
            $str = 'NULL';
            if ( isset( $this->params[$this->rule_table][$this->rule_field] ) ) {
                $str = CRM_Utils_Type::escape($this->params[$this->rule_table][$this->rule_field], 'String');
            }
            if ($this->rule_length) {
                $where[] = "SUBSTR({$this->rule_field}, 1, {$this->rule_length}) = SUBSTR('$str', 1, {$this->rule_length})";
                $where[] = "{$this->rule_field} IS NOT NULL";
            } else {
                $where[] = "{$this->rule_field} = '$str'";
            }
        } else {
            if ($this->rule_length) {
                $from = "{$this->rule_table} t1 JOIN {$this->rule_table} t2 ON (" . implode(' AND ', $on) . ")";
            } else {
                $from = "{$this->rule_table} t1 JOIN {$this->rule_table} t2 USING (" . implode(', ', $using) . ")";
            }
        }

        // finish building WHERE, also limit the results if requested
        if (!$this->params) {
            $where[] = "t1.$id < t2.$id";
            $where[] = "t1.{$this->rule_field} IS NOT NULL";
        }
        if ($this->contactIds) {
            $cids = array();
            foreach ($this->contactIds as $cid) {
                $cids[] = CRM_Utils_Type::escape($cid, 'Integer');
            }
            if (count($cids) == 1) {
                $where[] = "(t1.$id = {$cids[0]} OR t2.$id = {$cids[0]})";
            } else {
                $where[] = "(t1.$id IN (" . implode(',', $cids) . ") OR t2.$id IN (" . implode(',', $cids) . "))";
            }
        }

        return "SELECT $select FROM $from WHERE " . implode(' AND ', $where);
    }

    /**
     * To find fields related to a rule group.
     * @param array contains the rule group property to identify rule group
     *
     * @return rule fields array associated to rule group
     * @access public
     */
    function dedupeRuleFields( $params)
    {
        require_once 'CRM/Dedupe/BAO/RuleGroup.php';
        $rgBao = new CRM_Dedupe_BAO_RuleGroup();
        $rgBao->level = $params['level'];
        $rgBao->contact_type = $params['contact_type'];
        $rgBao->is_default = 1;
        $rgBao->find(true);
        
        $ruleBao = new CRM_Dedupe_BAO_Rule();
        $ruleBao->dedupe_rule_group_id = $rgBao->id;
        $ruleBao->find();
        $ruleFields = array();
        while ($ruleBao->fetch()) {
            $ruleFields[] = $ruleBao->rule_field;
        }
        return $ruleFields;
    }
}
