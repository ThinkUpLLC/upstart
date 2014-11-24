<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
require_once dirname(__FILE__) . '/classes/mock.AmazonFPSAPIAccessor.php';

class TestOfConfirmPaymentController extends UpstartUnitTestCase {

    protected $subscriber = null;

    protected $builders = null;

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        if (isset($this->subscriber)) {
            $this->tearDownInstall($this->subscriber);
        }
        $this->builders = null;
        $this->subscriber = null;
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
        $results = $this->buildDataAndConfirmPayment('twitter', true, true);
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/Welcome to ThinkUp/', $results);
        $this->assertPattern('/Your Insights/', $results);

        $dao = new ThinkUpTablesMySQLDAO('iamtaken');
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM thinkupstart_iamtaken'
            . '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['is_free_trial'], 0);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail('me@example.com');
        $this->assertEqual($subscriber->subscription_status, 'Paid');
        $this->assertNotNull($subscriber->paid_through);

        //Refresh
        $results = $this->confirmPaymentControl(true);
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/Whoa there\! It looks like you already paid for your ThinkUp/', $results);
    }

    public function testReturnFromAmazonValidSignatureMissingParams() {
        $results = $this->buildDataAndConfirmPayment('twitter', false);
        $this->assertNoPattern('/Subscriber ID  does not exist./', $results);
        $this->assertPattern('/Oops\! Something went wrong and our team is looking into it./', $results);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail('me@example.com');
        $this->assertEqual($subscriber->subscription_status, 'Free trial');
        $this->assertNull($subscriber->paid_through);
    }

    public function testReturnFromAmazonWithTwitter() {
        $results = $this->buildDataAndConfirmPayment('twitter', true, true);
        $this->assertPattern('/Add a Facebook account/', $results);
    }

    public function testReturnFromAmazonWithFacebook() {
        $results = $this->buildDataAndConfirmPayment('facebook', true, true);
        $this->assertPattern('/Add a Twitter account/', $results);
    }

    // We have multiple tests that all need this, so we’ll keep it DRY.
    protected function buildDataAndConfirmPayment($network = 'twitter', $include_all_params=true, $is_installed=true) {
        $builders = $this->buildData($network, $is_installed);
        SessionCache::put('new_subscriber_id', 6);
        return self::confirmPaymentControl($include_all_params);
    }

    // We have multiple tests that all need this, so we’ll keep it DRY.
    protected function confirmPaymentControl($include_all_params = true) {
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

    protected function buildData($network = 'twitter', $is_installed = true) {
        if (!isset($this->builders)) {
            $subscriber_array = array('id'=>6, 'email'=>'me@example.com', 'is_admin'=>1, 'thinkup_username'=>'iamtaken',
                'membership_level'=>'Member', 'network'=>$network, 'paid_through'=>null);
            if ($is_installed) {
                //Not installed; will be in the next section if it should be
                $subscriber_array['date_installed'] = null;
                $subscriber_array['is_activated'] = 0;
            }
            $builders[] = FixtureBuilder::build('subscribers', $subscriber_array);
            $this->builders = $builders;
        }

        if (!isset($this->subscriber) && $is_installed) {
            $dao = new SubscriberMySQLDAO();
            $subscriber = $dao->getByID(6);
            $this->subscriber = $subscriber;
            try {
                $this->setUpInstall($subscriber);
                $this->debug("Setting up install");
            } catch (Exception $e) {
                $this->debug("Caught ".get_class($e).": ".$e->getMessage());
            }
        } else {
            $this->debug("Not setting up install; already exists");
        }
    }

    public function testInvalidClaimCode() {
        $builders = $this->buildData();
        SessionCache::put('new_subscriber_id', 6);
        $_GET['code'] = 'asdfasd';

        $controller = new ConfirmPaymentController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/That code doesn&#39;t seem right. Check it and try again/', $results);
        $this->assertNoPattern('/Oops! There was a problem processing your code/', $results);
        $this->assertNoPattern('/Whoops! It looks like that code has already been used/', $results);
        $this->assertNoPattern('/It worked! We&#39;ve applied your coupon code./', $results);
    }

    public function testRedeemedClaimCode() {
        $builders = $this->buildData();
        $builders[] = FixtureBuilder::build('claim_codes', array('code'=>'1234567890AB', 'is_redeemed'=>1));
        SessionCache::put('new_subscriber_id', 6);
        $_GET['code'] = '1234567890AB';

        $controller = new ConfirmPaymentController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/That code doesn&#39;t seem right. Check it and try again?/', $results);
        $this->assertNoPattern('/Oops! There was a problem processing your code/', $results);
        $this->assertPattern('/Whoops! It looks like that code has already been used/', $results);
        $this->assertNoPattern('/It worked! We&#39;ve applied your coupon code./', $results);

        $dao = new ThinkUpTablesMySQLDAO('iamtaken');
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM thinkupstart_iamtaken'
            . '.tu_owners o WHERE o.email = "paid@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['is_free_trial'], 0);
    }

    public function testValidClaimCodeWithSpacesLowercase() {
        $builders = $this->buildData();
        $builders[] = FixtureBuilder::build('claim_codes', array('code'=>'1234567890AB', 'is_redeemed'=>0));
        SessionCache::put('new_subscriber_id', 6);
        $_GET['code'] = '123456   7890aB';

        $controller = new ConfirmPaymentController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/That code doesn&#39;t seem right. Check it and try again?/', $results);
        $this->assertNoPattern('/Oops! There was a problem processing your code/', $results);
        $this->assertNoPattern('/Whoops! It looks like that code has already been used/', $results);
        $this->assertPattern('/It worked! We&#39;ve applied your coupon code/', $results);

        $dao = new ThinkUpTablesMySQLDAO('iamtaken');
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM thinkupstart_iamtaken'
            . '.tu_owners o WHERE o.email = "paid@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['is_free_trial'], 0);
    }
}