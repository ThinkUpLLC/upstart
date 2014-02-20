<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfForgotPasswordController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
        $this->builder = self::buildData();
    }

    protected function buildData() {
        $hashed_pass = TestLoginHelper::hashPassword("oldpassword", 'hash');
        $builder = FixtureBuilder::build('subscribers', array('id'=>1, 'full_name'=>'ThinkUp J. User',
        'email'=>'me@example.com', 'pwd'=>$hashed_pass, 'activation_code'=>8888, 'is_activated'=>1,
        'pwd_salt'=>'defaultsalt'));
        return $builder;
    }

    public function tearDown() {
        $this->builder = null;
        parent::tearDown();
    }


    public function testOfControllerNoParams() {
        $controller = new ForgotPasswordController(true);
        $result = $controller->go();
        $this->debug($result);

        $this->assertPattern('/Reset your password/', $result);
    }

    public function testOfCustomJavascript() {
        $controller = new ForgotPasswordController(true);
        $result = $controller->go();

        $this->assertPattern('/jqBootstrapValidation.js/', $result);
        $this->assertPattern('/validate-fields.js/', $result);
    }

    public function testOfControllerWithBadEmailAddress() {
        $_POST['email'] = 'im a broken email address';
        $_POST['Submit'] = "Send";

        $controller = new ForgotPasswordController(true);
        $result = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), 'Sorry, can\'t find that email address.');
    }

    public function testOfControllerWithWaitlistedUser() {
        $builder = FixtureBuilder::build('subscribers', array('id'=>2,
        'email'=>'waitlisted@example.com', 'membership_level'=>'Waitlist'));

        $_POST['email'] = 'waitlisted@example.com';
        $_POST['Submit'] = "Send";

        $controller = new ForgotPasswordController(true);
        $result = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), 'Sorry, can\'t find that email address.');
    }

    public function testOfControllerWithValidEmailAddress() {
        $config = Config::getInstance();
        $site_root_path = $config->getValue('site_root_path');
        $this->debug("site_root_path ". $site_root_path);

        $_POST['email'] = 'me@example.com';
        $_POST['Submit'] = "Send";
        $_SERVER['HTTP_HOST'] = "mytestthinkup";
        $controller = new ForgotPasswordController(true);
        $result = $controller->go();

        $this->debug($result);
        $this->assertPattern('/Check your email for a password reset link./', $result );

        $actual_forgot_email = Mailer::getLastMail();
        $this->debug($actual_forgot_email);

        $email_object = JSONDecoder::decode($actual_forgot_email);
        $this->assertEqual($email_object->subject, "Recover your ThinkUp password");
        $this->assertEqual($email_object->from_email, "help@thinkup.com");
        $this->assertEqual($email_object->from_name, "ThinkUp");

        $expected_forgot_email_pattern = '/Looks like you forgot your ThinkUp.com password./';
        $this->assertPattern($expected_forgot_email_pattern, $email_object->text);

        $expected_forgot_email_pattern = 'http:\/\/mytestthinkup'.str_replace('/', '\/', $site_root_path).
        'user\/reset.php';
        $this->debug("patternized site_root_path ". str_replace('/', '\/', $site_root_path));
        $this->assertTrue(strpos( $actual_forgot_email, $expected_forgot_email_pattern) > 0 );
    }

    public function testOfControllerWithValidEmailAddressAndSSL() {
        $config = Config::getInstance();
        $config->setValue('app_title_prefix', '');
        $site_root_path = $config->getValue('site_root_path');
        $_POST['email'] = 'me@example.com';
        $_POST['Submit'] = "Send";
        $_SERVER['HTTP_HOST'] = "mytestthinkup";
        $_SERVER['HTTPS'] = true;
        $controller = new ForgotPasswordController(true);
        $result = $controller->go();

        $this->assertTrue(strpos($result, 'Check your email for a password reset link.') > 0);

        $actual_forgot_email = Mailer::getLastMail();
        $this->debug($actual_forgot_email);

        $email_object = JSONDecoder::decode($actual_forgot_email);
        $this->assertEqual($email_object->subject, "Recover your ThinkUp password");
        $this->assertEqual($email_object->from_email, "help@thinkup.com");
        $this->assertEqual($email_object->from_name, "ThinkUp");

        $expected_forgot_email_pattern = '/Looks like you forgot your ThinkUp.com password./';
        $this->assertPattern($expected_forgot_email_pattern, $actual_forgot_email);

        $expected_forgot_email_pattern = 'https:\/\/mytestthinkup'.str_replace('/', '\/', $site_root_path).
        'user\/reset.php';
        $this->debug("patternized site_root_path ". str_replace('/', '\/', $site_root_path));
        $this->assertTrue(strpos( $actual_forgot_email, $expected_forgot_email_pattern) > 0 );
    }
}
