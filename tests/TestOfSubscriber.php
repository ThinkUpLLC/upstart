<?php
require_once dirname(__FILE__) . '/init.tests.php';

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