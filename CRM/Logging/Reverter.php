<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
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

require_once 'CRM/Logging/Differ.php';

class CRM_Logging_Reverter
{
    private $db;
    private $log_conn_id;
    private $log_date;

    function __construct($log_conn_id, $log_date)
    {
        $dsn = defined('CIVICRM_LOGGING_DSN') ? DB::parseDSN(CIVICRM_LOGGING_DSN) : DB::parseDSN(CIVICRM_DSN);
        $this->db          = $dsn['database'];
        $this->log_conn_id = $log_conn_id;
        $this->log_date    = $log_date;
    }

    function revert($tables)
    {
        // FIXME: split off the table → DAO mapping to a GenCode-generated class
        $daos = array(
            'log_civicrm_address' => 'CRM_Core_DAO_Address',
            'log_civicrm_contact' => 'CRM_Contact_DAO_Contact',
            'log_civicrm_email'   => 'CRM_Core_DAO_Email',
            'log_civicrm_im'      => 'CRM_Core_DAO_IM',
            'log_civicrm_openid'  => 'CRM_Core_DAO_OpenID',
            'log_civicrm_phone'   => 'CRM_Core_DAO_Phone',
            'log_civicrm_website' => 'CRM_Core_DAO_Website',
        );
        $differ = new CRM_Logging_Differ($this->log_conn_id, $this->log_date);
        $diffs  = $differ->diffsInTables($tables);

        $deletes = array();
        $reverts = array();
        foreach ($diffs as $table => $changes) {
            foreach ($changes as $change) {
                switch ($change['action']) {
                case 'Delete':
                    // FIXME: handle Delete actions
                    break;
                case 'Insert':
                    if (!isset($deletes[$table])) $deletes[$table] = array();
                    $deletes[$table][] = $change['id'];
                    break;
                case 'Update':
                    if (!isset($reverts[$table]))                $reverts[$table] = array();
                    if (!isset($reverts[$table][$change['id']])) $reverts[$table][$change['id']] = array();
                    $reverts[$table][$change['id']][$change['field']] = $change['from'];
                    break;
                }
            }
        }

        // revert inserts by deleting
        foreach ($deletes as $table => $ids) {
            CRM_Core_DAO::executeQuery('DELETE FROM `' . substr($table, 4) . '` WHERE id IN (' . implode(', ', array_unique($ids)) . ')');
        }

        // revert updates by updating to ‘from’ values
        // FIXME: handle custom data tables
        foreach ($reverts as $table => $row) {
            if (in_array($table, array_keys($daos))) {
                require_once str_replace('_', DIRECTORY_SEPARATOR, $daos[$table]) . '.php';
                eval("\$dao = new {$daos[$table]};");
                foreach ($row as $id => $changes) {
                    $dao->id = $id;
                    foreach ($changes as $field => $value) {
                        $dao->$field = $value;
                    }
                    $dao->save();
                    $dao->reset();
                }
            }
        }
    }
}
