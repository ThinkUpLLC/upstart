<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfResetPasswordController extends UpstartUnitTestCase {
    protected $subscriber;
    protected $token;
    protected $subscriber_salt;
    protected $token_salt;

    public function setUp() {
        parent::setUp();
        $this->builder = self::buildData();
        $config = Config::getInstance();
        $config->setValue('debug', true);
    }

    protected function buildData() {
        $builders = array();

        $saltedpass = TestLoginHelper::hashPassword('oldpassword', 'testsalt');

        $hashed_pass = TestLoginHelper::hashPassword("oldpassword", 'testsalt');
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'full_name'=>'ThinkUp J. User',
        'email'=>'me@example.com', 'pwd'=>$hashed_pass, 'pwd_salt'=>'defaultsalt',
        'activation_code'=>'8888', 'is_activated'=>1));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>2, 'full_name'=>'Salted User',
        'email'=>'salt@example.com', 'pwd'=>$saltedpass, 'pwd_salt'=>'defaultsalt',
        'activation_code'=>'8888', 'is_activated'=>1));
        $dao = new SubscriberMySQLDAO();
        $this->subscriber = $dao->getByEmail('me@example.com');
        $this->token = $this->subscriber->setPasswordRecoveryToken();

        $this->subscriber_salt = $dao->getByEmail('salt@example.com');
        $this->token_salt = $this->subscriber_salt->setPasswordRecoveryToken();
        return $builders;
    }

    public function tearDown() {
        $this->builder = null;
        parent::tearDown();
    }

    public function testOfCustomJavascript() {
        $controller = new ForgotPasswordController(true);
        $result = $controller->go();

        $this->assertPattern('/jqBootstrapValidation.js/', $result);
        $this->assertPattern('/validate-fields.js/', $result);
    }

    public function testOfControllerNoToken() {
        unset($_GET['token']);

        $controller = new ResetPasswordController(true);
        $result = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), 'You have reached this page in error.');
    }

    public function testOfControllerExpiredToken() {
        $expired_time = strtotime('-2 days');
        $q = <<<SQL
UPDATE subscribers
SET password_token = '{$this->token}_{$expired_time}'
WHERE id = 1;
SQL;
        $this->testdb_helper->runSQL($q);

        $_GET['token'] = $this->token;
        $controller = new ResetPasswordController(true);
        $result = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), 'Your token is expired.');
    }

    public function testOfControllerGoodToken() {
        $time = strtotime('-1 hour');
        $q = <<<SQL
UPDATE subscribers
SET password_token = '{$this->token}_{$time}'
WHERE id = 1;
SQL;
        $this->testdb_helper->runSQL($q);

        $_GET['token'] = $this->token;
        $controller = new ResetPasswordController(true);
        $result = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertFalse($v_mgr->getTemplateDataItem('error_msg'));
        $this->assertFalse($v_mgr->getTemplateDataItem('success_msg'));
    }

    public function testOfControllerGoodTokenMismatchedPassword() {
        $time = strtotime('-1 hour');
        $q = <<<SQL
UPDATE subscribers
SET password_token = '{$this->token}_{$time}'
WHERE id = 1;
SQL;
        $this->testdb_helper->runSQL($q);

        $_POST['password'] = 'not';
        $_POST['password_confirm'] = 'the same';
        $_GET['token'] = $this->token;
        $controller = new ResetPasswordController(true);
        $result = $controller->go();

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), "The passwords must match.");
    }

    public function testOfControllerGoodTokenInvalidPassword() {
        $time = strtotime('-1 hour');
        $q = <<<SQL
UPDATE subscribers
SET password_token = '{$this->token}_{$time}'
WHERE id = 1;
SQL;
        $this->testdb_helper->runSQL($q);

        $_GET['token'] = $this->token;
        $_POST['password'] = 'the same';
        $_POST['password_confirm'] = 'the same';
        $controller = new ResetPasswordController(true);
        $result = $controller->go();
        $this->debug($result);

        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'),
            "Your password must be at least 8 characters, contain both numbers &amp; letters, and omit ".
            "special characters.");
    }

    public function testOfControllerGoodTokenMatchedNewPasswordWithNoUniqueSalt() {
        $dao = new SubscriberMySQLDAO();
        $dao->setAccountStatus("me@example.com", "Deactivated account");

        $time = strtotime('-1 hour');
        $q = <<<SQL
UPDATE subscribers
SET password_token = '{$this->token}_{$time}'
WHERE id = 1;
SQL;
        $this->testdb_helper->runSQL($q);

        $_POST['password'] = 'thesamepass21';
        $_POST['password_confirm'] = 'thesamepass21';
        $_GET['token'] = $this->token;
        $controller = new ResetPasswordController(true);
        $result = $controller->go();

        $owner = $dao->getByEmail('me@example.com');
        $this->assertEqual($owner->account_status, '');

        // Check a new unique salt got generated
        $sql = "select pwd_salt from subscribers where email = 'me@example.com'";
        $stmt = PDODAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotEqual($data['pwd_salt'], 'defaultsalt');
    }

    public function testOfControllerGoodTokenMatchedNewPasswordWithUniqueSalt() {
        $dao = new SubscriberMySQLDAO();
        $dao->setAccountStatus("salt@example.com", "Deactivated account");

        $time = strtotime('-1 hour');
        $q = <<<SQL
UPDATE subscribers
SET password_token = '{$this->token_salt}_{$time}'
WHERE id = 2;
SQL;
        $this->testdb_helper->runSQL($q);

        $_POST['password'] = 'thesamepass21';
        $_POST['password_confirm'] = 'thesamepass21';
        $_GET['token'] = $this->token_salt;
        $controller = new ResetPasswordController(true);
        $result = $controller->go();

        //assert account status is not deactivated
        $owner = $dao->getByEmail('salt@example.com');
        $this->assertEqual($owner->account_status, '');
    }

    public function testOwnerHasCleanStateAfterSuccessfulPasswordReset() {
        $builder = FixtureBuilder::build('subscribers', array('id'=>3, 'full_name'=>'Zaphod Beeblebrox',
        'email'=>'zaphod@hog.com', 'is_activated'=>0, 'failed_logins'=>10,
        'account_status'=>'Deactivated account'));

        $dao = new SubscriberMySQLDAO();
        $owner = $dao->getByEmail('zaphod@hog.com');
        $token = $owner->setPasswordRecoveryToken();

        $_POST['password'] = 'trillian1010';
        $_POST['password_confirm'] = 'trillian1010';
        $_GET['token'] = $token;

        $controller = new ResetPasswordController(true);
        $result = $controller->go();

        // Lack of error_msg in ResetPasswordController's view template indicates success.
        $v_mgr = $controller->getViewManager();
        $this->assertFalse($v_mgr->getTemplateDataItem('error_msg'));

        $owner = $dao->getByEmail('zaphod@hog.com');
        $this->assertTrue($owner->is_activated);
        $this->assertEqual($owner->account_status, '');
        $this->assertEqual($owner->password_token, '');
        $this->assertEqual($owner->failed_logins, 0);

        // Trying to use the same password reset token
        $controller = new ResetPasswordController(true);
        $result = $controller->go();

        // Error message should appear
        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('error_msg'), 'You have reached this page in error.');
    }
}
