<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfAPIMemberStatusController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new APIMemberStatusController(true);
        $this->assertIsA($controller, 'APIMemberStatusController');
    }

    public function testNoEmailSpecified() {
        $controller = new APIMemberStatusController(true);
        $results = $controller->go();
        $this->assertPattern('/No email specified/', $results);
    }

    public function testInvalidEmailFormat() {
        $controller = new APIMemberStatusController(true);
        $_GET['email'] = "adfasdfa sdfa sdfa sdf adsfads";
        $results = $controller->go();
        $this->assertPattern('/Not a valid email address/', $results);
    }

    public function testNonexistentCode() {
        $controller = new APIMemberStatusController(true);
        $_GET['email'] = "me@example.com";
        $results = $controller->go();
        $this->assertPattern('/Subscriber does not exist/', $results);
    }

    public function testValidMember() {
        $builder = FixtureBuilder::build('subscribers', array('email'=>'valid@example.com',
            'subscription_status'=>'Paid'));
        $controller = new APIMemberStatusController(true);

        // Uppercase
        $_GET['email'] = "VALID@example.com";
        $results = $controller->go();
        $this->assertPattern('/"subscription_status":"Paid/', $results);

        // Lowercase
        $_GET['email'] = "valid@example.com";
        $results = $controller->go();
        $this->assertPattern('/"subscription_status":"Paid/', $results);
    }
}