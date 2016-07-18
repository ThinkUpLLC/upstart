<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfRefundAndCloseAccountsController extends UpstartUnitTestCase {
    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        $this->builders = null;
    }

    public function testConstructor() {
        $controller = new LoginController(true);
        $this->assertIsA($controller, 'LoginController');
    }

}