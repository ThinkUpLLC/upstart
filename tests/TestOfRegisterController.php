<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfRegisterController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new RegisterController(true);
        $this->assertIsA($controller, 'RegisterController');
    }
}