<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfThinkUpTablesMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new ThinkUpTablesMySQLDAO();
        $this->assertIsA($dao, 'ThinkUpTablesMySQLDAO');
    }
}