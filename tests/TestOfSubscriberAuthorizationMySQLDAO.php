<?php
require_once dirname(__FILE__) . '/init.tests.php';

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

    public function testDeleteBySubscriberID() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>1,
        'authorization_id'=>10));
        $dao = new SubscriberAuthorizationMySQLDAO();
        $result = $dao->deleteBySubscriberID(1);
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscriber_authorizations WHERE subscriber_id=1";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($data);
    }

    public function testGetBySubscriberID() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>1,
        'authorization_id'=>10));
        $builders[] = FixtureBuilder::build('authorizations', array('id'=>10, 'token_id'=>'aabbccdd'));

        $dao = new SubscriberAuthorizationMySQLDAO();
        $result = $dao->getBySubscriberID(1);
        $this->assertEqual(sizeof($result), 1);
        $this->assertEqual($result[0]->token_id, 'aabbccdd');
    }
}