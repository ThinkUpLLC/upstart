<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfSubscriberAuthorizationMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new SubscriberAuthorizationMySQLDAO();
        $this->assertIsA($dao, 'SubscriberAuthorizationMySQLDAO');
    }

    public function testInsert() {
        $dao = new SubscriberAuthorizationMySQLDAO();
        $result = $dao->insert(10, 20);
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscriber_authorizations WHERE id = ".$result;
        $stmt = SubscriberAuthorizationMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual(20, $data['authorization_id']);
        $this->assertEqual(10, $data['subscriber_id']);
    }

    public function testInsertDuplicate() {
        $dao = new SubscriberAuthorizationMySQLDAO();
        $result = $dao->insert(10, 20);

        //test inserting same token twice
        $this->expectException('DuplicateSubscriberAuthorizationException');
        $result = $dao->insert(10, 20);
    }
}