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
        $this->assertEqual('Payment due', $data['subscription_status']);
        $this->assertEqual(0, $data['is_account_closed']);
    }

    public function testInsertCompleteSubscriber() {
        //Valid, unique new subscriber
        $subscriber = new Subscriber();
        $subscriber->email = 'ginatrapani@example.com';
        $subscriber->network_user_name = 'ginatrapani';
        $subscriber->network_user_id = '930061';
        $subscriber->network = 'twitter';
        $subscriber->full_name = 'Gina Trapani';
        $subscriber->oauth_access_token = 's3cr3tt0ken';
        $subscriber->membership_level = 'Member';
        $subscriber->timezone = 'America/New_York';

        $dao = new SubscriberMySQLDAO();
        $result = $dao->insertCompleteSubscriber($subscriber);
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscribers WHERE id = ".$result;
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual('ginatrapani@example.com', $data['email']);
        $this->assertEqual('ginatrapani', $data['network_user_name']);
        $this->assertEqual('Gina Trapani', $data['full_name']);
        $this->assertEqual('s3cr3tt0ken', $data['oauth_access_token']);
        $this->assertEqual('Member', $data['membership_level']);
        $this->assertEqual('America/New_York', $data['timezone']);
        $this->assertEqual('Payment due', $data['subscription_status']);
        $this->assertEqual(0, $data['is_account_closed']);

        //Try inserting new subscriber with same network credentials
        $subscriber = new Subscriber();
        $subscriber->email = 'ginatrapani2@example.com';
        $subscriber->network_user_name = 'ginatrapani';
        $subscriber->network_user_id = '930061';
        $subscriber->network = 'twitter';
        $subscriber->full_name = 'Gina 2 Trapani';
        $subscriber->oauth_access_token = 's3cr3tt0ken';
        $subscriber->membership_level = 'Member';
        $subscriber->timezone = 'America/New_York';

        $this->expectException('DuplicateSubscriberConnectionException');
        $result = $dao->insertCompleteSubscriber($subscriber);
    }

    public function testDoesSubscriberConnectionExist() {
        $dao = new SubscriberMySQLDAO();
        $this->assertFalse($dao->doesSubscriberConnectionExist('930061', 'twitter'));

        $builder = FixtureBuilder::build('subscribers', array('network_user_id'=>'930061', 'network'=>'twitter'));
        $this->assertTrue($dao->doesSubscriberConnectionExist('930061', 'twitter'));
    }

    public function testDoesSubscriberEmailExist() {
        $dao = new SubscriberMySQLDAO();
        $this->assertFalse($dao->doesSubscriberEmailExist('thinkuptest@gmail.com'));

        $builder = FixtureBuilder::build('subscribers', array('email'=>'thinkuptest@gmail.com'));
        $this->assertTrue($dao->doesSubscriberEmailExist('thinkuptest@gmail.com'));
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
        $this->assertEqual($subscriber->subscription_status, 'Payment due');
        $this->assertFalse($subscriber->is_account_closed);

        $this->expectException('SubscriberDoesNotExistException');
        $subscriber = $dao->getByEmail('yoyo@example.com');
        $this->assertNull($subscriber);
    }

    public function testGetByID() {
        $dao = new SubscriberMySQLDAO();
        $result = $dao->insert('ginatrapani@example.com', 'secr3tpassword');

        $subscriber = $dao->getByID(1);
        $this->assertIsA($subscriber, 'Subscriber');
        $this->assertEqual($subscriber->id, 1);
        $this->assertEqual($subscriber->email, 'ginatrapani@example.com');
        $this->assertEqual($subscriber->full_name, '');
        $this->assertEqual($subscriber->follower_count, 0);
        $this->assertEqual($subscriber->is_verified, 0);
        $this->assertEqual($subscriber->subscription_status, 'Payment due');
        $this->assertFalse($subscriber->is_account_closed);

        $this->expectException('SubscriberDoesNotExistException');
        $subscriber = $dao->getByID(5);
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
        $this->assertFalse($subscriber->is_account_closed);
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
        'verification_code'=>1234, 'thinkup_username'=>'unique'));

        $dao = new SubscriberMySQLDAO();
        $result = $dao->getVerificationCode('ginatrapani@example.com');
        $this->assertEqual($result['verification_code'], 1234);

        $result = $dao->getVerificationCode('doesnotexist@example.com');
        $this->assertEqual($result['verification_code'], null);
    }

    public function testGetPass() {
        $builders = array();
        $test_salt = 'test_salt';
        $password = TestLoginHelper::hashPassword('secretpassword', $test_salt);

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'me@example.com', 'pwd'=>$password,
        'pwd_salt'=>$test_salt, 'is_email_verified'=>1, 'is_activated'=>0, 'is_admin'=>1, 'thinkup_username'=>null));

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>7, 'email'=>'inactive@example.com',
        'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_email_verified'=>0, 'is_activated'=>0, 'is_admin'=>0,
        'thinkup_username'=>null, 'verification_code'=>'224455'));

        $builders[] = FixtureBuilder::build('subscribers', array('id'=>8, 'email'=>'active@example.com',
        'pwd'=>$password, 'pwd_salt'=>$test_salt, 'is_email_verified'=>1, 'is_activated'=>1, 'is_admin'=>0,
        'thinkup_username'=>null, 'verification_code'=>'224455'));

        $dao = new SubscriberMySQLDAO();
        //subscriber who doesn't exist
        $result = $dao->getPass('idontexist@example.com');
        $this->assertFalse($result);
        //subscriber who is not activated
        $result = $dao->getPass('inactive@example.com');
        $this->assertTrue($result);
        //activated subscriber
        $result = $dao->getPass('active@example.com');
        $this->assertEqual($result, $password);
    }

    public function testVerifyEmailAddress() {
        $builder = FixtureBuilder::build('subscribers', array('email'=>'ginatrapani@example.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'unique'));

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

    public function testGetSearchResults() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'ginatrapani@example.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
        'thinkup_username'=>'unique1'));
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'lexluther@evilmail.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'unique2'));
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'xanderharris@buff.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'unique3'));
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'willowrosenberg@willow.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'unique4'));

        $dao = new SubscriberMySQLDAO();
        //test match email address
        $result = $dao->getSearchResults('ginatrapani@example.com');
        $this->assertEqual(sizeof($result), 1);
        $result = $dao->getSearchResults('.com');
        $this->assertEqual(sizeof($result), 4);

        //test match username
        $result = $dao->getSearchResults('gtra');
        $this->assertEqual(sizeof($result), 1);

        //test match full name
        $result = $dao->getSearchResults('Gena');
        $this->assertEqual(sizeof($result), 1);
        $result = $dao->getSearchResults('gena davisson');
        $this->assertEqual(sizeof($result), 0);
        $result = $dao->getSearchResults('gena davis');
        $this->assertEqual(sizeof($result), 1);

        //test match TU username
        $result = $dao->getSearchResults('unique');
        $this->assertEqual(sizeof($result), 4);
        $result = $dao->getSearchResults('unique4');
        $this->assertEqual(sizeof($result), 1);

        //test no matches
        $result = $dao->getSearchResults('yaya');
        $this->assertEqual(sizeof($result), 0);
    }

    public function testArchiveSubscriber() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'ginatrapani@example.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
        'thinkup_username'=>'unique1', 'subscription_status'=>'About to be archived', 'total_payment_reminders_sent'=>3,
        'payment_reminder_last_sent'=>'2001-01-01 11:55:05'));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>2, 'email'=>'lexluther@evilmail.com',
        'network_user_name'=>'lexluther', 'verification_code'=>1234, 'is_email_verified'=>0,
        'thinkup_username'=>'unique2'));
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'xanderharris@buff.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'unique3'));
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'willowrosenberg@willow.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'unique4'));
        $builders[] = FixtureBuilder::build('authorizations', array('id'=>1, 'token_id'=>'aabbccdd' ));
        $builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>2,
        'authorization_id'=>1));

        $dao = new SubscriberMySQLDAO();
        $result = $dao->archiveSubscriber(1);
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscriber_archive WHERE email = 'ginatrapani@example.com'";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual('gtra', $data['network_user_name']);
        $this->assertEqual('', $data['token_id']);
        $this->assertEqual('About to be archived', $data['subscription_status']);
        $this->assertEqual(3, $data['total_payment_reminders_sent']);
        $this->assertEqual('2001-01-01 11:55:05', $data['payment_reminder_last_sent']);

        $result = $dao->archiveSubscriber(2);
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscriber_archive WHERE email = 'lexluther@evilmail.com'";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual('lexluther', $data['network_user_name']);
        $this->assertEqual('aabbccdd', $data['token_id']);
        $this->assertEqual('Payment due', $data['subscription_status']);
        $this->assertEqual(0, $data['total_payment_reminders_sent']);
    }

    public function testDeleteBySubscriberID() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>101, 'email'=>'willow@buffy.com',
        'network_user_name'=>'willowr', 'thinkup_username'=>'unique1'));

        $dao = new SubscriberMySQLDAO();
        $result = $dao->deleteBySubscriberID(101);
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscribers WHERE id=101";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($data);
    }

    public function testSetUsername() {
        //Subscriber doesn't exist
        $dao = new SubscriberMySQLDAO();
        $result = $dao->setUsername(1, 'willowrosenberg');
        $this->assertFalse($result);

        //Subscriber exists and has no username
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>101, 'email'=>'willow@buffy.com',
        'network_user_name'=>'willowr', 'thinkup_username'=>null));

        $result = $dao->setUsername(101, 'willowrosenberg');
        $this->assertTrue($result);

        $sql = "SELECT * FROM subscribers WHERE id=101";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual('willowrosenberg', $data['thinkup_username']);

        //Try to set a duplicate username
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>102, 'email'=>'xander@buffy.com',
        'network_user_name'=>'xanderh', 'thinkup_username'=>null));

        //test inserting same token twice
        $this->expectException('DuplicateSubscriberUsernameException');
        $result = $dao->setUsername(102, 'willowrosenberg');
    }

    public function testSetEmail() {
        //Subscriber doesn't exist
        $dao = new SubscriberMySQLDAO();
        $result = $dao->setEmail(1, 'willowrosenberg@buffy.com');
        $this->assertFalse($result);

        //Subscriber exists and has no username
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>101, 'email'=>'willow@buffy.com',
        'network_user_name'=>'willowr', 'thinkup_username'=>null));

        $result = $dao->setEmail(101, 'willowrosenberg@buffy.com');
        $this->assertTrue($result);

        $sql = "SELECT * FROM subscribers WHERE id=101";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual('willowrosenberg@buffy.com', $data['email']);

        //Try to set a duplicate username
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>102, 'email'=>'xander@buffy.com',
        'network_user_name'=>'xanderh', 'thinkup_username'=>null));

        //test inserting same token twice
        $this->expectException('DuplicateSubscriberEmailException');
        $result = $dao->setEmail(102, 'willowrosenberg@buffy.com');
    }

    public function testIsUsernameTaken() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'ginatrapani@example.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
        'thinkup_username'=>'unique1'));
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'lexluther@evilmail.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'unique2'));
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'xanderharris@buff.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'unique3'));
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'willowrosenberg@willow.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'unique4'));
        $builders[] = FixtureBuilder::build('subscribers', array('email'=>'buffysummers@willow.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'thinkup_username'=>'UniQue5'));
        $dao = new SubscriberMySQLDAO();
       //Username not taken
        $this->assertFalse($dao->isUsernameTaken('cordeliachase'));

        //Usernames taken
        $this->assertTrue($dao->isUsernameTaken('unique1'));
        $this->assertTrue($dao->isUsernameTaken('unique2'));
        $this->assertTrue($dao->isUsernameTaken('unique3'));
        $this->assertTrue($dao->isUsernameTaken('unique4'));
        //Is the check case-insensitive?
        $this->assertTrue($dao->isUsernameTaken('UniquE4'));
        $this->assertTrue($dao->isUsernameTaken('unique5'));
    }

    public function testUpdateDateInstalled() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani@example.com',
        'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
        'thinkup_username'=>'unique1', 'date_installed'=>null));

        $dao = new SubscriberMySQLDAO();
        $dao->updateDateInstalled(1, '2014-01-15 09:00:00');

        $subscriber = $dao->getByEmail('ginatrapani@example.com');
        $this->assertEqual($subscriber->date_installed, '2014-01-15 09:00:00');

        $dao->updateDateInstalled(1, null);
        $subscriber = $dao->getByEmail('ginatrapani@example.com');
        $this->assertEqual($subscriber->date_installed, null);
    }

    public function testCompSubscription() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0));

        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByID(1);
        $this->assertFalse($subscriber->is_membership_complimentary);

        $result = $dao->compSubscription(1);
        $this->assertEqual($result, 1);

        $subscriber = $dao->getByID(1);
        $this->assertTrue($subscriber->is_membership_complimentary);
    }

    public function testUpdateSubscriptionStatus() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>1,
            'subscription_status'=>'Payment due'));

        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByID(1);
        $this->assertEqual($subscriber->subscription_status, 'Payment due');
        //test without specifying status
        $subscriber = $dao->updateSubscriptionStatus(1);
        $subscriber = $dao->getByID(1);
        $this->assertEqual($subscriber->subscription_status, 'Complimentary membership');

        //test with specifying status
        $subscriber = $dao->updateSubscriptionStatus(1, 'Test status');
        $subscriber = $dao->getByID(1);
        $this->assertEqual($subscriber->subscription_status, 'Test status');
    }

    public function testGetPaidStaleInstalls() {
        $builders = array();
        //Should get returned
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid through Jan 15 2015'));
        //Should get returned
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>2, 'email'=>'ginatrapani+2@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique2', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>null, 'subscription_status'=>'Paid through Jan 15 2015'));
        //Should not get returned because installation is not active
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>3, 'email'=>'ginatrapani+3@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique3', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>0, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid through Jan 15 2015'));
        //Should not get returned because not paid
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Payment failed'));
        //Should get returned because complimentary
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>5, 'email'=>'ginatrapani+5@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique5', 'date_installed'=>null, 'is_membership_complimentary'=>1,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Payment failed'));
        //Should not get returned because less than 1 hour old
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'ginatrapani+6@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique6', 'date_installed'=>null, 'is_membership_complimentary'=>1,
            'is_installation_active'=>1, 'last_dispatched'=>'-1h', 'subscription_status'=>'Payment failed'));
        //Should get returned because more than 1 hour old
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>7, 'email'=>'ginatrapani+7@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique7', 'date_installed'=>null, 'is_membership_complimentary'=>1,
            'is_installation_active'=>1, 'last_dispatched'=>'-4h', 'subscription_status'=>'Payment failed'));

        $dao = new SubscriberMySQLDAO();
        $result = $dao->getPaidStaleInstalls();

        $this->assertEqual(sizeof($result), 4);
        $this->assertEqual($result[0]['thinkup_username'], 'unique2');
        $this->assertEqual($result[1]['thinkup_username'], 'unique1');
    }

    public function testGetNotPaidStaleInstalls() {
        $builders = array();
        //Should not get returned because paid
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid through Jan 15 2015'));
        //Should not get returned because paid
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>2, 'email'=>'ginatrapani+2@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique2', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>null, 'subscription_status'=>'Paid through Jan 15 2015'));
        //Should not get returned because installation is not active
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>3, 'email'=>'ginatrapani+3@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique3', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>0, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid through Jan 15 2015'));
        //Should get returned because not paid
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Payment failed'));
        //Should get returned because not paid and only 1 reminder was sent
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>5, 'email'=>'ginatrapani+5@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra5', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique5', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Payment failed',
            'total_payment_reminders_sent'=>1, 'payment_reminder_last_sent'=>'-3d'));
        //Should not get returned because not paid and 3 reminders sent, last one more than 12 days ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>6, 'email'=>'ginatrapani+6@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra6', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique6', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Payment due',
            'total_payment_reminders_sent'=>3, 'payment_reminder_last_sent'=>'-14d'));
        //Should get returned because not paid and 3 reminders sent, last one less than 12 days ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>7, 'email'=>'ginatrapani+7@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra6', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique7', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Payment due',
            'total_payment_reminders_sent'=>3, 'payment_reminder_last_sent'=>'-10d'));

        $dao = new SubscriberMySQLDAO();
        $result = $dao->getNotYetPaidStaleInstalls();

        $this->assertEqual(sizeof($result), 3);
        $this->assertEqual($result[0]['thinkup_username'], 'unique4');
        $this->assertEqual($result[1]['thinkup_username'], 'unique5');
        $this->assertEqual($result[2]['thinkup_username'], 'unique7');
    }

    public function testgetSubscribersPaymentDueReminder() {
        $builders = array();
        //Payment due, 0 reminders sent, signed up 2 days ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Payment due',
            'total_payment_reminders_sent'=>0, 'creation_time'=>'-2d'));
        //Payment due, 0 reminders sent, signed up 6 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>2, 'email'=>'ginatrapani+2@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique2', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>null, 'subscription_status'=>'Payment due',
            'total_payment_reminders_sent'=>0, 'creation_time'=>'-6h'));
        //Payment due, 0 reminders sent, signed up 3 days ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>3, 'email'=>'ginatrapani+3@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique3', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>0, 'last_dispatched'=>'-1d', 'subscription_status'=>'Payment due',
            'total_payment_reminders_sent'=>0, 'creation_time'=>'-3d'));
        //Payment due, 1 reminder sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Payment due',
            'total_payment_reminders_sent'=>1, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-60h'));
        //Paid, 2 reminders sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>5, 'email'=>'ginatrapani+5@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique5', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid through April 1, 2015',
            'total_payment_reminders_sent'=>2, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-60h'));

        $dao = new SubscriberMySQLDAO();
        //sleep(1000);

        //Get subscribers with no reminders, due one 24 hours later
        $result = $dao->getSubscribersPaymentDueReminder( 0, 24);
        $this->debug(Utils::varDumpToString($result));
        $this->assertEqual(sizeof($result), 2);
        $this->assertEqual($result[0]->thinkup_username, 'unique3');
        $this->assertEqual($result[1]->thinkup_username, 'unique1');

        //Get subscribers with 1 reminder, due one 24 hours later
        $result = $dao->getSubscribersPaymentDueReminder(1, 24);
        $this->debug(Utils::varDumpToString($result));
        $this->assertEqual(sizeof($result), 1);
        $this->assertEqual($result[0]->thinkup_username, 'unique4');

        //Get subscribers with 1 reminder, due one 70 hours later
        $result = $dao->getSubscribersPaymentDueReminder(1, 70);
        $this->debug(Utils::varDumpToString($result));
        $this->assertEqual(sizeof($result), 0);
    }

    public function testSetTotalPaymentRemindersSent() {
        //Subscriber doesn't exist
        $dao = new SubscriberMySQLDAO();
        $result = $dao->setTotalPaymentRemindersSent(1, 2);
        $this->assertEqual($result, 0);

        //Subscriber exists and has no username
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>101, 'email'=>'willow@buffy.com',
        'network_user_name'=>'willowr', 'thinkup_username'=>null, 'total_payment_reminders_sent'=>0,
        'payment_reminder_last_sent'=>null));

        $result = $dao->setTotalPaymentRemindersSent(101, 2);
        $this->assertEqual($result, 1);

        $sql = "SELECT * FROM subscribers WHERE id=101";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual(2, $data['total_payment_reminders_sent']);
        $this->assertNotNull($data['payment_reminder_last_sent']);
    }
}