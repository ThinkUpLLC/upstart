<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfAmazonFPSAPIAccessor extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $cfg = Config::getInstance();
        $current_access_key = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $current_secret_key = $cfg->getValue('AWS_SECRET_ACCESS_KEY');
        $deprec_access_key = $cfg->getValue('AWS_ACCESS_KEY_ID_DEPREC');
        $deprec_secret_key = $cfg->getValue('AWS_SECRET_ACCESS_KEY_DEPREC');

        $api_accessor = new AmazonFPSAPIAccessor();
        $this->assertIsA($api_accessor, 'AmazonFPSAPIAccessor');
        $this->assertEqual($api_accessor->AWS_ACCESS_KEY_ID, $current_access_key);
        $this->assertEqual($api_accessor->AWS_SECRET_ACCESS_KEY, $current_secret_key);

        $api_accessor = new AmazonFPSAPIAccessor($use_deprecated_tokens = true);
        $this->assertIsA($api_accessor, 'AmazonFPSAPIAccessor');
        $this->assertEqual($api_accessor->AWS_ACCESS_KEY_ID, $deprec_access_key);
        $this->assertEqual($api_accessor->AWS_SECRET_ACCESS_KEY, $deprec_secret_key);
    }

    public function testUpdateTransactionStatus() {
        $api_accessor = new AmazonFPSAPIAccessor();
        //@TODO Mock this call to get API payload from local filesystem
		//$result = $api_accessor->getTransactionStatus('18EECVOQ637BSI61FLCURNQ9I6T3AMSKUZH');
    }

    public function testRefund() {
        $api_accessor = new AmazonFPSAPIAccessor($use_deprecated_tokens = true);
        //@TODO Mock this call to get API payload from local filesystem
        // $caller_reference = time();
        // $response = $api_accessor->refundPayment($caller_reference, '19BSAL8CG468LVAIO93ED6RE779DRDGGJNG',
        //     50);
    }
}