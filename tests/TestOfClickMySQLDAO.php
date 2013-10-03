<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfClickMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new ClickMySQLDAO();
        $this->assertIsA($dao, 'ClickMySQLDAO');
    }

    public function testInsert() {
        $dao = new ClickMySQLDAO();
        $result = $dao->insert();
        $this->assertEqual($result, 1);
    }
}