<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfAPIValidClaimCodeController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new APIValidClaimCodeController(true);
        $this->assertIsA($controller, 'APIValidClaimCodeController');
    }

    public function testNoCodeSpecified() {
        $controller = new APIValidClaimCodeController(true);
        $results = $controller->go();
        $this->assertPattern('/No code specified/', $results);
    }

    public function testInvalidFormat() {
        $controller = new APIValidClaimCodeController(true);
        $_GET['code'] = "adfasdfa sdfa sdfa sdf adsfads";
        $results = $controller->go();
        $this->assertPattern('/Not a valid code format/', $results);
    }

    public function testNonexistentCode() {
        $controller = new APIValidClaimCodeController(true);
        $_GET['code'] = "1234567890AB";
        $results = $controller->go();
        $this->assertPattern('/"is_valid":false/', $results);
    }

    public function testInvalidCharsInCode() {
        $controller = new APIValidClaimCodeController(true);
        $_GET['code'] = "1234567&90*B";
        $results = $controller->go();
        $this->assertPattern('/Not a valid code format/', $results);
    }

    public function testInvalidSpacesInCode() {
        $controller = new APIValidClaimCodeController(true);
        $_GET['code'] = "12345 678 90AB";
        $results = $controller->go();
        $this->assertPattern('/Not a valid code format/', $results);
    }

    public function testValidCode() {
        $builder = FixtureBuilder::build('claim_codes', array('code'=>'1234567890AB'));
        $controller = new APIValidClaimCodeController(true);

        // Uppercase
        $_GET['code'] = "1234567890AB";
        $results = $controller->go();
        $this->assertPattern('/"is_valid":true/', $results);

        // Lowercase
        $_GET['code'] = "1234567890ab";
        $results = $controller->go();
        $this->assertPattern('/"is_valid":true/', $results);
    }
}