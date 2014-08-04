<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfSubscriptionOperationMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new SubscriptionOperationMySQLDAO();
        $this->assertIsA($dao, 'SubscriptionOperationMySQLDAO');
    }

    public function testInsertAndGetLatest() {
        $op = new SubscriptionOperation();
        $op->subscriber_id = 10;
        $op->payment_reason = 'ThinkUp.com membership';
        $op->transaction_amount = 'USD 5';
        $op->status_code = 'SS';
        $op->buyer_email = 'ginatrapani@example.com';
        $op->reference_id = '24_1407173263';
        $op->amazon_subscription_id = '5a81c762-f3ff-4319-9e07-007fffe7d4da';
        $op->transaction_date = '1407173277';
        $op->buyer_name = 'Angelina Jolie';
        $op->operation = 'pay';
        $op->recurring_frequency = '1 month';
        $op->payment_method = 'Credit Card';

        $dao = new SubscriptionOperationMySQLDAO();
        $result = $dao->insert($op);
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscription_operations WHERE id = ".$result;
        $stmt = SubscriptionOperationMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual('ginatrapani@example.com', $data['buyer_email']);
        $this->assertEqual('1 month', $data['recurring_frequency']);
        $this->assertEqual('Credit Card', $data['payment_method']);

        //Test insert duplicate
        $this->expectException('DuplicateSubscriptionOperationException');
        $result = $dao->insert($op);

        $result = null;
        $result = $dao->getLatest(10);
        $this->assertIsA($result, 'SubscriptionOperation');
    }
}