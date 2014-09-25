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

    public function testCalculateProRatedMonthlyRefund() {
        $op = new SubscriptionOperation();
        $op->subscriber_id = 10;
        $op->payment_reason = 'ThinkUp.com membership';
        $op->transaction_amount = 'USD 5';
        $op->status_code = 'SS';
        $op->buyer_email = 'ginatrapani@example.com';
        $op->reference_id = '24_1407173263';
        $op->amazon_subscription_id = '5a81c762-f3ff-4319-9e07-007fffe7d4da';
        $op->transaction_date = strtotime('-3 days');
        $op->buyer_name = 'Angelina Jolie';
        $op->operation = 'pay';
        $op->recurring_frequency = '1 month';
        $op->payment_method = 'Credit Card';

        $dao = new SubscriptionOperationMySQLDAO();
        $result = $dao->insert($op);
        $this->assertEqual($result, 1);

        $refund = $dao->calculateProRatedMonthlyRefund(10);
        $this->debug($refund);
        $this->assertTrue(($refund > 4.50));
    }

    public function testGetDailyRevenue() {
        $builders = array();

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $day_before = date('Y-m-d', strtotime("-2 days"));

        $dao = new SubscriptionOperationMySQLDAO();

        //No payments or refunds in last 3 days
        $result = $dao->getDailyRevenue();
        $this->debug(Utils::varDumpToString($result));
        $this->assertEqual($result[$today]['revenue'], 0);
        $this->assertEqual($result[$today]['successful_payments'], 0);

        //Successful payment yesterday only
        $builders[] = FixtureBuilder::build('subscription_operations', array('operation'=>'pay',
            'timestamp'=>'-23h', 'transaction_amount'=>'USD 5', 'reference_id'=>'101_adb'));

        $result = $dao->getDailyRevenue();
        $this->debug(Utils::varDumpToString($result));
        $this->assertEqual($result[$today]['revenue'], 0);
        $this->assertEqual($result[$today]['successful_payments'], 0);

        $this->assertEqual($result[$yesterday]['revenue'], 5);
        $this->assertEqual($result[$yesterday]['successful_payments'], 1);

        //Another successful payment yesterday and one day before yesterday
        $builders[] = FixtureBuilder::build('subscription_operations', array('operation'=>'pay',
            'timestamp'=>'-23h', 'transaction_amount'=>'USD 5', 'reference_id'=>'102_adb'));
        //These two are the same operations on one reference ID so should be counted as one pay
        $builders[] = FixtureBuilder::build('subscription_operations', array('operation'=>'pay',
            'timestamp'=>'-46h', 'transaction_amount'=>'USD 5', 'reference_id'=>'103_adb'));
        $builders[] = FixtureBuilder::build('subscription_operations', array('operation'=>'pay',
            'timestamp'=>'-46h', 'transaction_amount'=>'USD 5', 'reference_id'=>'103_adb'));
        //Refund yesterday
        $builders[] = FixtureBuilder::build('subscription_operations', array('operation'=>'refund',
            'timestamp'=>'-46h', 'transaction_amount'=>'USD 5', 'reference_id'=>'103_abc'));

        $result = $dao->getDailyRevenue();
        $this->debug(Utils::varDumpToString($result));
        $this->assertEqual($result[$today]['revenue'], 0);
        $this->assertEqual($result[$today]['successful_payments'], 0);

        $this->assertEqual($result[$yesterday]['revenue'], 10);
        $this->assertEqual($result[$yesterday]['successful_payments'], 2);

        $this->assertEqual($result[$day_before]['revenue'], 5);
        $this->assertEqual($result[$day_before]['successful_payments'], 1);
        $this->assertEqual($result[$day_before]['refunds'], 1);
    }
}