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

    public function testGetSubscriptionStatusAndPaidThrough() {
        $helper = new SubscriptionHelper();

        $subscriber = new Subscriber();
        $subscriber->id = 10;
        $subscriber->is_membership_complimentary = true;

        //Complimentary
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Complimentary membership');
        $this->assertNull($new_values['paid_through']);

        //Monthly
        $subscriber->is_membership_complimentary = false;
        $builders = array();
        $builders[] = FixtureBuilder::build('subscription_operations', array('subscriber_id'=>10,
            'operation'=>'pay', 'status_code'=>'PS', 'transaction_date'=>'-5h'));
        $new_values = $helper->getSubscriptionStatusAndPaidThrough($subscriber);
        $this->assertEqual($new_values['subscription_status'], 'Paid');
        $this->assertNotNull($new_values['paid_through']);
        $this->assertEqual(substr($new_values['paid_through'], 0, 10),
            substr(date(DATE_ATOM,  strtotime('+1 month')), 0, 10));

        //Annual
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
            substr(date(DATE_ATOM,  strtotime('+1 year')), 0, 10));

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
}