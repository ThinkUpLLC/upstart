<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfSettingsController extends UpstartUnitTestCase {
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
        $test_salt = 'test_salt';
        $password = TestLoginHelper::hashPassword('secretpassword', $test_salt);

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com', 'pwd'=>$password,
        'pwd_salt'=>$test_salt, 'is_activated'=>1, 'is_admin'=>1));

        return $builders;
    }

    public function testConstructor() {
        $controller = new SettingsController(true);
        $this->assertIsA($controller, 'SettingsController');
    }

    public function testNotLoggedIn() {
        $controller = new SettingsController(true);
        $results = $controller->go();

        $this->assertPattern("/Log in/", $results);
        $this->assertNoPattern("/Settings go here/", $results);
    }
}