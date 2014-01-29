<?php

class UpstartUnitTestCase extends UpstartBasicUnitTestCase {
    /**
     * @var UpstartTestDatabaseHelper
     */
    var $testdb_helper;
    /**
     * @var str
     */
    var $test_database_name;
    /**
     * @var str
     */
    var $table_prefix;
    /**
     * Create a clean copy of the ThinkUp database structure
     */
    public function setUp() {
        parent::setUp();
        require ROOT_PATH .'tests/config.tests.inc.php';
        $this->test_database_name = $TEST_DATABASE;
        //Override default CFG values
        $ISOSCELES_CFG['db_name'] = $this->test_database_name;

        $config = Config::getInstance();
        $config->setValue('db_name', $this->test_database_name);

        $this->testdb_helper = new UpstartTestDatabaseHelper();
        $this->testdb_helper->drop($this->test_database_name);
        $this->testdb_helper->create($ISOSCELES_CFG['source_root_path']."sql/build_db.sql");

        $this->table_prefix = $config->getValue('table_prefix');
        $_SERVER['REQUEST_URI'] = null;
    }

    /**
     * Drop the database and kill the connection
     */
    public function tearDown() {
        if (isset(UpstartTestDatabaseHelper::$PDO)) {
            $this->testdb_helper->drop($this->test_database_name);
        }
        parent::tearDown();
    }

    /**
     * Returns an xml/xhtml document element by id
     * @param $doc an xml/xhtml document pobject
     * @param $id element id
     * @return Element
     */
    public function getElementById($doc, $id) {
        $xpath = new DOMXPath($doc);
        return $xpath->query("//*[@id='$id']")->item(0);
    }
}