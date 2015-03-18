<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once dirname(__FILE__) . '/classes/mock.AmazonFPSAPIAccessor.php';

class TestOfBundleLandingController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testOfControllerNoParams() {
        $controller = new BundleLandingController(true);
        $result = $controller->go();
        $this->debug($result);

        $this->assertPattern('/Web Bundle/', $result);
        $this->assertNoPattern( '/Get it now/', $result);
        $this->assertPattern( '/Thanks for supporting good independent web sites/', $result);
    }

    public function testOfControllerMissingParams() {
        $_GET = self::setUpPaymentParams(false);

        $controller = new BundleLandingController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern( '/Get it now/', $results);
        $this->assertPattern( '/Thanks for supporting good independent web sites/', $results);

        $confirmation_email = Mailer::getLastMail();
        $this->assertEqual($confirmation_email, '');
    }

    // We have multiple tests that all need this, so we’ll keep it DRY.
    protected function setUpPaymentParams($include_all_params = true) {
        //array('paymentReason', 'transactionAmount',
        //'transactionId', 'status', 'buyerEmail', 'referenceId', 'transactionDate', 'buyerName', 'operation' );
        $_GET['transactionId'] = 'abcde';
        $_GET['referenceId'] = '24_34390d';
        $_GET['status'] = "PS";
        $_GET['certificateUrl'] = "certificate1";
        $_GET['signatureMethod'] = "SHA";
        $_GET['signature'] = 'signature1';
        if ($include_all_params) {
            $_GET['paymentReason'] = "ThinkUp.com membership";
            $_GET['transactionAmount'] = 'USD 100';
            $_GET['operation'] = 'pay';
        }
        $_GET['transactionDate'] = '1407173277';
        $_GET['buyerName'] = 'Angelina Jolie';
        $_GET['buyerEmail'] = 'angelina@example.com';
        return $_GET;
    }

    public function testSendPurchaseConfirmationEmail() {
        $controller = new BundleLandingController(true);
        $message = $controller->sendConfirmationEmail('buyer@example.com', 'AAAABBBBCCCCDDDD', 'AAAA BBBB CCCC DDDD');
        $this->assertPattern('/Thanks for buying the Good Web Bundle!/', $message);
        $this->assertPattern('/AAAABBBBCCCCDDDD/', $message);

        $this->debug($message);
    }
}
