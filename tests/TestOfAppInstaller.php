<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfAppInstaller extends UpstartUnitTestCase {

    var $thinkup_username = 'testerrific';

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        // Clean up
        // Destroy thinkupstart_username database
        $q = "DROP DATABASE IF EXISTS thinkupstart_$this->thinkup_username;";
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

        parent::tearDown();
    }

    public function testGetInstallationServerName() {
        // Try subdirectory URL scheme
        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_installer = new AppInstaller();
        $server_name = $app_installer->getInstallationServerName('ginatrapani');
        $this->assertEqual($server_name, 'www.example.com');

        // Try subdomain URL scheme
        $app_installer = null;
        $config->setValue('user_installation_url', 'http://{user}.example.com/');
        $app_installer = new AppInstaller();
        $server_name = $app_installer->getInstallationServerName('ginatrapani');
        $this->assertEqual($server_name, 'ginatrapani.example.com');
    }

    public function testUninstallSubscriberNotSpecified() {
        $app_installer = new AppInstaller();
        try {
            $app_installer->uninstall(null);
        } catch (Exception $e) {
        }
        $this->assertNotNull($e);
        $this->assertEqual($e->getMessage(), 'No subscriber specified.');
    }

    public function testUninstallSubscriberDoesntExist() {
        $app_installer = new AppInstaller();
        try {
            $app_installer->uninstall(6);
        } catch (Exception $e) {
        }
        $this->assertNotNull($e);
        $this->assertEqual($e->getMessage(), 'Subscriber does not exist.');
    }

    public function testUninstallSubscriberUsernameNotSet() {
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>null, 'date_installed'=> '2014-01-15', 'timezone'=>'UTC'));
        $app_installer = new AppInstaller();
        try {
            $app_installer->uninstall(6);
        } catch (Exception $e) {
        }
        $this->assertNotNull($e);
        $this->assertEqual($e->getMessage(), 'ThinkUp username is not set.');
    }

    public function testUninstallInstallationDoesntExist() {
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>$this->thinkup_username, 'date_installed'=> null, 'timezone'=>'UTC'));
        $app_installer = new AppInstaller();
        try {
            $app_installer->uninstall(6);
        } catch (Exception $e) {
        }
        $this->assertNotNull($e);
        $this->assertEqual($e->getMessage(), 'Installation does not exist.');
    }

    public function testUninstall() {
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>$this->thinkup_username, 'date_installed'=> null, 'timezone'=>'UTC'));

        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_source_path = $config->getValue('app_source_path');
        $data_path = $config->getValue('data_path');

        $app_installer = new AppInstaller();
        $app_installer->install(6);

        // Assert installation completed
        $this->assertTrue(file_exists($app_source_path.$this->thinkup_username ) );
        $this->assertTrue(file_exists($data_path.$this->thinkup_username ) );

        // Unininstall
        $uninstall_results = $app_installer->uninstall(6);
        $this->debug($uninstall_results);

        // Assert symlink doesn't exist
        $this->debug($app_source_path.$this->thinkup_username );
        $this->assertFalse(file_exists($app_source_path.$this->thinkup_username ) );

        // Assert data directory doesn't exist
        $this->assertFalse(file_exists($data_path.$this->thinkup_username ) );

        // Assert user database doesn't exist
        $stmt = PDODAO::$PDO->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  '
            .' "thinkupstart_' . $this->thinkup_username . '";');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->debug(Utils::varDumpToString($row));
        $this->assertFalse($row);

        // Assert subscriber record got updated
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail('me@example.com');
        $this->assertNull($subscriber->date_installed);
        $this->assertNull($subscriber->last_dispatched);
        $this->assertNull($subscriber->commit_hash);
        $this->assertFalse($subscriber->is_installation_active);
    }

    public function testInstallTwitterAuth() {
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>$this->thinkup_username, 'date_installed'=> null, 'timezone'=>'UTC', 'network'=>'twitter',
        'network_user_name'=>'TheFakeTweeter', 'network_user_id'=>'abcdefg101'));

        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_installer = new AppInstaller();
        $install_results = $app_installer->install(6);
        $this->debug($install_results);
        // @TODO add assertions

        // Assert Upstart user pass and salt match ThinkUp owner pass and salt
        $stmt = PDODAO::$PDO->query('SELECT pwd, pwd_salt, api_key_private, timezone, membership_level '.
            'FROM thinkupstart_'. $this->thinkup_username . '.tu_owners WHERE email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $thinkup_owner_pass = $row['pwd'];
        $thinkup_owner_pass_salt = $row['pwd_salt'];
        $thinkup_owner_membership_level = $row['membership_level'];
        $thinkup_owner_timezone = $row['timezone'];

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail('me@example.com');

        $this->assertEqual($subscriber->pwd, $thinkup_owner_pass);
        $this->assertEqual($subscriber->pwd_salt, $thinkup_owner_pass_salt);
        $this->assertEqual($subscriber->is_installation_active, 1);
        $this->assertNotNull($subscriber->date_installed);
        $this->assertEqual($subscriber->timezone, $thinkup_owner_timezone);
        $this->assertEqual($subscriber->membership_level, $thinkup_owner_membership_level);

        // Assert Upstart api_key_private = ThinkUp's owner api_key_private
        $thinkup_owner_api_key_private = $row['api_key_private'];
        $this->assertEqual($subscriber->api_key_private, $thinkup_owner_api_key_private);

        // Assert ThinkUp application option server name is correct
        // Assert ThinkUp plugin option Facebook & Twitter API keys and Mandrill template is set
        // Assert ThinkUp database_version option is correct/up-to-date
        // Assert admin private API key is set

        $stmt = PDODAO::$PDO->query('SELECT i.* FROM thinkupstart_'. $this->thinkup_username .
            '.tu_instances i INNER JOIN thinkupstart_'. $this->thinkup_username . '.tu_owner_instances oi '.
            'ON i.id = oi.instance_id INNER JOIN thinkupstart_'. $this->thinkup_username . '.tu_owners o '.
            'WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $instance_network = $row['network'];
        $instance_network_username = $row['network_username'];
        $instance_network_user_id = $row['network_user_id'];
        $instance_network_viewer_id = $row['network_viewer_id'];

        $this->assertEqual($instance_network, 'twitter');
        $this->assertEqual($instance_network_username, 'TheFakeTweeter');
        $this->assertEqual($instance_network_user_id, 'abcdefg101');
        $this->assertEqual($instance_network_viewer_id, 'abcdefg101');
    }

    public function testInstallFacebookAuth() {
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>$this->thinkup_username, 'date_installed'=> null, 'timezone'=>'UTC', 'network'=>'facebook',
        'network_username'=>'TheBachelorette', 'full_name'=>'Trista Sutter', 'network_user_id'=>'abcdefg101'));

        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_installer = new AppInstaller();
        $install_results = $app_installer->install(6);
        $this->debug($install_results);
        // @TODO add assertions

        // Assert Upstart user pass and salt match ThinkUp owner pass and salt
        $stmt = PDODAO::$PDO->query('SELECT pwd, pwd_salt, api_key_private, timezone, membership_level '.
            'FROM thinkupstart_'. $this->thinkup_username . '.tu_owners WHERE email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $thinkup_owner_pass = $row['pwd'];
        $thinkup_owner_pass_salt = $row['pwd_salt'];
        $thinkup_owner_membership_level = $row['membership_level'];
        $thinkup_owner_timezone = $row['timezone'];

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail('me@example.com');

        $this->assertEqual($subscriber->pwd, $thinkup_owner_pass);
        $this->assertEqual($subscriber->pwd_salt, $thinkup_owner_pass_salt);
        $this->assertEqual($subscriber->is_installation_active, 1);
        $this->assertNotNull($subscriber->date_installed);
        $this->assertEqual($subscriber->timezone, $thinkup_owner_timezone);
        $this->assertEqual($subscriber->membership_level, $thinkup_owner_membership_level);

        // Assert Upstart api_key_private = ThinkUp's owner api_key_private
        $thinkup_owner_api_key_private = $row['api_key_private'];
        $this->assertEqual($subscriber->api_key_private, $thinkup_owner_api_key_private);

        // Assert ThinkUp application option server name is correct
        // Assert ThinkUp plugin option Facebook & Twitter API keys and Mandrill template is set
        // Assert ThinkUp database_version option is correct/up-to-date
        // Assert admin private API key is set

        $stmt = PDODAO::$PDO->query('SELECT i.* FROM thinkupstart_'. $this->thinkup_username .
            '.tu_instances i INNER JOIN thinkupstart_'. $this->thinkup_username . '.tu_owner_instances oi '.
            'ON i.id = oi.instance_id INNER JOIN thinkupstart_'. $this->thinkup_username . '.tu_owners o '.
            'WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $instance_network = $row['network'];
        $instance_network_username = $row['network_username'];
        $instance_network_user_id = $row['network_user_id'];
        $instance_network_viewer_id = $row['network_viewer_id'];

        $this->assertEqual($instance_network, 'facebook');
        $this->assertEqual($instance_network_username, 'Trista Sutter');
        $this->assertEqual($instance_network_user_id, 'abcdefg101');
        $this->assertEqual($instance_network_viewer_id, 'abcdefg101');
    }
}