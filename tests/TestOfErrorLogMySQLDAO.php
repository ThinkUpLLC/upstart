<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfErrorLogMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new ErrorLogMySQLDAO();
        $this->assertIsA($dao, 'ErrorLogMySQLDAO');
    }

    public function testInsert() {
        SessionCache::put('hi', 'there');
        $extra_debug = "Coder-written notes on problem";
        exec('git rev-parse --verify HEAD 2> /dev/null', $output);
        $commit_hash = $output[0];
        $debug = "SESSION:
".Utils::varDumpToString($_SESSION)."

GET:
".Utils::varDumpToString($_GET)."

POST:
".Utils::varDumpToString($_POST)."

Notes:
".$extra_debug;
        $error_dao = new ErrorLogMySQLDAO();
        $result = $error_dao->insert($commit_hash, __FILE__, __LINE__, __METHOD__, $debug);

        $sql = "SELECT * FROM error_log WHERE id = ".$result;
        $stmt = ErrorLogMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($commit_hash, $data['commit_hash']);
        $this->assertEqual(__METHOD__, $data['method']);
        $this->assertEqual(__FILE__, $data['filename']);
    }
}