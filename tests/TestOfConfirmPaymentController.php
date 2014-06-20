<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
require_once dirname(__FILE__) . '/classes/mock.AmazonFPSAPIAccessor.php';

class TestOfConfirmPaymentController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new ConfirmPaymentController(true);
        $this->assertIsA($controller, 'ConfirmPaymentController');
    }

    public function testNoReturnFromAmazon() {
        $controller = new ConfirmPaymentController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Subscriber ID  does not exist./', $results);
    }

    public function testReturnFromAmazonValidSignature() {
        $builders = $this->buildData();
        SessionCache::put('new_subscriber_id', 6);

        $_GET['callerReference'] = 'abcde';
        $_GET['tokenID'] = 'token1';
        $_GET['level'] = 'member';
        $_GET['status'] = "SC";
        $_GET['certificateUrl'] = "certificate1";
        $_GET['signatureMethod'] = "SHA";
        $_GET['signature'] = 'signature1';

        $controller = new ConfirmPaymentController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/delighted to have you as part of the ThinkUp community/', $results);
    }

    protected function buildData() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
            'is_activated'=>1, 'is_admin'=>1, 'thinkup_username'=>'iamtaken', 'membership_level'=>'Member'));

        return $builders;
    }
}