<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfThinkUpTablesMySQLDAO extends UpstartUnitTestCase {

    public $thinkup_username = 'testify';

    public $user_database;

    public function setUp() {
        parent::setUp();
        $this->user_database = Config::getInstance()->getValue('user_installation_db_prefix').
            $this->thinkup_username;
        $this->builders = self::buildData();
    }

    public function tearDown() {
        // Clean up
        // Destroy user database
        $q = "DROP DATABASE IF EXISTS ".$this->user_database.";";
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
        'is_installation_active'=>0, 'is_free_trial'=>1));

        $config = Config::getInstance();
        $config->setValue('user_installation_url', 'http://www.example.com/thinkup/{user}/');
        $app_source_path = $config->getValue('app_source_path');
        $data_path = $config->getValue('data_path');

        $app_installer = new AppInstaller();
        $results = $app_installer->install(6);
        $this->debug($results);

        return $builders;
    }

    public function testConstructor() {
        $dao = new ThinkUpTablesMySQLDAO($this->thinkup_username);
        $this->assertIsA($dao, 'ThinkUpTablesMySQLDAO');
    }

    public function testOfUpdateOwnerEmailSuccess() {
        $this->debug(Utils::varDumpToString(ThinkUpPDODAO::$PDO));
        // Assert that email is me@example.com
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database. '.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['email'], 'me@example.com');

        $this->debug('About to update owner email');
        $dao = new ThinkUpTablesMySQLDAO($this->thinkup_username);
        $result = $dao->updateOwnerEmail('me@example.com', 'newme@example.com');
        $this->assertTrue($result);

        // Assert that email is me@example.com
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database. '.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['email'], 'newme@example.com');
    }

    public function testOfUpdateOwnerEmailFailure() {
        // Assert that email is me@example.com
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database . '.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['email'], 'me@example.com');

        $dao = new ThinkUpTablesMySQLDAO($this->thinkup_username);
        $result = $dao->updateOwnerEmail('me@example.com', 'me@example.com');
        $this->assertFalse($result);

        // Assert that email is me@example.com
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database .'.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['email'], 'me@example.com');
    }

    public function testOfEndFreeTrial() {
        $this->debug(Utils::varDumpToString(ThinkUpPDODAO::$PDO));
        // Assert that free trial is on
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database. '.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['is_free_trial'], 1);

        $this->debug('About to end free trial');
        $dao = new ThinkUpTablesMySQLDAO($this->thinkup_username);
        $result = $dao->endFreeTrial('me@example.com');
        $this->assertTrue($result);

        // Assert that free trial is off
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database. '.tu_owners o WHERE o.id = 2');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['is_free_trial'], 0);

        //Test failure to update because the free trial has already ended
        $result = $dao->endFreeTrial('me@example.com');
        $this->assertFalse($result);
    }

    public function testCreateOwner() {
        $dao = new ThinkUpTablesMySQLDAO($this->thinkup_username);
        $result = $dao->createOwner('newowner@example.com', 'hash', 'salt', 'Member', 'UTC', false, null, true);
        $this->assertEqual($result[0], 3); // new insert ID
        $this->assertNotNull($result[1]); // API key

        // Assert that email is me@example.com
        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM '. $this->user_database. '.tu_owners o WHERE o.id = 3');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['is_free_trial'], 1);
    }
}