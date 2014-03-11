<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfSettingsController extends UpstartUnitTestCase {

    public $thinkup_username = 'testify';

    public $user_database;

    public function setUp() {
        parent::setUp();
        $this->builders = self::buildData();
        $this->user_database = Config::getInstance()->getValue('user_installation_db_prefix').
            $this->thinkup_username;
    }

    public function tearDown() {
        // Clean up
        // Destroy user database
        $q = "DROP DATABASE IF EXISTS ". $this->user_database;
        PDODAO::$PDO->exec($q);

        // Unlink username installation folder
        $config = Config::getInstance();
        $app_source_path = $config->getValue('app_source_path');
        $cmd = 'rm -rf '.$app_source_path.$this->thinkup_username;
        $cmd_result = exec($cmd, $output, $return_var);

        // Unlink username installation data folder
        $data_path = $config->getValue('data_path');
        $cmd = 'rm -rf '.$data_path.$this->thinkup_username;
        $cmd_result = exec($cmd, $output, $return_var);

        $this->builders = null;

        parent::tearDown();
    }

    protected function buildData() {
        $builders = array();
        $test_salt = 'test_salt';
        $password = TestLoginHelper::hashPassword('secretpassword', $test_salt);

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com', 'pwd'=>$password,
        'pwd_salt'=>$test_salt, 'is_activated'=>1, 'is_admin'=>1, 'thinkup_username'=>$this->thinkup_username,
        'is_installation_active'=>0));

        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_source_path = $config->getValue('app_source_path');
        $data_path = $config->getValue('data_path');

        $app_installer = new AppInstaller();
        $app_installer->install(6);

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
        $this->assertNoPattern("/Settings/", $results);
    }

    public function testLoggedIn() {
        $this->simulateLogin('me@example.com');
        $controller = new SettingsController(true);
        $results = $controller->go();

        $this->debug($results);
        $this->assertNoPattern("/Log in/", $results);
        $this->assertPattern("/Settings/", $results);
    }

    public function testLoggedInSubmittedChangesNoCSRFToken() {
        // Assert that timezone is America/New_York and frequency is daily
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database .
            '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['timezone'], 'UTC');
        $this->assertEqual($row['email_notification_frequency'], 'daily');

        $this->simulateLogin('me@example.com');

        // Set $_POST vars
        $_POST['Done'] = 'Done';
        $_POST['timezone'] = 'America/Los_Angeles';
        $controller = new SettingsController(true);
        try {
            $results = $controller->control();
            $this->fail("should throw InvalidCSRFTokenException");
        } catch(InvalidCSRFTokenException $e) {
            $this->assertIsA($e, 'InvalidCSRFTokenException');
        }
    }

    public function testLoggedInSubmittedValidChanges() {
        // Assert that timezone is UTC and frequency is daily
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database .
        '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['timezone'], 'UTC');
        $this->assertEqual($row['email_notification_frequency'], 'daily');

        $this->simulateLogin('me@example.com', false, true);

        // Set $_POST vars
        $_POST['Done'] = 'Done';
        $_POST['timezone'] = 'America/Los_Angeles';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;
        $controller = new SettingsController(true);
        $results = $controller->go();

        // Assert that timezone is new values
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database .
        '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['timezone'], 'America/Los_Angeles');
        $this->assertEqual($row['email_notification_frequency'], 'daily');

        $this->debug($results);
        $this->assertPattern("/Settings/", $results);
        $this->assertPattern("/Saved your changes/", $results);
    }

    public function testInvalidTZ() {
        // Assert that timezone is UTC and frequency is daily
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database .
        '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['timezone'], 'UTC');
        $this->assertEqual($row['email_notification_frequency'], 'daily');

        $this->simulateLogin('me@example.com', false, true);

        // Set $_POST vars
        $_POST['Done'] = 'Done';
        $_POST['timezone'] = 'Not a valid timezone';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;
        $controller = new SettingsController(true);
        $results = $controller->go();

        // Assert that timezone is NOT new value
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database .
        '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['timezone'], 'UTC');
        $this->assertEqual($row['email_notification_frequency'], 'daily');

        $this->debug($results);
        $this->assertPattern("/Settings/", $results);
        $this->assertNoPattern("/Saved your changes/", $results);
    }

    public function testInvalidFrequency() {
        // Assert that timezone is America/New_York and frequency is daily
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database .
        '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['timezone'], 'UTC');
        $this->assertEqual($row['email_notification_frequency'], 'daily');

        $this->simulateLogin('me@example.com', false, true);

        // Set $_POST vars
        $_POST['Done'] = 'Done';
        $_POST['control-notification-frequency'] = 'not a valid frequency';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;
        $controller = new SettingsController(true);
        $results = $controller->go();

        // Assert that frequency is NOT new value
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database .
        '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['timezone'], 'UTC');
        $this->assertEqual($row['email_notification_frequency'], 'daily');

        $this->debug($results);
        $this->assertPattern("/Settings/", $results);
        $this->assertNoPattern("/Saved your changes/", $results);
    }

    public function testSetPassword() {
        $this->simulateLogin('me@example.com', false, true);
        $subscriber_dao = new SubscriberMySQLDAO();
        $owner_dao = new OwnerMySQLDAO();

        // Set $_POST vars
        $_POST['Done'] = 'Done';
        $_POST['current_password'] = 'not a real password';
        $_POST['new_password1'] = 'bbbbbb';
        $_POST['new_password2'] = 'aaaaaa';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;
        $controller = new SettingsController(true);
        $results = $controller->go();
        $this->assertPattern('/current password was not correct/', $results);

        $_POST['current_password'] = 'secretpassword';
        $controller = new SettingsController(true);
        $results = $controller->go();
        $this->assertNoPattern('/current password was not correct/', $results);
        $this->assertPattern('/did not match/', $results);

        $_POST['new_password2'] = 'bbbbbb';
        $controller = new SettingsController(true);
        $results = $controller->go();
        $this->assertNoPattern('/current password was not correct/', $results);
        $this->assertNoPattern('/did not match/', $results);
        $this->assertPattern('/at least 8 characters and contain/', $results);

        $this->assertFalse($subscriber_dao->isAuthorized('me@example.com', 'Bbbbbb66'));
        ThinkUpTablesMySQLDAO::switchToInstallationDatabase($this->thinkup_username);
        $this->assertFalse($owner_dao->isOwnerAuthorized('me@example.com', 'Bbbbbb66'));
        ThinkUpTablesMySQLDAO::switchToUpstartDatabase();

        $_POST['new_password1'] = 'Bbbbbb66';
        $_POST['new_password2'] = 'Bbbbbb66';
        $controller = new SettingsController(true);
        $results = $controller->go();
        $this->assertNoPattern('/current password was not correct/', $results);
        $this->assertNoPattern('/did not match/', $results);
        $this->assertNoPattern('/at least 8 characters and contain/', $results);
        $this->assertPattern('/Saved your changes./', $results);

        $this->assertTrue($subscriber_dao->isAuthorized('me@example.com', 'Bbbbbb66'));

        ThinkUpTablesMySQLDAO::switchToInstallationDatabase($this->thinkup_username);
        $this->assertTrue($owner_dao->isOwnerAuthorized('me@example.com', 'Bbbbbb66'));
        ThinkUpTablesMySQLDAO::switchToUpstartDatabase();
    }
}
