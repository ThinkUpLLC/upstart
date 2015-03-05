<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfCheckEmailController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
        $this->builders = self::buildData();
    }

    public function tearDown() {
        $this->builders = null;
        parent::tearDown();
    }

    protected function buildData() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'me@example.com'));
        return $builders;
    }

    public function testConstructor() {
        $controller = new CheckEmailController(true);
        $this->assertIsA($controller, 'CheckEmailController');
    }

    public function testNoParam() {
        $controller = new CheckEmailController(true);
        $result = $controller->go();
        $this->assertPattern( '/"error":"No email specified"/', $result );
    }

    public function testUsernameDoesNotExist() {
        $controller = new CheckEmailController(true);
        $_GET['em'] = 'checkme@example.com';
        $result = $controller->go();
        $this->assertPattern( '/"available":true/', $result );
    }

    public function testUsernameDoesExist() {
        $controller = new CheckEmailController(true);
        $_GET['em'] = 'me@example.com';
        $result = $controller->go();
        $this->assertPattern( '/"available":false/', $result );
    }
}