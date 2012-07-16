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

require_once 'CiviTest/CiviUnitTestCase.php';

/**
 * Test class for Domain API - civicrm_domain_*
 *
 *  @package   CiviCRM
 */
class api_v3_SystemTest extends CiviUnitTestCase {

  const TEST_CACHE_GROUP = 'SystemTest';
  const TEST_CACHE_PATH = 'api/v3/system';
  public $_eNoticeCompliant = TRUE;
  /**
   *  Constructor
   *
   *  Initialize configuration
   */ function __construct() {
    parent::__construct();
  }

  /**
   * Sets up the fixture, for example, opens a network connection.
   * This method is called before a test is executed.
   *
   * @access protected
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * Tears down the fixture, for example, closes a network connection.
   * This method is called after a test is executed.
   *
   * @access protected
   */
  protected function tearDown() {}

  ///////////////// civicrm_domain_get methods

  /**
   * Test system flush
   */
  public function testFlush() {
    // Note: this operation actually flushes several different caches; we don't
    // check all of them -- just enough to make sure that the API is doing
    // something

    $this->assertTrue(NULL === CRM_Core_BAO_Cache::getItem(self::TEST_CACHE_GROUP, self::TEST_CACHE_PATH));

    $data = 'abc';
    CRM_Core_BAO_Cache::setItem($data, self::TEST_CACHE_GROUP, self::TEST_CACHE_PATH);

    $this->assertEquals('abc', CRM_Core_BAO_Cache::getItem(self::TEST_CACHE_GROUP, self::TEST_CACHE_PATH));

    $params = array(
      'version' => 3,
    );
    $result = civicrm_api('system', 'flush', $params);
    $this->assertAPISuccess($result);
    $this->documentMe($params, $result, __FUNCTION__, __FILE__, "Flush all system caches", 'Flush', 'flush');

    $this->assertTrue(NULL === CRM_Core_BAO_Cache::getItem(self::TEST_CACHE_GROUP, self::TEST_CACHE_PATH));
  }
}
