<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfAuthorizationMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new AuthorizationMySQLDAO();
        $this->assertIsA($dao, 'AuthorizationMySQLDAO');
    }

    public function testInsert() {
        $dao = new AuthorizationMySQLDAO();
        $result = $dao->insert($token_id='aabbccdd', $amount=60, $status_code='SE', $caller_reference='xxyyz',
        $error_message=null, $payment_method_expiry='2015-01-15');
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM authorizations WHERE id = ".$result;
        $stmt = AuthorizationMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual('aabbccdd', $data['token_id']);
        $this->assertEqual('60', $data['amount']);
        $this->assertEqual('2015-01-15', $data['payment_method_expiry']);
        $this->assertEqual('SE', $data['status_code']);
        $this->assertEqual('xxyyz', $data['caller_reference']);
        $this->assertNull($data['error_message']);
    }

    public function testInsertDuplicate() {
        $dao = new AuthorizationMySQLDAO();
        $result = $dao->insert($token_id='aabbccdd', $amount=60, $status_code='SE', $caller_reference='xxyyz',
        $error_message=null, $payment_method_expiry='2015-01-15');

        //test inserting same token twice
        $this->expectException('DuplicateAuthorizationException');
        $result = $dao->insert($token_id='aabbccdd', $amount=60, $status_code='SE', $caller_reference='xxyyz',
        $error_message=null, $payment_method_expiry='2015-01-15');
    }

    public function testInsertInvalidStatusCode() {
        $dao = new AuthorizationMySQLDAO();
        $this->expectException('InvalidAuthorizationStatusCodeException');
        $result = $dao->insert($token_id='aabbccdd', $amount=60, $status_code='GT', $caller_reference='xxyyz',
        $error_message=null, $payment_method_expiry='2015-01-15');
    }

    public function testGetByTokenID() {
        $dao = new AuthorizationMySQLDAO();
        $result = $dao->insert($token_id='aabbccdd', $amount=60, $status_code='SE', $caller_reference='xxyyz',
        $error_message=null, $payment_method_expiry='2015-01-15');

        $authorization = $dao->getByTokenID('aabbccdd');
        $this->assertIsA($authorization, 'Authorization');
        $this->assertEqual($authorization->token_id, 'aabbccdd');

        $authorization = $dao->getByTokenID('aabbccdddd');
        $this->assertNull($authorization);
    }

    public function testDeleteBySubscriberID() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>1,
        'authorization_id'=>11));
        $builders[] = FixtureBuilder::build('authorizations', array('id'=>11, 'token_id'=>'aabbddccedd'));
        $builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>2,
        'authorization_id'=>10));
        $builders[] = FixtureBuilder::build('authorizations', array('id'=>10, 'token_id'=>'aabbddccedds'));

        $dao = new AuthorizationMySQLDAO();
        $result = $dao->deleteBySubscriberID(1);
        $this->assertEqual($result, 1);


        $sql = "SELECT * FROM authorizations WHERE id=11";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($data);

        $sql = "SELECT * FROM authorizations WHERE id=10";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual(sizeof($data), 10);
    }
}