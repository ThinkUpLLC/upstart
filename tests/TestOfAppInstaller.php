<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfAppInstaller extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
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

    public function testInstall() {
        $thinkup_username = 'testeriffic';
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com',
        'thinkup_username'=>$thinkup_username, 'date_installed'=> null));

        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_installer = new AppInstaller();
        $app_installer->install(6);
        // @TODO add assertions
        // Assert Upstart session_api_token = ThinkUp's owner api_key_private
        // Assert ThinkUp application option server name is correct
        // Assert ThinkUp database_version option is correct/up-to-date

        // Clean up
        // Destroy thinkupstart_username database
        $q = "DROP DATABASE thinkupstart_$thinkup_username;";
        PDODAO::$PDO->exec($q);

        // Unlink username installation folder
        $app_source_path = $config->getValue('app_source_path');
        $cmd = 'rm -rf '.$app_source_path.$thinkup_username;
        $cmd_result = exec($cmd, $output, $return_var);

        // Unlink username installation data folder
        $data_path = $config->getValue('data_path');
        $cmd = 'rm -rf '.$data_path.$thinkup_username;
        $cmd_result = exec($cmd, $output, $return_var);
    }
}