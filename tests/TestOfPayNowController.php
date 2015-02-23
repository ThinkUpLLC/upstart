<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
require_once dirname(__FILE__) . '/classes/mock.AmazonFPSAPIAccessor.php';

class TestOfPayNowController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new PayNowController(true);
        $this->assertIsA($controller, 'PayNowController');
    }

    public function testNoSubscriber() {
        $controller = new PayNowController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Subscriber ID  does not exist./', $results);
    }

    public function testValidSubscriberMemberLevel() {
        $builders = $this->buildData('Member');
        SessionCache::put('new_subscriber_id', 6);

        $controller = new PayNowController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/Terrific! Your 14-day free trial has begun./', $results);
        $this->assertPattern('/the book we wrote for you/', $results);
        $this->assertPattern('/Your Insights/', $results);
        $this->assertPattern('/5 a month/', $results);
        $this->assertPattern('/Just 5 bucks a month/', $results);
    }

    public function testValidSubscriberProLevel() {
        $builders = $this->buildData('Pro');
        SessionCache::put('new_subscriber_id', 6);

        $controller = new PayNowController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/Terrific! Your 14-day free trial has begun./', $results);
        $this->assertPattern('/the book we wrote for you/', $results);
        $this->assertPattern('/Your Insights/', $results);
        $this->assertNoPattern('/5 a month/', $results);
        $this->assertPattern('/10 a month/', $results);
        $this->assertPattern('/Just 10 bucks a month/', $results);
    }

    public function testClaimCodeInvalid() {
        $builders = $this->buildData('Member');
        $builders[] = FixtureBuilder::build('claim_codes', array('code'=>'1234567890AB', 'is_redeemed'=>0));
        SessionCache::put('new_subscriber_id', 6);
        $_POST['claim_code'] = 'adsfadsf';

        $controller = new PayNowController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/That code doesn&#39;t seem right. Check it and try again?/', $results);
    }

    public function testClaimCodeRedeemed() {
        $builders = $this->buildData('Member');
        $builders[] = FixtureBuilder::build('claim_codes', array('code'=>'1234567890AB', 'is_redeemed'=>1));
        SessionCache::put('new_subscriber_id', 6);
        $_POST['claim_code'] = '1234567890AB';

        $controller = new PayNowController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/That code doesn&#39;t seem right. Check it and try again?/', $results);
        $this->assertPattern('/Whoops! It looks like that code has already been used./', $results);
    }

    public function testClaimCodeValidSubmittedLowercaseWithSpaces() {
        $builders = $this->buildData('Member');
        $builders[] = FixtureBuilder::build('claim_codes', array('code'=>'1234567890AB', 'is_redeemed'=>0));
        SessionCache::put('new_subscriber_id', 6);
        $_POST['claim_code'] = '1234 5678 90Ab';

        $controller = new PayNowController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertFalse($results);
    }

    protected function buildData($level) {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
            'is_activated'=>1, 'is_admin'=>1, 'thinkup_username'=>'iamtaken', 'membership_level'=>$level));

        return $builders;
    }
}