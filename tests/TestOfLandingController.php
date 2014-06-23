<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
require_once dirname(__FILE__) . '/classes/mock.TwitterOAuth.php';
require_once dirname(__FILE__) . '/classes/mock.facebook.php';

class TestOfLandingController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new LandingController(true);
        $this->assertIsA($controller, 'LandingController');
    }

    public function testControl() {
        $controller = new LandingController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Sign in with<\/small> Twitter/', $results);
        $this->assertPattern('/Sign in with<\/small> Facebook/', $results);
    }
}