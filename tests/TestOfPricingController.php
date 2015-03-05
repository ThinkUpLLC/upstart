<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfPricingController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new PricingController(true);
        $this->assertIsA($controller, 'PricingController');
    }
}