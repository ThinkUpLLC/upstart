<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfThinkUpTablesMySQLDAO extends UpstartUnitTestCase {

    var $thinkup_username = 'testify';

    public function setUp() {
        parent::setUp();
        $this->builders = self::buildData();
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
        $dao = new ThinkUpTablesMySQLDAO();
        $this->assertIsA($dao, 'ThinkUpTablesMySQLDAO');
    }

    public function testOfUpdateOwnerEmailSuccess() {
        // Assert that email is me@example.com
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM thinkupstart_'. $this->thinkup_username .
        '.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['email'], 'me@example.com');

        $dao = new ThinkUpTablesMySQLDAO();
        $dao->switchToInstallationDatabase( $this->thinkup_username );
        $result = $dao->updateOwnerEmail('me@example.com', 'newme@example.com');
        $this->assertTrue($result);

        // Assert that email is me@example.com
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM thinkupstart_'. $this->thinkup_username .
        '.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['email'], 'newme@example.com');
        $dao->switchToUpstartDatabase();
    }

    public function testOfUpdateOwnerEmailFailure() {
        // Assert that email is me@example.com
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM thinkupstart_'. $this->thinkup_username .
        '.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['email'], 'me@example.com');

        $dao = new ThinkUpTablesMySQLDAO();
        $dao->switchToInstallationDatabase( $this->thinkup_username );
        $result = $dao->updateOwnerEmail('me@example.com', 'me@example.com');
        $this->assertFalse($result);

        // Assert that email is me@example.com
        $stmt = PDODAO::$PDO->query('SELECT o.* FROM thinkupstart_'. $this->thinkup_username .
        '.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['email'], 'me@example.com');
        $dao->switchToUpstartDatabase();
    }
}