<?php

require_once 'CiviTest/CiviUnitTestCase.php';

class CRM_Extension_BrowserTest extends CiviUnitTestCase {
  function setUp() {
    parent::setUp();
  }

  function tearDown() {
    parent::tearDown();
  }

  function testDisabled() {
    $browser = new CRM_Extension_Browser(FALSE, '/index.html', '/itd/oesn/tmat/ter');
    $this->assertEquals(FALSE, $browser->isEnabled());
    $this->assertEquals(array(), $browser->checkRequirements());
    $this->assertEquals(array(), $browser->getExtensions());
  }

  function testCheckRequirements_BadCachedir_false() {
    $browser = new CRM_Extension_Browser(dirname(__FILE__) .'/dataset/good-repository', '/index.html', FALSE);
    $this->assertEquals(TRUE, $browser->isEnabled());
    $reqs = $browser->checkRequirements();
    $this->assertEquals(1, count($reqs));
  }

  function testCheckRequirements_BadCachedir_nonexistent() {
    $browser = new CRM_Extension_Browser(dirname(__FILE__) .'/dataset/good-repository', '/index.html', '/tot/all/yin/v/alid');
    $this->assertEquals(TRUE, $browser->isEnabled());
    $reqs = $browser->checkRequirements();
    $this->assertEquals(1, count($reqs));
  }

  function testGetExtensions_good() {
    $browser = new CRM_Extension_Browser(dirname(__FILE__) .'/dataset/good-repository', '/index.html', $this->createTempDir('ext-cache-'));
    $this->assertEquals(TRUE, $browser->isEnabled());
    $this->assertEquals(array(), $browser->checkRequirements());
    $exts = $browser->getExtensions();
    $keys = array_keys($exts);
    sort($keys);
    $this->assertEquals(array('test.crm.extension.browsertest.a', 'test.crm.extension.browsertest.b'), $keys);
    $this->assertEquals('report', $exts['test.crm.extension.browsertest.a']->type);
    $this->assertEquals('module', $exts['test.crm.extension.browsertest.b']->type);
    $this->assertEquals('http://example.com/test.crm.extension.browsertest.a-0.1.zip', $exts['test.crm.extension.browsertest.a']->downloadUrl);
    $this->assertEquals('http://example.com/test.crm.extension.browsertest.b-1.2.zip', $exts['test.crm.extension.browsertest.b']->downloadUrl);
  }

  function testGetExtension_good() {
    $browser = new CRM_Extension_Browser(dirname(__FILE__) .'/dataset/good-repository', '/index.html', $this->createTempDir('ext-cache-'));
    $this->assertEquals(TRUE, $browser->isEnabled());
    $this->assertEquals(array(), $browser->checkRequirements());

    $info = $browser->getExtension('test.crm.extension.browsertest.b');
    $this->assertEquals('module', $info->type);
    $this->assertEquals('http://example.com/test.crm.extension.browsertest.b-1.2.zip', $info->downloadUrl);
  }

  function testGetExtension_nonexistent() {
    $browser = new CRM_Extension_Browser(dirname(__FILE__) .'/dataset/good-repository', '/index.html', $this->createTempDir('ext-cache-'));
    $this->assertEquals(TRUE, $browser->isEnabled());
    $this->assertEquals(array(), $browser->checkRequirements());

    $info = $browser->getExtension('test.crm.extension.browsertest.nonexistent');
    $this->assertEquals(NULL, $info);
  }
}
