<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
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

// escape early if called directly
defined('_JEXEC') or die('No direct access allowed'); 

function com_uninstall()
{
    $uninstall = false;
    // makes it easier if folks want to really uninstall
    if ( $uninstall ) {
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'civicrm.settings.php';
    
        require_once 'CRM/Core/Config.php';
        $config =& CRM_Core_Config::singleton( );
    
        require_once 'CRM/Core/DAO.php';
        CRM_Core_DAO::dropAllTables( );
    
        echo "You have uninstalled CiviCRM. All CiviCRM related tables have been dropped from the database.";
    } else {
        echo "You have uninstalled CiviCRM.";
    }
}


