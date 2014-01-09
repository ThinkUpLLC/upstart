<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfSubscriberPaymentMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new SubscriberPaymentMySQLDAO();
        $this->assertIsA($dao, 'SubscriberPaymentMySQLDAO');
    }

    public function testInsert() {
        $dao = new SubscriberPaymentMySQLDAO();
        $result = $dao->insert($sub_id = 12, $pay_id = 99);
        $this->assertNotEqual(0, $result);

        $sql = "SELECT * FROM subscriber_payments WHERE id = ".$result;
        $stmt = SubscriberPaymentMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEqual($data['subscriber_id'], $sub_id);
        $this->assertEqual($data['payment_id'], $pay_id);
        $this->assertTrue(strtotime($data['timestamp']) > (time()-60));
    }

    public function testGetBySubscriber() {
        $payment_dao = new PaymentMySQLDAO();
        $dao = new SubscriberPaymentMySQLDAO();

        $usera = 2;
        $userb = 1;
        // Insert 2 for usera and 1 for userb
        $pid = $payment_dao->insert(1111, 1234, 'OK', 99, 'test');
        $dao->insert($usera, $pid);
        $pid = $payment_dao->insert(2222, 1234, 'SURE', 88, 'test2');
        $dao->insert($usera, $pid);
        $pid = $payment_dao->insert(9999, 8888, 'FAILED', 0, 'test3');
        $dao->insert($userb, $pid);

        $payments = $dao->getBySubscriber($usera);
        $this->assertEqual(2, count($payments));
        // Should be in ascending order
        $this->assertEqual($payments[0]['transaction_id'], 1111);
        $this->assertEqual($payments[0]['request_id'], 1234);
        $this->assertEqual($payments[0]['transaction_status'], 'OK');
        $this->assertEqual($payments[0]['amount'], 99);
        $this->assertEqual($payments[0]['caller_reference'], 'test');
        $this->assertEqual($payments[1]['transaction_id'], 2222);
        $this->assertEqual($payments[1]['request_id'], 1234);
        $this->assertEqual($payments[1]['transaction_status'], 'SURE');
        $this->assertEqual($payments[1]['amount'], 88);
        $this->assertEqual($payments[1]['caller_reference'], 'test2');

        $payments = $dao->getBySubscriber($userb);
        $this->assertEqual(1, count($payments));
        $this->assertEqual($payments[0]['transaction_id'], 9999);
        $this->assertEqual($payments[0]['request_id'], 8888);
        $this->assertEqual($payments[0]['transaction_status'], 'FAILED');
        $this->assertEqual($payments[0]['amount'], 0);
        $this->assertEqual($payments[0]['caller_reference'], 'test3');
    }

}
