<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfCheckUsernameController extends UpstartUnitTestCase {

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
        'pwd_salt'=>$test_salt, 'is_activated'=>1, 'is_admin'=>1, 'thinkup_username'=>'iamtaken'));

        return $builders;
    }

    public function testConstructor() {
        $controller = new CheckUsernameController(true);
        $this->assertIsA($controller, 'CheckUsernameController');
    }

    public function testNoParam() {
        $controller = new CheckUsernameController(true);
        $result = $controller->go();
        $this->assertPattern( '/"error":"No username specified"/', $result );
    }

    public function testUsernameDoesNotExist() {
        $controller = new CheckUsernameController(true);
        $_GET['un'] = 'checkme';
        $result = $controller->go();
        $this->assertPattern( '/"available":"true"/', $result );
    }

    public function testUsernameDoesExist() {
        $controller = new CheckUsernameController(true);
        $_GET['un'] = 'iamtaken';
        $result = $controller->go();
        $this->assertPattern( '/"available":"false"/', $result );
    }
}