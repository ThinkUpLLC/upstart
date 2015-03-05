<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfAppInstaller extends UpstartUnitTestCase {

    public $thinkup_username = 'testerrific';

    public $user_database;

    public function setUp() {
        parent::setUp();
        $this->user_database = Config::getInstance()->getValue('user_installation_db_prefix').
            $this->thinkup_username;
    }

    public function tearDown() {
        // Clean up
        // Destroy user installation database
        $q = "DROP DATABASE IF EXISTS ".$this->user_database.";";
        PDODAO::$PDO->exec($q);

        // Destroy archived user installation database
        $stmt = PDODAO::$PDO->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME LIKE  '
            .' "'. AppInstaller::ARCHIVED_DB_PREFIX . $this->thinkup_username . '%";');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($row['SCHEMA_NAME'])) {
            $q = "DROP DATABASE IF EXISTS `".$row["SCHEMA_NAME"]."`;";
            PDODAO::$PDO->exec($q);
        }

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
        $this->assertEqual($e->getMessage(), 'Subscriber ID 6 does not exist.');
    }

    public function testUninstallSubscriberUsernameNotSet() {
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>null, 'date_installed'=> '2014-01-15', 'timezone'=>'UTC'));
        $app_installer = new AppInstaller();
        try {
            $app_installer->uninstall(6);
        } catch (NonExistentInstallationException $e) {
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
        } catch (NonExistentInstallationException $e) {
        }
        $this->assertNotNull($e);
        $this->assertEqual($e->getMessage(), 'testerrific installation date_installed is not set.');
    }

    public function testUninstallInstallationNotActive() {
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>$this->thinkup_username, 'date_installed'=> '2014-01-15', 'timezone'=>'UTC',
        'is_installation_active'=>0));
        $app_installer = new AppInstaller();
        try {
            $app_installer->uninstall(6);
        } catch (InactiveInstallationException $e) {
        }
        $this->assertNotNull($e);
        $this->assertEqual($e->getMessage(),
            'testerrific installation is not active (is_installation_active is set to false).');
    }

    public function testUninstall() {
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>$this->thinkup_username, 'date_installed'=> null, 'timezone'=>'UTC'));

        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_source_path = $config->getValue('app_source_path');
        $data_path = $config->getValue('data_path');

        $this->debug('About to install app');
        $app_installer = new AppInstaller();
        $app_installer->install(6);
        $this->debug('App installed');

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
            .' "'.$this->user_database . '";');
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

    public function testInstallNoServiceUserAuth() {
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>$this->thinkup_username, 'date_installed'=> null, 'timezone'=>'UTC', 'network'=>null,
        'network_user_name'=>'', 'network_user_id'=>null));

        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_installer = new AppInstaller();
        $install_results = $app_installer->install(6);
        $this->debug($install_results);

        // Assert Upstart user pass and salt match ThinkUp owner pass and salt
        $stmt = PDODAO::$PDO->query('SELECT * FROM '. $this->user_database .'.tu_owners');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assert admin owner details are set
        $this->assertEqual($rows[0]['email'],'admin@thinkup.com');
        $this->assertEqual($rows[0]['is_admin'], 1);
        $this->assertEqual($rows[0]['api_key_private'], $config->getValue('admin_session_api_key'));

        // Assert user owner details are set
        $thinkup_owner_pass = $rows[1]['pwd'];
        $thinkup_owner_pass_salt = $rows[1]['pwd_salt'];
        $thinkup_owner_membership_level = $rows[1]['membership_level'];
        $thinkup_owner_timezone = $rows[1]['timezone'];

        // Assert owner details matches subscriber details
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail('me@example.com');

        $this->assertEqual($subscriber->pwd, $thinkup_owner_pass);
        $this->assertEqual($subscriber->pwd_salt, $thinkup_owner_pass_salt);
        $this->assertEqual($subscriber->is_installation_active, 1);
        $this->assertNotNull($subscriber->date_installed);
        $this->assertEqual($subscriber->timezone, $thinkup_owner_timezone);
        $this->assertEqual($subscriber->membership_level, $thinkup_owner_membership_level);

        // Assert Upstart api_key_private = ThinkUp's owner api_key_private
        $thinkup_owner_api_key_private = $rows[1]['api_key_private'];
        $this->assertEqual($subscriber->api_key_private, $thinkup_owner_api_key_private);

        $rows = null;

        // Assert application and plugin options are set
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '.$this->user_database . '.tu_options o');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Assert ThinkUp application database_version option is correct/up-to-date
        $this->assertEqual($rows[0]['namespace'], 'application_options');
        $this->assertEqual($rows[0]['option_name'], 'database_version');
        $this->assertEqual($rows[0]['option_value'], '2.0-beta.10');

        // Assert ThinkUp application option server name is correct
        $this->assertEqual($rows[1]['namespace'], 'application_options');
        $this->assertEqual($rows[1]['option_name'], 'server_name');
        $this->assertEqual($rows[1]['option_value'], 'www.example.com');

        // Assert Twitter API keys are set
        $this->assertEqual($rows[2]['namespace'], 'plugin_options-1');
        $this->assertEqual($rows[2]['option_name'], 'oauth_consumer_key');
        $this->assertEqual($rows[2]['option_value'], $config->getValue('oauth_consumer_key'));

        $this->assertEqual($rows[3]['namespace'], 'plugin_options-1');
        $this->assertEqual($rows[3]['option_name'], 'oauth_consumer_secret');
        $this->assertEqual($rows[3]['option_value'], $config->getValue('oauth_consumer_secret'));

        // Facebook options
        $this->assertEqual($rows[4]['namespace'], 'plugin_options-2');
        $this->assertEqual($rows[4]['option_name'], 'facebook_app_id');
        $this->assertEqual($rows[4]['option_value'], $config->getValue('facebook_app_id'));

        $this->assertEqual($rows[5]['namespace'], 'plugin_options-2');
        $this->assertEqual($rows[5]['option_name'], 'facebook_api_secret');
        $this->assertEqual($rows[5]['option_value'], $config->getValue('facebook_api_secret'));

        $this->assertEqual($rows[6]['namespace'], 'plugin_options-2');
        $this->assertEqual($rows[6]['option_name'], 'max_crawl_time');
        $this->assertEqual($rows[6]['option_value'], $config->getValue('facebook_max_crawl_time'));

        // Mandrill template option
        $this->assertEqual($rows[7]['namespace'], 'plugin_options-3');
        $this->assertEqual($rows[7]['option_name'], 'links_to_expand');
        $this->assertEqual($rows[7]['option_value'], $config->getValue('expandurls_links_to_expand_per_crawl'));

        // Expand URLs option is set
        $this->assertEqual($rows[8]['namespace'], 'plugin_options-4');
        $this->assertEqual($rows[8]['option_name'], 'mandrill_template');
        $this->assertEqual($rows[8]['option_value'], $config->getValue('mandrill_notifications_template'));

        // Assert no owner instances are set because there's no service user connection
        $stmt = PDODAO::$PDO->query('SELECT i.* FROM '. $this->user_database . '.tu_instances i INNER JOIN '.
            $this->user_database . '.tu_owner_instances oi ON i.id = oi.instance_id INNER JOIN '.
            $this->user_database . '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($row);
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

        // Assert Upstart user pass and salt match ThinkUp owner pass and salt
        $stmt = PDODAO::$PDO->query('SELECT * FROM '.$this->user_database . '.tu_owners');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assert admin owner details are set
        $this->assertEqual($rows[0]['email'],'admin@thinkup.com');
        $this->assertEqual($rows[0]['is_admin'], 1);
        $this->assertEqual($rows[0]['api_key_private'], $config->getValue('admin_session_api_key'));
        $this->assertEqual($rows[0]['is_free_trial'], 0); //Admin is not on free trial

        // Assert user owner details are set
        $thinkup_owner_pass = $rows[1]['pwd'];
        $thinkup_owner_pass_salt = $rows[1]['pwd_salt'];
        $thinkup_owner_membership_level = $rows[1]['membership_level'];
        $thinkup_owner_timezone = $rows[1]['timezone'];

        // Assert owner details matches subscriber details
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail('me@example.com');

        $this->assertEqual($subscriber->pwd, $thinkup_owner_pass);
        $this->assertEqual($subscriber->pwd_salt, $thinkup_owner_pass_salt);
        $this->assertEqual($subscriber->is_installation_active, 1);
        $this->assertNotNull($subscriber->date_installed);
        $this->assertEqual($subscriber->timezone, $thinkup_owner_timezone);
        $this->assertEqual($subscriber->membership_level, $thinkup_owner_membership_level);
        $this->assertEqual($rows[1]['is_free_trial'], 1); //user is on free trial

        // Assert Upstart api_key_private = ThinkUp's owner api_key_private
        $thinkup_owner_api_key_private = $rows[1]['api_key_private'];
        $this->assertEqual($subscriber->api_key_private, $thinkup_owner_api_key_private);

        $rows = null;

        // Assert application and plugin options are set
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '.$this->user_database . '.tu_options o');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Assert ThinkUp application database_version option is correct/up-to-date
        $this->assertEqual($rows[0]['namespace'], 'application_options');
        $this->assertEqual($rows[0]['option_name'], 'database_version');
        $this->assertEqual($rows[0]['option_value'], '2.0-beta.10');

        // Assert ThinkUp application option server name is correct
        $this->assertEqual($rows[1]['namespace'], 'application_options');
        $this->assertEqual($rows[1]['option_name'], 'server_name');
        $this->assertEqual($rows[1]['option_value'], 'www.example.com');

        // Assert Twitter API keys are set
        $this->assertEqual($rows[2]['namespace'], 'plugin_options-1');
        $this->assertEqual($rows[2]['option_name'], 'oauth_consumer_key');
        $this->assertEqual($rows[2]['option_value'], $config->getValue('oauth_consumer_key'));

        $this->assertEqual($rows[3]['namespace'], 'plugin_options-1');
        $this->assertEqual($rows[3]['option_name'], 'oauth_consumer_secret');
        $this->assertEqual($rows[3]['option_value'], $config->getValue('oauth_consumer_secret'));

        // Facebook options
        $this->assertEqual($rows[4]['namespace'], 'plugin_options-2');
        $this->assertEqual($rows[4]['option_name'], 'facebook_app_id');
        $this->assertEqual($rows[4]['option_value'], $config->getValue('facebook_app_id'));

        $this->assertEqual($rows[5]['namespace'], 'plugin_options-2');
        $this->assertEqual($rows[5]['option_name'], 'facebook_api_secret');
        $this->assertEqual($rows[5]['option_value'], $config->getValue('facebook_api_secret'));

        $this->assertEqual($rows[6]['namespace'], 'plugin_options-2');
        $this->assertEqual($rows[6]['option_name'], 'max_crawl_time');
        $this->assertEqual($rows[6]['option_value'], $config->getValue('facebook_max_crawl_time'));

        // Mandrill template option
        $this->assertEqual($rows[7]['namespace'], 'plugin_options-3');
        $this->assertEqual($rows[7]['option_name'], 'links_to_expand');
        $this->assertEqual($rows[7]['option_value'], $config->getValue('expandurls_links_to_expand_per_crawl'));

        // Expand URLs option is set
        $this->assertEqual($rows[8]['namespace'], 'plugin_options-4');
        $this->assertEqual($rows[8]['option_name'], 'mandrill_template');
        $this->assertEqual($rows[8]['option_value'], $config->getValue('mandrill_notifications_template'));

        $stmt = PDODAO::$PDO->query('SELECT i.* FROM '. $this->user_database .
            '.tu_instances i INNER JOIN '. $this->user_database .
            '.tu_owner_instances oi ON i.id = oi.instance_id INNER JOIN '.
             $this->user_database . '.tu_owners o WHERE o.email = "me@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $instance_network = $row['network'];
        $instance_network_username = $row['network_username'];
        $instance_network_user_id = $row['network_user_id'];
        $instance_network_viewer_id = $row['network_viewer_id'];

        $this->assertEqual($instance_network, 'twitter');
        $this->assertEqual($instance_network_username, 'TheFakeTweeter');
        $this->assertEqual($instance_network_user_id, 'abcdefg101');
        $this->assertEqual($instance_network_viewer_id, 'abcdefg101');

        //All the tables except a few that get created in post-installation migrations should be InnoDB
        //Other tables are less-accessed, so it's okay to keep MyISAM
        $non_innodb_tables = array('tu_completed_migrations', 'tu_instances_facebook', 'tu_cookies', 'tu_sessions',
            'tu_user_versions');
        $stmt = PDODAO::$PDO->query('SHOW TABLE STATUS FROM '. $this->user_database);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            if (!in_array($row['Name'], $non_innodb_tables)) {
                $this->assertEqual($row['Engine'], 'InnoDB');
            }
        }

        //Try installing again
        try {
            $install_results = $app_installer->install(6);
        } catch (Exception $e) {
        }
        $this->assertNotNull($e);
        $this->assertEqual($e->getMessage(), "Installation for testerrific already exists.");
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

        // Assert Upstart user pass and salt match ThinkUp owner pass and salt
        $stmt = PDODAO::$PDO->query('SELECT * FROM '. $this->user_database .'.tu_owners');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assert admin owner details are set
        $this->assertEqual($rows[0]['email'],'admin@thinkup.com');
        $this->assertEqual($rows[0]['is_admin'], 1);
        $this->assertEqual($rows[0]['api_key_private'], $config->getValue('admin_session_api_key'));
        $this->assertEqual($rows[0]['is_free_trial'], 0); //Admin is not on free trial

        // Assert user owner details are set
        $thinkup_owner_pass = $rows[1]['pwd'];
        $thinkup_owner_pass_salt = $rows[1]['pwd_salt'];
        $thinkup_owner_membership_level = $rows[1]['membership_level'];
        $thinkup_owner_timezone = $rows[1]['timezone'];

        // Assert owner details matches subscriber details
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail('me@example.com');

        $this->assertEqual($subscriber->pwd, $thinkup_owner_pass);
        $this->assertEqual($subscriber->pwd_salt, $thinkup_owner_pass_salt);
        $this->assertEqual($subscriber->is_installation_active, 1);
        $this->assertNotNull($subscriber->date_installed);
        $this->assertEqual($subscriber->timezone, $thinkup_owner_timezone);
        $this->assertEqual($subscriber->membership_level, $thinkup_owner_membership_level);
        $this->assertEqual($rows[1]['is_free_trial'], 1); //user is on free trial

        // Assert Upstart api_key_private = ThinkUp's owner api_key_private
        $thinkup_owner_api_key_private = $rows[1]['api_key_private'];
        $this->assertEqual($subscriber->api_key_private, $thinkup_owner_api_key_private);

        $rows = null;

        // Assert application and plugin options are set
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database .'.tu_options o');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Assert ThinkUp application database_version option is correct/up-to-date
        $this->assertEqual($rows[0]['namespace'], 'application_options');
        $this->assertEqual($rows[0]['option_name'], 'database_version');
        $this->assertEqual($rows[0]['option_value'], '2.0-beta.10');

        // Assert ThinkUp application option server name is correct
        $this->assertEqual($rows[1]['namespace'], 'application_options');
        $this->assertEqual($rows[1]['option_name'], 'server_name');
        $this->assertEqual($rows[1]['option_value'], 'www.example.com');

        // Assert Twitter API keys are set
        $this->assertEqual($rows[2]['namespace'], 'plugin_options-1');
        $this->assertEqual($rows[2]['option_name'], 'oauth_consumer_key');
        $this->assertEqual($rows[2]['option_value'], $config->getValue('oauth_consumer_key'));

        $this->assertEqual($rows[3]['namespace'], 'plugin_options-1');
        $this->assertEqual($rows[3]['option_name'], 'oauth_consumer_secret');
        $this->assertEqual($rows[3]['option_value'], $config->getValue('oauth_consumer_secret'));

        // Facebook options
        $this->assertEqual($rows[4]['namespace'], 'plugin_options-2');
        $this->assertEqual($rows[4]['option_name'], 'facebook_app_id');
        $this->assertEqual($rows[4]['option_value'], $config->getValue('facebook_app_id'));

        $this->assertEqual($rows[5]['namespace'], 'plugin_options-2');
        $this->assertEqual($rows[5]['option_name'], 'facebook_api_secret');
        $this->assertEqual($rows[5]['option_value'], $config->getValue('facebook_api_secret'));

        $this->assertEqual($rows[6]['namespace'], 'plugin_options-2');
        $this->assertEqual($rows[6]['option_name'], 'max_crawl_time');
        $this->assertEqual($rows[6]['option_value'], $config->getValue('facebook_max_crawl_time'));

        // Mandrill template option
        $this->assertEqual($rows[7]['namespace'], 'plugin_options-3');
        $this->assertEqual($rows[7]['option_name'], 'links_to_expand');
        $this->assertEqual($rows[7]['option_value'], $config->getValue('expandurls_links_to_expand_per_crawl'));

        // Expand URLs option is set
        $this->assertEqual($rows[8]['namespace'], 'plugin_options-4');
        $this->assertEqual($rows[8]['option_name'], 'mandrill_template');
        $this->assertEqual($rows[8]['option_value'], $config->getValue('mandrill_notifications_template'));

        $stmt = PDODAO::$PDO->query('SELECT i.* FROM '.$this->user_database .'.tu_instances i INNER JOIN '.
            $this->user_database . '.tu_owner_instances oi ON i.id = oi.instance_id INNER JOIN '.
            $this->user_database . '.tu_owners o WHERE o.email = "me@example.com"');
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

    public function testDropDatabase() {
         $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>$this->thinkup_username, 'date_installed'=> null, 'timezone'=>'UTC'));

        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_source_path = $config->getValue('app_source_path');
        $data_path = $config->getValue('data_path');

        $this->debug('About to install app');
        $app_installer = new AppInstaller();
        $app_installer->install(6);
        $this->debug('App installed');

        $app_installer = new AppInstaller();
        $app_installer->dropDatabase($this->thinkup_username);

         // Assert user database doesn't exist
        $stmt = PDODAO::$PDO->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  '
            .' "'.$this->user_database . '";');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->debug(Utils::varDumpToString($row));
        $this->assertFalse($row);

         // Assert archived database does exist
        $stmt = PDODAO::$PDO->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME LIKE  '
            .' "'. AppInstaller::ARCHIVED_DB_PREFIX . $this->thinkup_username . '%";');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->debug(Utils::varDumpToString($row));
        $this->assertTrue($row);
   }
}