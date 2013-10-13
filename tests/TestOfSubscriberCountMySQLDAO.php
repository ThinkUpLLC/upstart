<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfSubscriberCountMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new SubscriberCountMySQLDAO();
        $this->assertIsA($dao, 'SubscriberCountMySQLDAO');
    }

    public function testIncrement() {
        $dao = new SubscriberCountMySQLDAO();
        $result = $dao->increment(50);
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscriber_counts WHERE amount = 50";
        $stmt = SubscriberCountMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual(1, $data['count']);
    }

    public function testGetAll() {
        $dao = new SubscriberCountMySQLDAO();
        $dao->increment(50);
        $dao->increment(50);
        $dao->increment(50);
        $dao->increment(60);
        $dao->increment(120);
        $dao->increment(150);
        $result = $dao->getAll();

        $this->assertEqual(sizeof($result), 5);
        $this->assertEqual($result['all'], 6);
        $this->assertEqual($result[50], 3);
        $this->assertEqual($result[60], 1);
    }
}