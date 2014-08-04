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
        $results = $this->confirmPayment();
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/Welcome to ThinkUp/', $results);
        $this->assertPattern('/Your Insights/', $results);

        //Refresh
        $results = $this->confirmPayment();
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/Whoa there\! It looks like you already paid for your ThinkUp/', $results);
    }

    public function testReturnFromAmazonValidSignatureMissingParams() {
        $results = $this->confirmPayment('twitter', false);
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/Oops\! Something went wrong and our team is looking into it./', $results);
    }

    public function testReturnFromAmazonWithTwitter() {
        $results = $this->confirmPayment('twitter');
        $this->assertPattern('/Add a Facebook account/', $results);
    }

    public function testReturnFromAmazonWithFacebook() {
        $results = $this->confirmPayment('facebook');
        $this->assertPattern('/Add a Twitter account/', $results);
    }

    // We have three tests that all need this, so we’ll keep it DRY.
    protected function confirmPayment($network = 'twitter', $include_all_params = true) {
        $builders = $this->buildData($network);
        SessionCache::put('new_subscriber_id', 6);

        $_GET['callerReference'] = 'abcde';
        $_GET['tokenID'] = 'token1';
        $_GET['level'] = 'member';
        $_GET['recur'] = '1 month';
        $_GET['status'] = "SC";
        $_GET['certificateUrl'] = "certificate1";
        $_GET['signatureMethod'] = "SHA";
        $_GET['signature'] = 'signature1';
        if ($include_all_params) {
            $_GET['paymentReason'] = "ThinkUp.com monthly membership";
            $_GET['transactionAmount'] = 'USD 5';
            $_GET['status'] = 'SS';
            $_GET['referenceId'] = '24_34390d';
        }
        $_GET['subscriptionId'] = '5a81c762-f3ff-4319-9e07-007fffe7d4da';
        $_GET['transactionDate'] = '1407173277';
        $_GET['buyerName'] = 'Angelina Jolie';
        $_GET['buyerEmail'] = 'angelina@example.com';
        $_GET['operation'] = 'pay';
        $_GET['recurringFrequency'] = '1 month';
        $_GET['paymentMethod'] = 'Credit Card';


        $controller = new ConfirmPaymentController(true);
        $results = $controller->go();
        $this->debug($results);

        return $results;
    }

    protected function buildData($network = 'twitter') {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
            'is_activated'=>1, 'is_admin'=>1, 'thinkup_username'=>'iamtaken', 'membership_level'=>'Member',
            'network'=>$network));

        return $builders;
    }
}