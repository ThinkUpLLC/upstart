<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfSubscriber extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
        $this->builders = self::buildData();
    }

    public function tearDown() {
        $this->builders = null;
        parent::tearDown();
    }

    protected function buildData() {
        $builders = array();
        $test_salt = 'test_salt';
        $password = TestLoginHelper::hashPassword('secretpassword', $test_salt);

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com', 'pwd'=>$password,
        'pwd_salt'=>$test_salt, 'is_email_verified'=>1, 'is_activated'=>0, 'is_admin'=>1, 'thinkup_username'=>null,
        'creation_time'=>'-2d'));

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>7, 'email'=>'unverified@example.com',
        'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_email_verified'=>0, 'is_activated'=>0, 'is_admin'=>0,
        'thinkup_username'=>null, 'verification_code'=>'224455', 'creation_time'=>'-10d'));

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>9, 'email'=>'activeinstall@example.com',
        'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_email_verified'=>1, 'is_activated'=>0, 'is_admin'=>0,
        'thinkup_username'=>'unique', 'membership_level'=>'Member', 'is_installation_active'=>1,
        'date_installed'=>'-1d'));

        return $builders;
    }

    public function testConstructor() {
        $subscriber = new Subscriber();
        $this->assertIsA($subscriber, 'Subscriber');
    }

    public function testGetSubscriptionStatusComplimentary() {
        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $subscriber->is_membership_complimentary = true;
        $result = $subscriber->getSubscriptionStatus();
        $this->assertEqual($result, 'Complimentary membership');
    }

    public function testGetSubscriptionStatusNoPaymentNoAuth() {
        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getSubscriptionStatus();
        $this->assertEqual($result, 'Free trial');
    }

    public function testGetSubscriptionStatusPaymentPending() {
        $this->builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'error_message'=>null));
        $this->builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>6,
            'authorization_id'=>1));
        $this->builders[] = FixtureBuilder::build('payments', array('id'=>1, 'transaction_status'=>'Pending'));
        $this->builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>6,
            'payment_id'=>1));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getSubscriptionStatus();
        $this->assertEqual($result, 'Payment pending');
    }

    public function testGetSubscriptionStatusPaymentFailed() {
        $this->builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'error_message'=>null));
        $this->builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>6,
            'authorization_id'=>1));
        $this->builders[] = FixtureBuilder::build('payments', array('id'=>1, 'transaction_status'=>'Failure'));
        $this->builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>6,
            'payment_id'=>1));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getSubscriptionStatus();
        $this->assertEqual($result, 'Payment failed');
    }

    public function testGetSubscriptionStatusPaymentFailedWithoutStatus() {
        $this->builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'error_message'=>null));
        $this->builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>6,
            'authorization_id'=>1));
        $this->builders[] = FixtureBuilder::build('payments', array('id'=>1, 'transaction_status'=>''));
        $this->builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>6,
            'payment_id'=>1));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getSubscriptionStatus();
        $this->assertEqual($result, 'Payment failed');
    }

    public function testGetSubscriptionStatusPaymentSucceeded() {
        $this->builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'error_message'=>null));
        $this->builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>6,
            'authorization_id'=>1));
        $this->builders[] = FixtureBuilder::build('payments', array('id'=>1, 'transaction_status'=>'Success',
            'timestamp'=>'-0s'));
        $this->builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>6,
            'payment_id'=>1));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getSubscriptionStatus();

        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j, ');
        $this->assertEqual($result, 'Paid through '.$paid_through_date.$paid_through_year);
    }

    public function testGetSubscriptionStatusSimplePayPaymentSucceeded() {
        $this->builders[] = FixtureBuilder::build('subscription_operations', array('subscriber_id'=>6,
            'operation'=>'pay', 'status_code'=>'SS', 'transaction_date'=>'2014-08-05 11:51:44'));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getSubscriptionStatus();

        $this->assertEqual($result, 'Paid through Sep 5, 2014');
    }

    public function testGetSubscriptionStatusSimplePayPaymentFailed() {
        $this->builders[] = FixtureBuilder::build('subscription_operations', array('subscriber_id'=>6,
            'operation'=>'pay', 'status_code'=>'SF', 'transaction_date'=>'2014-08-05 11:51:44'));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getSubscriptionStatus();

        $this->assertEqual($result, 'Payment failed');
    }

    public function testGetSubscriptionStatusSimplePayPaymentPending() {
        $this->builders[] = FixtureBuilder::build('subscription_operations', array('subscriber_id'=>6,
            'operation'=>'pay', 'status_code'=>'SI', 'transaction_date'=>'2014-08-05 11:51:44'));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getSubscriptionStatus();

        $this->assertEqual($result, 'Payment pending');
    }

    public function testGetDaysLeftInFreeTrial() {
        $subscriber = new Subscriber();
        $subscriber->creation_time = '2014-01-01';
        $result = $subscriber->getDaysLeftInFreeTrial();
        $this->assertEqual($result, 0);

        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByID(6);
        $result = $subscriber->getDaysLeftInFreeTrial();
        $this->assertEqual($result, 13);

        $subscriber = $dao->getByID(7);
        $result = $subscriber->getDaysLeftInFreeTrial();
        $this->assertEqual($result, 5);
    }
}