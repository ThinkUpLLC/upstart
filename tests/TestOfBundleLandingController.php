<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
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
        $this->assertPattern( '/Get it now/', $result);
    }

    public function testOfControllerAllParamsValidSig() {
        $_GET = self::setUpPaymentParams();

        $controller = new BundleLandingController(true);
        $results = $controller->go();
        $this->debug($results);

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('success_msg'),
            'Success! You\'ve purchased The Good Web Bundle');

        $confirmation_email = Mailer::getLastMail();
        //$this->debug($confirmation_email);
        $this->assertPattern('/Thanks for purchasing The Good Web Bundle/', $confirmation_email);
    }

    public function testOfControllerAllParamsInvalidSig() {
        $_GET = self::setUpPaymentParams();
        $_GET['signatureValidity'] = false;

        $controller = new BundleLandingController(true);
        $results = $controller->go();
        $this->debug($results);

        $v_mgr = $controller->getViewManager();
        $this->assertPattern('/Oops/', $v_mgr->getTemplateDataItem('error_msg'));

        $confirmation_email = Mailer::getLastMail();
        $this->assertEqual($confirmation_email, '');
    }

    public function testOfControllerMissingParams() {
        $_GET = self::setUpPaymentParams(false);

        $controller = new BundleLandingController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern( '/Get it now/', $results);

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
            $_GET['paymentReason'] = "ThinkUp.com monthly membership";
            $_GET['transactionAmount'] = 'USD 100';
            $_GET['operation'] = 'pay';
        }
        $_GET['transactionDate'] = '1407173277';
        $_GET['buyerName'] = 'Angelina Jolie';
        $_GET['buyerEmail'] = 'angelina@example.com';
        return $_GET;
    }
}
