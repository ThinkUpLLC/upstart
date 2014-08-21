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
    }

    protected function buildData($level) {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
            'is_activated'=>1, 'is_admin'=>1, 'thinkup_username'=>'iamtaken', 'membership_level'=>$level));

        return $builders;
    }
}