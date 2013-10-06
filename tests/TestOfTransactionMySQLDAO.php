<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfTransactionMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new TransactionMySQLDAO();
        $this->assertIsA($dao, 'TransactionMySQLDAO');
    }

    public function testInsert() {
        $dao = new TransactionMySQLDAO();
        $result = $dao->insert($token_id='aabbccdd', $amount=60, $status_code='SE', $error_message=null,
        $payment_method_expiry='2015-01-15');
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM transactions WHERE id = ".$result;
        $stmt = TransactionMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual('aabbccdd', $data['token_id']);
        $this->assertEqual('60', $data['amount']);
        $this->assertEqual('2015-01-15', $data['payment_method_expiry']);
        $this->assertEqual('SE', $data['status_code']);
        $this->assertNull($data['error_message']);
    }

    public function testInsertDuplicate() {
        $dao = new TransactionMySQLDAO();
        $result = $dao->insert($token_id='aabbccdd', $amount=60, $status_code='SE', $error_message=null,
        $payment_method_expiry='2015-01-15');

        //test inserting same token twice
        $this->expectException('DuplicateTransactionException');
        $result = $dao->insert($token_id='aabbccdd', $amount=60, $status_code='SE', $error_message=null,
        $payment_method_expiry='2015-01-15');
    }

    public function testInsertInvalidStatusCode() {
        $dao = new TransactionMySQLDAO();
        $this->expectException('InvalidTransactionStatusCodeException');
        $result = $dao->insert($token_id='aabbccdd', $amount=60, $status_code='GT', $error_message=null,
        $payment_method_expiry='2015-01-15');
    }

}