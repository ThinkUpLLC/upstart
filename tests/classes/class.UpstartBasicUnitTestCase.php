<?php
require_once ROOT_PATH.'webapp/extlibs/isosceles/libs/class.Loader.php';

class UpstartBasicUnitTestCase extends UnitTestCase {
    /**
     * Test CSRF Token
     */
    const CSRF_TOKEN = 'test_csrf_token_123';

    public function setUp() {
        parent::setUp();
        Loader::register(array(
        ROOT_PATH . 'tests/',
        ROOT_PATH . 'tests/classes/',
        ROOT_PATH . 'tests/fixtures/',
        ROOT_PATH . 'webapp/libs/',
        ROOT_PATH . 'webapp/libs/model/',
        ROOT_PATH . 'webapp/libs/dao/',
        ROOT_PATH . 'webapp/libs/controller/',
        ROOT_PATH . 'webapp/libs/exceptions/'
        ));

        $config = Config::getInstance();
        if ($config->getValue('timezone')) {
            date_default_timezone_set($config->getValue('timezone'));
        }
        $this->DEBUG = (getenv('TEST_DEBUG')!==false) ? true : false;

        self::isTestEnvironmentReady();
    }

    public function tearDown() {
        Mailer::clearLastMail();
        Config::destroyInstance();
        if (isset($_SESSION)) {
            $this->unsetArray($_SESSION);
        }
        $this->unsetArray($_POST);
        $this->unsetArray($_GET);
        $this->unsetArray($_REQUEST);
        $this->unsetArray($_SERVER);
        $this->unsetArray($_FILES);
        Loader::unregister();
        parent::tearDown();
    }

    /**
     * Unset all the values for every key in an array
     * @param array $array
     */
    protected function unsetArray(array &$array) {
        $keys = array_keys($array);
        foreach ($keys as $key) {
            unset($array[$key]);
        }
    }

    /**
     * Move webapp/config.inc.php to webapp/config.inc.bak.php for tests with no config file
     */
    protected function removeConfigFile() {
        if (file_exists(WEBAPP_PATH . 'config.inc.php')) {
            $cmd = 'mv '.WEBAPP_PATH . 'config.inc.php ' .WEBAPP_PATH . 'config.inc.bak.php';
            exec($cmd, $output, $return_val);
            if ($return_val != 0) {
                echo "Could not ".$cmd;
            }
        }
    }

    /**
     * Move webapp/config.inc.bak.php to webapp/config.inc.php
     */
    protected function restoreConfigFile() {
        if (file_exists(WEBAPP_PATH . 'config.inc.bak.php')) {
            $cmd = 'mv '.WEBAPP_PATH . 'config.inc.bak.php ' .WEBAPP_PATH . 'config.inc.php';
            exec($cmd, $output, $return_val);
            if ($return_val != 0) {
                echo "Could not ".$cmd;
            }
        }
    }

    public function __destruct() {
        $this->restoreConfigFile();
    }

    public function debug($message) {
        if($this->DEBUG) {
            $bt = debug_backtrace();
            print get_class($this) . ": line " . $bt[0]['line'] . " - " . $message . "\n";
        }
    }

    /**
     * Preemptively halt test run if testing environment requirement isn't met.
     * Prevents unnecessary/inexplicable failures and data loss.
     */
    public static function isTestEnvironmentReady() {
        require WEBAPP_PATH.'extlibs/isosceles/libs/config.inc.php';

        $datadir_path = FileDataManager::getDataPath();
        if (!is_writable($datadir_path)) {
            $message = "In order to test your installation, $datadir_path must be writable.";
        }

        global $TEST_DATABASE;

        if ($ISOSCELES_CFG['db_name'] != $TEST_DATABASE) {
            $message = "The database name in ".WEBAPP_PATH.
            "extlibs/isosceles/libs/config.inc.php does not match \$TEST_DATABASE in ".
            "tests/config.tests.inc.php.
In order to test your Upstart installation without losing data, these database names must both point to the same ".
"empty test database.";
        }

        if ($ISOSCELES_CFG['cache_pages']) {
            $message = "In order to test your Upstart installation, \$ISOSCELES['cache_pages'] must be set to false.";
        }

        if (isset($message)) {
            die("Stopping tests...Test environment isn't ready.
".$message."
Please try again.
");
        }
    }

    /**
     * Wrapper for logging in an Upstart user in a test
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
