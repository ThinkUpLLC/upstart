<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfSubscriptionHelper extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testGetSubscriptionStatusBasedOnOperation() {
        $helper = new SubscriptionHelper();

        $operation = new SubscriptionOperation();

        //Pay operations
        $operation->operation = 'pay';

        $operation->status_code = 'SS';
        $this->assertEqual($helper->getSubscriptionStatusBasedOnOperation($operation), 'Paid');

        $operation->status_code = 'PS';
        $this->assertEqual($helper->getSubscriptionStatusBasedOnOperation($operation), 'Paid');

        $operation->status_code = 'PF';
        $this->assertEqual($helper->getSubscriptionStatusBasedOnOperation($operation), 'Payment failed');

        $operation->status_code = 'SF';
        $this->assertEqual($helper->getSubscriptionStatusBasedOnOperation($operation), 'Payment failed');

        $operation->status_code = 'PI';
        $this->assertEqual($helper->getSubscriptionStatusBasedOnOperation($operation), 'Payment pending');

        $operation->status_code = 'SI';
        $this->assertEqual($helper->getSubscriptionStatusBasedOnOperation($operation), 'Payment pending');

        //Pay operations
        $operation->operation = 'refund';
        $this->assertEqual($helper->getSubscriptionStatusBasedOnOperation($operation), 'Refunded');
    }

    public function testGetNextAnnualChargeAmount() {
        $helper = new SubscriptionHelper();
        $this->assertEqual($helper->getNextAnnualChargeAmount('Early Bird'), 50);
        $this->assertEqual($helper->getNextAnnualChargeAmount('Late Bird'), 50);
        $this->assertEqual($helper->getNextAnnualChargeAmount('Member'), 60);
        $this->assertEqual($helper->getNextAnnualChargeAmount('Pro'), 120);
        $this->assertEqual($helper->getNextAnnualChargeAmount('Exec'), 996);
        $this->assertEqual($helper->getNextAnnualChargeAmount('Huh?'), 0);
    }

    public function testGetSubscriptionStatusAndPaidThrough() {
        $helper = new SubscriptionHelper();

        $subscriber = new Subscriber();
        $subscriber->id = 10;
        $subscriber->is_membership_complimentary = true;

        //Complimentary
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Complimentary membership');
        $this->assertNull($new_values['paid_through']);

        //Monthly subscription
        $subscriber->is_membership_complimentary = false;
        $builders = array();
        $builders[] = FixtureBuilder::build('subscription_operations', array('subscriber_id'=>10,
            'operation'=>'pay', 'status_code'=>'PS', 'transaction_date'=>'-5h', 'recurring_frequency'=>'1 month'));
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Paid');
        $this->assertNotNull($new_values['paid_through']);
        $this->assertEqual(substr($new_values['paid_through'], 0, 10),
            substr(date('Y-m-d H:i:s',  strtotime('+1 month')), 0, 10));

        //Annual subscription
        $subscriber->is_membership_complimentary = false;
        $builders = array();
        $builders[] = FixtureBuilder::build('subscription_operations', array('subscriber_id'=>10,
            'operation'=>'pay', 'status_code'=>'PS', 'transaction_date'=>'-5h', 'recurring_frequency'=>'12 months'));
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Paid');
        $this->assertNotNull($new_values['paid_through']);
        $this->assertEqual(substr($new_values['paid_through'], 0, 10),
            substr(date('Y-m-d H:i:s',  strtotime('+12 months')), 0, 10));

        //Annual FPS auth
        //Success
        $builders = null; //clear fixtures
        $builders = array();
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>10,
            'payment_id'=>100));
        $builders[] = FixtureBuilder::build('payments', array('id'=>100, 'timestamp'=>'-4h',
            'transaction_status'=>'Success'));
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Paid');
        $this->assertNotNull($new_values['paid_through']);
        $this->assertEqual(substr($new_values['paid_through'], 0, 10),
            substr(date('Y-m-d H:i:s',  strtotime('+1 year')), 0, 10));

        //Failure
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>10,
            'payment_id'=>101));
        $builders[] = FixtureBuilder::build('payments', array('id'=>101, 'timestamp'=>'-3h',
            'transaction_status'=>'Failure'));
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Payment failed');
        $this->assertNull($new_values['paid_through']);

        //Pending
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>10,
            'payment_id'=>102));
        $builders[] = FixtureBuilder::build('payments', array('id'=>102, 'timestamp'=>'-2h',
            'transaction_status'=>'Failure'));
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Payment failed');
        $this->assertNull($new_values['paid_through']);

        //Free trial
        $builders = null; //clear fixtures
        $subscriber->creation_time =  date('Y-m-d', strtotime('-5 days'));
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Free trial');
        $this->assertNull($new_values['paid_through']);

        //Payment due, trial expired
        $builders = null; //clear fixtures
        $subscriber->creation_time = date('Y-m-d', strtotime('-16 days'));
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Payment due');
        $this->assertNull($new_values['paid_through']);
    }

    public function testUpdateSubscriptionStatusAndPaidThrough() {
        $subscriber = new Subscriber();
        $subscriber->id = 10;
        $subscriber->is_membership_complimentary = true;
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>10));

        //Monthly subscription
        $subscriber->is_membership_complimentary = false;

        $operation = new SubscriptionOperation();
        $operation->subscriber_id = 10;
        $operation->recurring_frequency = '1 month';
        $operation->operation = 'pay';
        $operation->status_code = 'PS';
        $operation->transaction_date = '2014-08-25 08:52:08';

        $helper = new SubscriptionHelper();
        $helper->updateSubscriptionStatusAndPaidThrough($subscriber, $operation);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID(10);
        $this->assertEqual($subscriber->subscription_status, 'Paid');
        $this->assertEqual($subscriber->paid_through, '2014-09-25 08:52:08');
    }
}