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
        'pwd_salt'=>$test_salt, 'is_email_verified'=>1, 'is_activated'=>0, 'is_admin'=>1, 'thinkup_username'=>null));

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>7, 'email'=>'unverified@example.com',
        'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_email_verified'=>0, 'is_activated'=>0, 'is_admin'=>0,
        'thinkup_username'=>null, 'verification_code'=>'224455'));

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>8, 'email'=>'waitlist@example.com',
        'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_email_verified'=>0, 'is_activated'=>0, 'is_admin'=>0,
        'thinkup_username'=>null, 'membership_level'=>'Waitlist'));

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

    public function testGetAccountStatusComplimentary() {
        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $subscriber->is_membership_complimentary = true;
        $result = $subscriber->getAccountStatus();
        $this->assertEqual($result, 'Complimentary membership');
    }

    public function testGetAccountStatusNoPaymentNoAuth() {
        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getAccountStatus();
        $this->assertEqual($result, '');
    }

    public function testGetAccountStatusAuthPending() {
        $this->builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'error_message'=>null));
        $this->builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>6,
            'authorization_id'=>1));
        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getAccountStatus();
        $this->assertEqual($result, 'Authorization pending');
    }

    public function testGetAccountStatusAuthFailed() {
        $this->builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'error_message'=>'An error'));
        $this->builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>6,
            'authorization_id'=>1));
        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getAccountStatus();
        $this->assertEqual($result, 'Authorization failed');
    }

    public function testGetAccountStatusPaymentPending() {
        $this->builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'error_message'=>null));
        $this->builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>6,
            'authorization_id'=>1));
        $this->builders[] = FixtureBuilder::build('payments', array('id'=>1, 'transaction_status'=>'Pending'));
        $this->builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>6,
            'payment_id'=>1));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getAccountStatus();
        $this->assertEqual($result, 'Payment pending');
    }

    public function testGetAccountStatusPaymentFailed() {
        $this->builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'error_message'=>null));
        $this->builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>6,
            'authorization_id'=>1));
        $this->builders[] = FixtureBuilder::build('payments', array('id'=>1, 'transaction_status'=>'Failure'));
        $this->builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>6,
            'payment_id'=>1));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getAccountStatus();
        $this->assertEqual($result, 'Payment failed');
    }

    public function testGetAccountStatusPaymentSucceeded() {
        $this->builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'error_message'=>null));
        $this->builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>6,
            'authorization_id'=>1));
        $this->builders[] = FixtureBuilder::build('payments', array('id'=>1, 'transaction_status'=>'Success',
            'timestamp'=>'-0s'));
        $this->builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>6,
            'payment_id'=>1));

        $subscriber = new Subscriber();
        $subscriber->id = 6;
        $result = $subscriber->getAccountStatus();

        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertEqual($result, 'Paid through '.$paid_through_date.$paid_through_year);
    }
}