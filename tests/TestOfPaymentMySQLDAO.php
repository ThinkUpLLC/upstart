<?php
require_once dirname(__FILE__) . '/init.tests.php';

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
        $this->assertNull($data['status_message']);
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
        $this->assertEqual($data['status_message'], $err);
    }

    public function testGetPayment() {
        $valid_transaction_id = '213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN8';
        $valid_caller_reference = '252cf11bbd71e2';
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('id'=>1,
            'transaction_id'=>$valid_transaction_id, 'caller_reference'=>$valid_caller_reference));
        $builders[] = FixtureBuilder::build('payments', array('id'=>2,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN6', 'caller_reference'=>'252cf11bbd71e1'));
        $dao = new PaymentMySQLDAO();
        $result = $dao->getPayment($valid_transaction_id, $valid_caller_reference);
        $this->assertEqual($result->id, 1);

        $this->expectException('PaymentDoesNotExistException');
        $dao->getPayment('asdfsfasdf', 'adfasdfasdf');
    }

    public function testGetSubscribersEarliestPayment() {
        $valid_transaction_id = '213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN8';
        $valid_caller_reference = '252cf11bbd71e2';
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('id'=>1,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN8', 'caller_reference'=>'252cf11bbd71e2',
            'refund_amount'=>null, 'timestamp'=>'-10d', 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('payments', array('id'=>2,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN6', 'caller_reference'=>'252cf11bbd71e1',
            'refund_amount'=>'5.60', 'timestamp'=>'-20d', 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>2, 'payment_id'=>1));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>2, 'payment_id'=>2));

        $dao = new PaymentMySQLDAO();
        $result = $dao->getSubscribersEarliestPayment(2);
        $this->assertEqual($result->id, 1);

        $this->expectException('PaymentDoesNotExistException');
        $result = $dao->getSubscribersEarliestPayment(5);
    }

    public function testUpdateStatus() {
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('id'=>1, 'status'=>'Not Updated',
        'status_message'=>'Longer not updated message'));
        $dao = new PaymentMySQLDAO();
        $result = $dao->updateStatus(1, 'Updated!', 'Longer Updated! message');
        $this->assertEqual($result, 1);
    }

    public function testSetRefund() {
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('id'=>1, 'transaction_id'=>'asdfsfasdf',
            'caller_reference'=>'adfasdfasdf', 'status'=>'Not Updated', 'status_message'=>'Longer not updated message',
            'refund_amount'=>null, 'refund_date'=>null, 'refund_caller_reference'=>null));
        $dao = new PaymentMySQLDAO();
        $result = $dao->setRefund(1, 'abcdef', 5.6);
        $this->assertEqual($result, 1);

        $payment = $dao->getPayment('asdfsfasdf', 'adfasdfasdf');
        $this->assertEqual($payment->refund_amount, 5.6);
        $this->assertEqual($payment->refund_caller_reference, 'abcdef');
        $this->assertNotEqual($payment->refund_date, '0000-00-00 00:00:00');
    }

    public function testCalculateProRatedAnnualRefund() {
        $payment = array('timestamp'=>date('Y-m-d', strtotime('-5 day')), 'transaction_id'=>'abcd', 'amount'=>50);
        $dao = new PaymentMySQLDAO();
        $result = $dao->calculateProRatedAnnualRefund($payment);
        //refund is different depending what time of day you're running these tests; let's round to estimate
        $this->assertEqual(round($result), 49);

        $payment = array('timestamp'=>date('Y-m-d', strtotime('-35 day')), 'transaction_id'=>'abcd', 'amount'=>50);
        $dao = new PaymentMySQLDAO();
        $result = $dao->calculateProRatedAnnualRefund($payment);
        $this->assertEqual(round($result), 45);

        $payment = array('timestamp'=>date('Y-m-d', strtotime('-0 day')), 'transaction_id'=>'abcd', 'amount'=>50);
        $dao = new PaymentMySQLDAO();
        $result = $dao->calculateProRatedAnnualRefund($payment);
        $this->assertEqual(round($result), 50);

        $payment = array('timestamp'=>date('Y-m-d', strtotime('-350 day')), 'transaction_id'=>'abcd', 'amount'=>50);
        $dao = new PaymentMySQLDAO();
        $result = $dao->calculateProRatedAnnualRefund($payment);
        $this->assertEqual(round($result), 2);
    }
}
