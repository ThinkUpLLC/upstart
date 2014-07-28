<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
require_once dirname(__FILE__) . '/classes/mock.AmazonFPSAPIAccessor.php';
require_once dirname(__FILE__) . '/classes/mock.SignatureUtilsForOutbound.php';

class TestOfAPIIPNController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
        $_SESSION = array();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new APIIPNController(true);
        $this->assertIsA($controller, 'APIIPNController');
    }

    public function testControl() {
        $controller = new APIIPNController(true);
        $controller->control();
    }
}