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

    public function testValidSubscriber() {
        $builders = $this->buildData();
        SessionCache::put('new_subscriber_id', 6);

        $controller = new PayNowController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/Thanks for joining ThinkUp/', $results);
        $this->assertPattern('/A special offer/', $results);
    }

    protected function buildData() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
            'is_activated'=>1, 'is_admin'=>1, 'thinkup_username'=>'iamtaken', 'membership_level'=>'Member'));

        return $builders;
    }
}