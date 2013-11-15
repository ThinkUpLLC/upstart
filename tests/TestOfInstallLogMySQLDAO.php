<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfInstallLogMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new InstallLogMySQLDAO();
        $this->assertIsA($dao, 'InstallLogMySQLDAO');
    }
}