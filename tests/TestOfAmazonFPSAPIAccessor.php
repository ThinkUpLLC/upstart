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
        $api_accessor = new AmazonFPSAPIAccessor();
        $this->assertIsA($api_accessor, 'AmazonFPSAPIAccessor');
    }

    public function testUpdateTransactionStatus() {
        $api_accessor = new AmazonFPSAPIAccessor();
        //@TODO Mock this call to get API payload from local filesystem
		//$result = $api_accessor->getTransactionStatus('18EECVOQ637BSI61FLCURNQ9I6T3AMSKUZH');
    }
}