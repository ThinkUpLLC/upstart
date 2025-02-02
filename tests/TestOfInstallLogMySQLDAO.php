<?php
require_once dirname(__FILE__) . '/init.tests.php';

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

    public function testGetBySubscriber() {
        $dao = new InstallLogMySQLDAO();
        $this->assertIsA($dao, 'InstallLogMySQLDAO');
        $results = $dao->getLogEntriesBySubscriber(1);
        $this->assertEqual(sizeof($results), 0);
    }
}