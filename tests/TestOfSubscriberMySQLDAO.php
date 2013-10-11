<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfSubscriberMySQLDAO extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $dao = new SubscriberMySQLDAO();
        $this->assertIsA($dao, 'SubscriberMySQLDAO');
    }

    public function testInsert() {
        $dao = new SubscriberMySQLDAO();
        $result = $dao->insert('ginatrapani@example.com', 'secr3tpassword');
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscribers WHERE id = ".$result;
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual('ginatrapani@example.com', $data['email']);
    }

    public function testInsertDuplicateEmail() {
        $dao = new SubscriberMySQLDAO();
        $result = $dao->insert('ginatrapani@example.com', 'secr3tpassword');

        //test inserting same token twice
        $this->expectException('DuplicateSubscriberEmailException');
        $result = $dao->insert('ginatrapani@example.com', 'secr3tpassword');
    }

    public function testGetByEmail() {
        $dao = new SubscriberMySQLDAO();
        $result = $dao->insert('ginatrapani@example.com', 'secr3tpassword');

        $subscriber = $dao->getByEmail('ginatrapani@example.com');
        $this->assertIsA($subscriber, 'Subscriber');
        $this->assertEqual($subscriber->id, 1);
        $this->assertEqual($subscriber->email, 'ginatrapani@example.com');
        $this->assertEqual($subscriber->full_name, '');
        $this->assertEqual($subscriber->follower_count, 0);
        $this->assertEqual($subscriber->is_verified, 0);

        $subscriber = $dao->getByEmail('yoyo@example.com');
        $this->assertNull($subscriber);
    }

    public function testUpdate() {
        $dao = new SubscriberMySQLDAO();
        $result = $dao->insert('ginatrapani@example.com', 'secr3tpassword');
        $this->assertEqual($result, 1);

        $update_count = $dao->update($result, "gina trapani", '930061', 'twitter', 'Gina Trapani', 'aabbcc', 'xxyyzz',
        1, 649);
        $this->assertEqual($update_count, 1);
        $subscriber = $dao->getByEmail('ginatrapani@example.com');
        $this->assertIsA($subscriber, 'Subscriber');
        $this->assertEqual($subscriber->id, 1);
        $this->assertEqual($subscriber->email, 'ginatrapani@example.com');
        $this->assertEqual($subscriber->full_name, 'Gina Trapani');
        $this->assertEqual($subscriber->follower_count, 649);
        $this->assertEqual($subscriber->is_verified, 1);
    }

    public function testUpdateDuplicateConnection() {
        $dao = new SubscriberMySQLDAO();
        $result1 = $dao->insert('ginatrapani@example.com', 'secr3tpassword');
        $this->assertEqual($result1, 1);
        $result2 = $dao->insert('ginatrapani+1@example.com', 'secr3tpassword');

        $update_count = $dao->update($result1, "gina trapani", '930061', 'twitter', 'Gina Trapani', 'aabbcc', 'xxyyzz',
        1, 649);
        $this->assertEqual($update_count, 1);

        $this->expectException('DuplicateSubscriberConnectionException');
        $update_count = $dao->update($result2, "gina trapani", '930061', 'twitter', 'Gina Trapani', 'aabbcc', 'xxyyzz',
        1, 649);
    }

    public function testGetVerificationCode() {
        $builder = FixtureBuilder::build('subscribers', array('email'=>'ginatrapani@example.com',
        'verification_code'=>1234));

        $dao = new SubscriberMySQLDAO();
        $result = $dao->getVerificationCode('ginatrapani@example.com');
        $this->assertEqual($result['verification_code'], 1234);

        $result = $dao->getVerificationCode('doesnotexist@example.com');
        $this->assertEqual($result['verification_code'], null);
    }

    public function testVerifyEmailAddress() {
        $builder = FixtureBuilder::build('subscribers', array('email'=>'ginatrapani@example.com',
        'verification_code'=>1234, 'is_email_verified'=>0));

        $dao = new SubscriberMySQLDAO();
        $result = $dao->verifyEmailAddress('ginatrapani@example.com');
        $this->assertEqual($result, 1);

        $sql = "SELECT is_email_verified FROM subscribers WHERE email = 'ginatrapani@example.com'";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual(1, $data['is_email_verified']);

        $result = $dao->verifyEmailAddress('idontxexist@example.com');
        $this->assertEqual($result, 0);
    }
}