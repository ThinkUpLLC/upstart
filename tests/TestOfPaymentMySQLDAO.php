<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfPaymentMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new PaymentMySQLDAO();
        $this->assertIsA($dao, 'PaymentMySQLDAO');
    }

    public function testInsert() {
        $dao = new PaymentMySQLDAO();
        $result = $dao->insert($transaction_id='abcd',$request_id='1234',$transaction_status='OK',$amt=100, $ref='z');
        $this->assertNotEqual(0, $result);

        $sql = "SELECT * FROM payments WHERE id = ".$result;
        $stmt = PaymentMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($data['transaction_id'], $transaction_id);
        $this->assertEqual($data['request_id'], $request_id);
        $this->assertEqual($data['transaction_status'], $transaction_status);
        $this->assertEqual($data['caller_reference'], $ref);
        $this->assertEqual($data['amount'], $amt);
        $this->assertEqual($data['id'], $result);
        $this->assertTrue(strtotime($data['timestamp']) > (time()-60));
        $this->assertNull($data['error_message']);
    }

    public function testInsertWithError() {
        $dao = new PaymentMySQLDAO();
        $result = $dao->insert($transaction_id='abc',$request_id='12',$transaction_status='',$amt=0,$ref='z',$err='TS');
        $this->assertNotEqual(0, $result);

        $sql = "SELECT * FROM payments WHERE id = ".$result;
        $stmt = PaymentMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($data['transaction_id'], $transaction_id);
        $this->assertEqual($data['request_id'], $request_id);
        $this->assertEqual($data['transaction_status'], $transaction_status);
        $this->assertEqual($data['amount'], $amt);
        $this->assertEqual($data['caller_reference'], $ref);
        $this->assertEqual($data['id'], $result);
        $this->assertTrue(strtotime($data['timestamp']) > (time()-60));
        $this->assertEqual($data['error_message'], $err);
    }
}
