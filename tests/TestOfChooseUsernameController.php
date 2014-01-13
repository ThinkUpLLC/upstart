<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfChooseUsernameController extends UpstartUnitTestCase {

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

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'usernamenotset@example.com',
            'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_activated'=>1, 'is_admin'=>1, 'thinkup_username'=>null));

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>7, 'email'=>'usernameset@example.com',
            'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_activated'=>1, 'is_admin'=>1, 'thinkup_username'=>'set'));

        return $builders;
    }

    public function testConstructor() {
        $controller = new ChooseUsernameController(true);
        $this->assertIsA($controller, 'ChooseUsernameController');
    }

    public function testNotLoggedIn() {
        $controller = new ChooseUsernameController(true);
        $result = $controller->go();
        $this->assertPattern( '/Log in/', $result );
        $this->assertNoPattern( '/Settings/', $result );
    }

    public function testNotSetNoParam() {
        $this->simulateLogin('usernamenotset@example.com');
        $controller = new ChooseUsernameController(true);
        $result = $controller->go();
        $this->debug($result);
        $this->assertPattern( '/Think carefully/', $result );
        $this->assertNoPattern( '/Settings/', $result );
    }

    public function testNotSetPostValidNotTakenUsername() {
        $this->simulateLogin('usernamenotset@example.com');
        $controller = new ChooseUsernameController(true);
        $_POST['username'] = 'setme';
        $result = $controller->go();
        $this->debug($result);
        $this->assertPattern( '/You picked a username!/', $result );
    }

    public function testNotSetPostValidTakenUsername() {
        $this->simulateLogin('usernamenotset@example.com');
        $controller = new ChooseUsernameController(true);
        $_POST['username'] = 'set';
        $result = $controller->go();
        $this->assertNoPattern( '/Settings/', $result );
    }

    public function testNotSetPostInvalidUsernames() {
        $this->simulateLogin('usernamenotset@example.com');
        $controller = new ChooseUsernameController(true);
        //too long
        $_POST['username'] = 'invalidusernameitiswaytoolongmorethan15characters';
        $result = $controller->go();
        //$this->debug($result);
        $this->assertPattern( '/Your username must be 3\-15 characters and contain only numbers and/', $result );
        $this->assertNoPattern( '/Settings/', $result );

        //too short
        $_POST['username'] = 'ya';
        $result = $controller->go();
        //$this->debug($result);
        $this->assertPattern( '/Your username must be 3\-15 characters and contain only numbers and/', $result );
        $this->assertNoPattern( '/Settings/', $result );

        //special char
        $_POST['username'] = 'y@a';
        $result = $controller->go();
        //$this->debug($result);
        $this->assertPattern( '/Your username must be 3\-15 characters and contain only numbers and/', $result );
        $this->assertNoPattern( '/Settings/', $result );

        //dash
        $_POST['username'] = 'ydd-a';
        $result = $controller->go();
        //$this->debug($result);
        $this->assertPattern( '/Your username must be 3\-15 characters and contain only numbers and/', $result );
        $this->assertNoPattern( '/Settings/', $result );

        //space
        $_POST['username'] = 'ydd a';
        $result = $controller->go();
        //$this->debug($result);
        $this->assertPattern( '/Your username must be 3\-15 characters and contain only numbers and/', $result );
        $this->assertNoPattern( '/Settings/', $result );
    }
}