<?php
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
 * A PHP script which deletes extraneous civicrm_membership_payment rows
 * in order to correct the condition where a contribution row is linked to > 1 membership.
  */

function initialize() {
  session_start();
  if (!function_exists('drush_get_context')) {
    require_once '../civicrm.config.php';
  }
  
  require_once 'CRM/Core/Config.php';
  $config = CRM_Core_Config::singleton();
  
  // this does not return on failure
  CRM_Utils_System::authenticateScript(TRUE);
  
}

function run() {
  initialize();
  echo "The following records have been processed. If action = Un-linked, that membership has been disconnected from the contribution record.\n";
  echo "Contact ID, ContributionID, Contribution Status, MembershipID, Membership Type, Start Date, End Date, Membership Status \n";
  $fh = fopen('php://stdout', 'w');
  fputcsv($fh, array("Contact ID", "ContributionID, Contribution Status, MembershipID, Membership Type, Start Date, End Date, Membership Status");
  CRM_Upgrade_Page_Cleanup42deleteInvalidPairs(function($row) use ($fh) {
    fputcsv($fh, $row);
  });
}
