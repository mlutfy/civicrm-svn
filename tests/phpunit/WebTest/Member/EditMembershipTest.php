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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/


require_once 'CiviTest/CiviSeleniumTestCase.php';
class WebTest_Member_EditMembershipTest extends CiviSeleniumTestCase {
  protected function setUp() {
    parent::setUp();
  }

  function testEditMembershipActivityTypes() {
    $this->open($this->sboxPath);
    // Log in using webtestLogin() method
    $this->webtestLogin();
    // create contact
    $firstName = substr(sha1(rand()), 0, 7);
    $this->webtestAddContact($firstName, "Memberson", "Memberson{$firstName}@memberson.name");
    $contactName = "Memberson, {$firstName}";
    $displayName = "{$firstName} Memberson";

    // add membership type
    $membershipTypes = $this->webtestAddMembershipType();

    // now add membership
    $this->open($this->sboxPath . "civicrm/member/add?reset=1&action=add&context=standalone");

    $this->waitForElementPresent("_qf_Membership_upload");

    // select contact
    $this->webtestFillAutocomplete($contactName);

    // fill in Membership Organization
    $this->select("membership_type_id[0]", "label={$membershipTypes['member_org']}");

    // select membership type
    $this->select("membership_type_id[1]", "label={$membershipTypes['membership_type']}");

    // fill in Source
    $this->type("source", "Membership StandaloneAddTest Webtest");

    // Let Join Date and Start Date stay default
    $this->click("_qf_Membership_upload");

    //View Membership
    $this->waitForElementPresent("xpath=//div[@id='memberships']//table//tbody/tr[1]/td[8]/span/a[text()='View']");
    $this->click("xpath=//div[@id='memberships']//table/tbody/tr[1]/td[8]/span/a[text()='View']");
    $this->waitForElementPresent("_qf_MembershipView_cancel-bottom");
    $expected = array(
      2 => $membershipTypes['membership_type'],
      3 => 'New',
      4 => 'Membership StandaloneAddTest Webtest',
    );
    foreach ($expected as $label => $value) {
      $this->verifyText("xpath=id('MembershipView')/div[2]/div/table[1]/tbody/tr[$label]/td[2]", preg_quote($value));
    }

    // now edit and update type and status
    $this->click("crm-membership-edit-button-top");
    $this->waitForElementPresent("_qf_Membership_upload-bottom");
    $this->click('is_override');
    $this->waitForElementPresent('status_id');
    $this->select('status_id', 'label=Current');
    $this->select('membership_type_id[0]', 'label=Inner City Arts');
    $this->select('membership_type_id[1]', 'label=General');
    $this->click('_qf_Membership_upload-bottom');

    $this->waitForElementPresent("access");

    // Use activity search to find the expected activities
    $this->open($this->sboxPath . "civicrm/activity/search?reset=1");
    $this->waitForElementPresent("_qf_Search_refresh");
    $this->type("sort_name", $contactName);
    $this->click('activity_type_id[35]');
    $this->click('activity_type_id[36]');
    $this->click("_qf_Search_refresh");
    $this->waitForPageToLoad('30000');

    $this->assertTrue($this->isElementPresent("xpath=//div[@class='crm-search-results']/table/tbody/tr[2]/td[2][text()='Change Membership Status']"));
    $this->assertTrue($this->isElementPresent("xpath=//div[@class='crm-search-results']/table/tbody/tr[2]/td[3][text()='Status changed from New to Current']"));
    $this->assertTrue($this->isElementPresent("xpath=//div[@class='crm-search-results']/table/tbody/tr[2]/td[5]/a[text()='{$contactName}']"));
    $this->assertTrue($this->isElementPresent("xpath=//div[@class='crm-search-results']/table/tbody/tr[3]/td[2][text()='Change Membership Type']"));
    $this->assertTrue($this->isElementPresent("xpath=//div[@class='crm-search-results']/table/tbody/tr[3]/td[3][text()='Type changed from {$membershipTypes['membership_type']} to General']"));
    $this->assertTrue($this->isElementPresent("xpath=//div[@class='crm-search-results']/table/tbody/tr[3]/td[5]/a[text()='{$contactName}']"));
  }
}

