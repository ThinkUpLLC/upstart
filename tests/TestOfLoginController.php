<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfLoginController extends UpstartUnitTestCase {
    /**
     * Test CSRF Token
     */
    const CSRF_TOKEN = 'test_csrf_token_123';

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
        'pwd_salt'=>$test_salt, 'is_email_verified'=>1, 'is_activated'=>0, 'is_admin'=>1, 'thinkup_username'=>null));

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>7, 'email'=>'unverified@example.com',
        'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_email_verified'=>0, 'is_activated'=>0, 'is_admin'=>0,
        'thinkup_username'=>null, 'verification_code'=>'224455'));

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>8, 'email'=>'waitlist@example.com',
        'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_email_verified'=>0, 'is_activated'=>0, 'is_admin'=>0,
        'thinkup_username'=>null, 'membership_level'=>'Waitlist'));

        return $builders;
    }

    public function testConstructor() {
        $controller = new LoginController(true);
        $this->assertIsA($controller, 'LoginController');
    }

    public function testNoSubmission() {
        $controller = new LoginController(true);
        $results = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('controller_title'), 'Log in');
        $this->assertPattern("/Log in/", $results);
    }

    public function testNoEmail() {
        $_POST['Submit'] = 'Log In';
        $_POST['email'] = '';
        $_POST['pwd'] = 'somepassword';
        $controller = new LoginController(true);
        $results = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('controller_title'), 'Log in');
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), 'You\'ll need to enter an email address.');
        $this->assertPattern("/Log in/", $results);
    }

    public function testNoPassword() {
        $_POST['Submit'] = 'Log In';
        $_POST['email'] = 'me@example.com';
        $_POST['pwd'] = '';
        $controller = new LoginController(true);
        $results = $controller->go();
        $this->debug($results);

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('controller_title'), 'Log in');
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), 'You\'ll need a password.');
        $this->assertPattern("/Log in/", $results);
    }

    public function testUserNotFound() {
        $_POST['Submit'] = 'Log In';
        $_POST['email'] = 'me1@example.com';
        $_POST['pwd'] = 'ddd';
        $controller = new LoginController(true);
        $results = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('controller_title'), 'Log in');
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), 'Sorry, can\'t find that email.');
        $this->assertPattern("/Log in/", $results);
    }

    public function testWaitlistedUser() {
        $_POST['Submit'] = 'Log In';
        $_POST['email'] = 'waitlist@example.com';
        $_POST['pwd'] = 'secretpassword';
        $controller = new LoginController(true);
        $results = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('controller_title'), 'Log in');
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), "You're not a ThinkUp member yet. ".
          "<a href=\"http://thinkup.com\">Join now!</a>");
        $this->assertPattern("/Log in/", $results);
    }

    public function testIncorrectPassword() {
        $_POST['Submit'] = 'Log In';
        $_POST['email'] = 'me@example.com';
        $_POST['pwd'] = 'notherightpassword';
        $controller = new LoginController(true);
        $results = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('controller_title'), 'Log in');
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), 'That password doesn\'t seem right.');
        $this->assertPattern("/Log in/", $results);
    }

    public function testUnverifiedUserWithVerificationCode() {
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('unverified@example.com');
        $this->assertFalse($subscriber->is_email_verified);

        $_POST['Submit'] = 'Log In';
        $_POST['email'] = 'unverified@example.com';
        $_POST['pwd'] = 'secretpassword';
        $_GET['usr'] = 'unverified@example.com';
        $_GET['code'] = '224455';
        $controller = new LoginController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern("/unverified@example.com/", $results);
        $subscriber = $dao->getByEmail('unverified@example.com');
        $this->assertTrue($subscriber->is_email_verified);
    }

    public function testCleanXSS() {
        $_POST['Submit'] = 'Log In';
        $_POST['email'] = "me@example.com <script>alert('wa');</script>";
        $_POST['pwd'] = 'notherightpassword';
        $controller = new LoginController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern("/me@example.com &#60;script&#62;alert\(&#39;wa&#39;\);&#60;\/script&#62;/", $results);
    }

    public function testCorrectUserPasswordAndUniqueSalt() {
        $_POST['Submit'] = 'Log In';
        $_POST['email'] = 'salt@example.com';
        $_POST['pwd'] = 'secretpassword';

        $controller = new LoginController(true);
        $results = $controller->go();

        $this->assertPattern("/salt@example.com/", $results);
    }

    public function testAlreadyLoggedIn() {
        $this->simulateLogin('me@example.com', true);

        $controller = new LoginController(true);
        $results = $controller->go();
        $this->debug($results);

        $this->assertNoPattern('/Log in/', $results);
    }

    public function testFailedLoginIncrements() {
        $hashed_pass = TestLoginHelper::hashPassword("blah", "blah");
        $subscriber_dao = new SubscriberMySQLDAO();

        $subscriber = array('id'=>2, 'email'=>'me2@example.com', 'pwd'=>$hashed_pass, 'is_activated'=>1,
        'pwd_salt'=>'blah');
        $builder = FixtureBuilder::build('subscribers', $subscriber);

        //try 5 failed logins then a successful one and assert failed login count gets reset
        $i = 1;
        while ($i <= 5) {
            $_POST['Submit'] = 'Log In';
            $_POST['email'] = 'me2@example.com';
            $_POST['pwd'] = 'incorrectpassword';
            $controller = new LoginController(true);
            $results = $controller->go();

            $v_mgr = $controller->getViewManager();
            $this->assertEqual($v_mgr->getTemplateDataItem('controller_title'), 'Log in');
            $this->assertPattern("/That password doesn\'t seem right/", $v_mgr->getTemplateDataItem('error_msg'));
            $subscriber = $subscriber_dao->getByEmail('me2@example.com');
            $this->assertEqual($subscriber->failed_logins, $i);
            $i = $i + 1;
        }

        $_POST['Submit'] = 'Log In';
        $_POST['email'] = 'me2@example.com';
        $_POST['pwd'] = 'blah';
        $controller = new LoginController(true);
        $results = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('controller_title'), 'Log in');
        $this->assertNoPattern("/That password doesn\'t seem right/", $v_mgr->getTemplateDataItem('error_msg'));
        $subscriber = $subscriber_dao->getByEmail('me2@example.com');
        $this->assertEqual($subscriber->failed_logins, 0);
    }

    public function testFailedLoginLockout() {
        $hashed_pass =TestLoginHelper::hashPassword("blah", "blah");
        $subscriber_dao = new SubscriberMySQLDAO();

        $subscriber = array('id'=>2, 'email'=>'me2@example.com', 'pwd'=>$hashed_pass, 'is_activated'=>1,
            'pwd_salt'=>'blah');
        $builder = FixtureBuilder::build('subscribers', $subscriber);

        //force login lockout by providing the wrong password more than 10 times
        $i = 1;
        while ($i <= 11) {
            $_POST['Submit'] = 'Log In';
            $_POST['email'] = 'me2@example.com';
            $_POST['pwd'] = 'blah1';
            $controller = new LoginController(true);
            $results = $controller->go();

            $v_mgr = $controller->getViewManager();
            $this->assertEqual($v_mgr->getTemplateDataItem('controller_title'), 'Log in');

            $subscriber = $subscriber_dao->getByEmail('me2@example.com');

            if ($i < 10) {
                $this->assertPattern("/That password doesn\'t seem right/", $v_mgr->getTemplateDataItem('error_msg'));
                $this->assertEqual($subscriber->failed_logins, $i);
            } else {
                $this->assertEqual("Inactive account. Account deactivated due to too many failed logins. ".
                '<a href="forgot.php">Reset your password.</a>', $v_mgr->getTemplateDataItem('error_msg'));
                $this->assertEqual($subscriber->account_status, "Account deactivated due to too many failed logins");
            }
            $i = $i + 1;
        }
    }

    /**
     * Wrapper for logging in a in a test
     * @param str $email
     * @param bool $is_admin Default to false
     * @param bool $use_csrf_token Whether or not to put down valid CSRF token, default to false
     */
    protected function simulateLogin($email, $is_admin = false, $use_csrf_token = false) {
        SessionCache::put('user', $email);
        if ($is_admin) {
            SessionCache::put('user_is_admin', true);
        }
        if ($use_csrf_token) {
            SessionCache::put('csrf_token', self::CSRF_TOKEN);
        }
    }
}