<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/



require_once 'ReleaseTestCase.php';

// name of the class doesn't end with Test on purpose - this way this
// webtest is not picked up by the suite, since it needs to run
// on specially prepare sandbox
// more details: http://wiki.civicrm.org/confluence/display/CRMDOC40/Release+testing+script+documentation
class WebTest_Release_InstallScript extends WebTest_Release_ReleaseTestCase {

  protected function setUp() {
    parent::setUp();
  }

  function testInstall() {
    $this->open($this->sboxPath);
    $this->webtestLogin();
    $this->open($this->settings->installURL);

    $this->waitForTextPresent("Thanks for choosing to use CiviCRM! Please follow the instructions below to get CiviCRM installed.");
    $this->type("mysql_server", $this->settings->civiDBServer);
    $this->type("mysql_username", $this->settings->civiDBUser);
    $this->type("mysql_password", $this->settings->civiDBPass);
    $this->type("mysql_database", $this->settings->civiDBName);

    $this->type("drupal_server", $this->settings->drupalDBServer);
    $this->type("drupal_username", $this->settings->drupalDBUser);
    $this->type("drupal_password", $this->settings->drupalDBPass);
    $this->type("drupal_database", $this->settings->drupalDBName);

    $this->click("xpath=//input[@value='Re-check requirements']");
    $this->waitForPageToLoad(30000);
    $this->click("install_button");
    $this->waitForPageToLoad(30000);
    //      $this->assertTrue($this->isTextPresent("this will take a few minutes"));
    $this->waitForTextPresent("CiviCRM has been successfully installed");
    $this->open($this->sboxPath . "civicrm/dashboard?reset=1");
    $this->assertTrue($this->isTextPresent("CiviCRM Home"));
  }
}

