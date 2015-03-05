<?php
require_once dirname(__FILE__) . '/init.tests.php';

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
        $this->assertEqual(strlen($result), 14);
        $id = substr($result, 0, 1);
        $suffix = substr($result, 1);
        $this->assertEqual($id, 1);

        $sql = "SELECT * FROM clicks WHERE id = 1";
        $stmt = ClickMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual(1, $data['id']);
        $this->assertEqual($suffix, $data['caller_reference_suffix']);
    }
}