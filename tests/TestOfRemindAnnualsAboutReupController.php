<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfRemindAnnualsAboutReupController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new RemindAnnualsAboutReupController(true);
        $this->assertIsA($controller, 'RemindAnnualsAboutReupController');
    }

    public function testFirstReminderMember() {
        $builders = array();
        //Paid through 2 days from now, no reminder sent yet
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'paid_through'=>'+2d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>0, 'creation_time'=>'-400d', 'membership_level'=>'Member'));

        $builders[] = FixtureBuilder::build('payments', array('id'=>1,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN8', 'caller_reference'=>'252cf11bbd71e2',
            'refund_amount'=>null, 'timestamp'=>date('2014-01-17 14:04:13'), 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('payments', array('id'=>2,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN6', 'caller_reference'=>'252cf11bbd71e1',
            'refund_amount'=>'5.60', 'timestamp'=>'2013-01-17 14:04:13', 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>1));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>2));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();

        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Renew your ThinkUp subscription and get 2 months free"/', $sent_email);
        $this->assertPattern('/On January 17th, 2014 you purchased an annual ThinkUp subscription and it\'s time/',
            $sent_email);
        $this->assertPattern('/2 MONTHS FREE/', $sent_email);
        $this->assertPattern('/only \$50/', $sent_email);
        $this->assertNoPattern('/only \$100/', $sent_email);

        $sql = "SELECT * FROM subscribers WHERE id=1";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual(1, $data['total_reup_reminders_sent']);
        $this->assertNotNull($data['reup_reminder_last_sent']);
        $this->assertNotNull($data['subscription_status'], 'Payment due');
    }

    public function testFirstReminderBird() {
        $builders = array();
        //Paid through 2 days from now, no reminder sent yet
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'paid_through'=>'+2d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>0, 'creation_time'=>'-400d', 'membership_level'=>'Early Bird'));

        $builders[] = FixtureBuilder::build('payments', array('id'=>1,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN8', 'caller_reference'=>'252cf11bbd71e2',
            'refund_amount'=>null, 'timestamp'=>date('2014-01-17 14:04:13'), 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('payments', array('id'=>2,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN6', 'caller_reference'=>'252cf11bbd71e1',
            'refund_amount'=>'5.60', 'timestamp'=>'2013-01-17 14:04:13', 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>1));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>2));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();

        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Time to renew your ThinkUp membership"/', $sent_email);
        $this->assertPattern('/On January 17th, 2014 you purchased an annual ThinkUp subscription and it\'s time/',
            $sent_email);
        //Birds already got the discount
        $this->assertNoPattern('/2 MONTHS FREE/', $sent_email);

        $sql = "SELECT * FROM subscribers WHERE id=1";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual(1, $data['total_reup_reminders_sent']);
        $this->assertNotNull($data['reup_reminder_last_sent']);
        $this->assertNotNull($data['subscription_status'], 'Payment due');
    }

    public function testFirstReminderPro() {
        $builders = array();
        //Paid through 2 days from now, no reminder sent yet
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'paid_through'=>'+2d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>0, 'creation_time'=>'-400d', 'membership_level'=>'Pro'));

        $builders[] = FixtureBuilder::build('payments', array('id'=>1,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN8', 'caller_reference'=>'252cf11bbd71e2',
            'refund_amount'=>null, 'timestamp'=>date('2014-01-17 14:04:13'), 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('payments', array('id'=>2,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN6', 'caller_reference'=>'252cf11bbd71e1',
            'refund_amount'=>'5.60', 'timestamp'=>'2013-01-17 14:04:13', 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>1));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>2));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();

        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Renew your ThinkUp subscription and get 2 months free"/', $sent_email);
        $this->assertPattern('/On January 17th, 2014 you purchased an annual ThinkUp subscription and it\'s time/',
            $sent_email);
        $this->assertPattern('/2 MONTHS FREE/', $sent_email);
        $this->assertNoPattern('/only \$50/', $sent_email);
        $this->assertPattern('/only \$100/', $sent_email);

        $sql = "SELECT * FROM subscribers WHERE id=1";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual(1, $data['total_reup_reminders_sent']);
        $this->assertNotNull($data['reup_reminder_last_sent']);
        $this->assertNotNull($data['subscription_status'], 'Payment due');
    }

    public function testFirstReminderExec() {
        $builders = array();
        //Paid through 2 days from now, no reminder sent yet
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'paid_through'=>'+2d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>0, 'creation_time'=>'-400d', 'membership_level'=>'Exec'));

        $builders[] = FixtureBuilder::build('payments', array('id'=>1,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN8', 'caller_reference'=>'252cf11bbd71e2',
            'refund_amount'=>null, 'timestamp'=>date('2014-01-17 14:04:13'), 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('payments', array('id'=>2,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN6', 'caller_reference'=>'252cf11bbd71e1',
            'refund_amount'=>'5.60', 'timestamp'=>'2013-01-17 14:04:13', 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>1));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>2));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();

        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->assertEqual($sent_email, '');
    }

    public function testFirstReminderNoInstallation() {
        $builders = array();
        //Paid through 2 days from now, no reminder sent yet
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>null, 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'paid_through'=>'+2d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>0, 'creation_time'=>'-400d', 'membership_level'=>'Exec'));

        $builders[] = FixtureBuilder::build('payments', array('id'=>1,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN8', 'caller_reference'=>'252cf11bbd71e2',
            'refund_amount'=>null, 'timestamp'=>date('2014-01-17 14:04:13'), 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('payments', array('id'=>2,
            'transaction_id'=>'213893Z22ZZ7483S3EVDZDFI95AZ1EOIRN6', 'caller_reference'=>'252cf11bbd71e1',
            'refund_amount'=>'5.60', 'timestamp'=>'2013-01-17 14:04:13', 'transaction_status'=>'Success'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>1));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>2));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();

        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->assertEqual($sent_email, '');
    }

    public function testFirstReminderAlreadySent() {
        $builders = array();
        //Paid through 2 days from now, reminder already sent
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'paid_through'=>'+14d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>1, 'creation_time'=>'-400d'));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->assertEqual($sent_email, '');
    }

    public function testSecondReminderMember() {
        $builders = array();
        //Paid through 1 week ago, only 1 reminder sent so far
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'total_payment_reminders_sent'=>1, 'creation_time'=>'-8d', 'payment_reminder_last_sent'=>'-150h',
            'paid_through'=>'-7d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>1, 'creation_time'=>'-400d', 'membership_level'=>'Member'));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Action required: Update your ThinkUp payment info/', $sent_email);
        $this->assertPattern('/It\'s time to renew your annual/', $sent_email);
        $this->assertPattern('/save \$10/', $sent_email);

        $sql = "SELECT * FROM subscribers WHERE id=4";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual(2, $data['total_reup_reminders_sent']);
        $this->assertNotNull($data['reup_reminder_last_sent']);
    }

    public function testSecondReminderBird() {
        $builders = array();
        //Paid through 1 week ago, only 1 reminder sent so far
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'total_payment_reminders_sent'=>1, 'creation_time'=>'-8d', 'payment_reminder_last_sent'=>'-150h',
            'paid_through'=>'-7d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>1, 'creation_time'=>'-400d', 'membership_level'=>'Late Bird'));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Action required: Update your ThinkUp payment info/', $sent_email);
        $this->assertPattern('/It\'s time to renew your annual/', $sent_email);
        $this->assertNoPattern('/save \$10/', $sent_email);

        $sql = "SELECT * FROM subscribers WHERE id=4";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual(2, $data['total_reup_reminders_sent']);
        $this->assertNotNull($data['reup_reminder_last_sent']);
    }

    public function testThirdReminderMember() {
        $builders = array();
        //Paid through 3 week ago, 2 reminders sent so far
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'total_payment_reminders_sent'=>1, 'creation_time'=>'-8d', 'payment_reminder_last_sent'=>'-150d',
            'paid_through'=>'-21d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>2, 'creation_time'=>'-400d', 'membership_level'=>'Member'));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/LAST CHANCE to save your ThinkUp account/', $sent_email);
        $this->assertPattern('/Your ThinkUp subscription is past due\!/', $sent_email);
        $this->assertPattern('/save \$10/', $sent_email);

        $sql = "SELECT * FROM subscribers WHERE id=4";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual(3, $data['total_reup_reminders_sent']);
        $this->assertNotNull($data['reup_reminder_last_sent']);
    }
}
