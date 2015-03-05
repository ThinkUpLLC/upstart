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

    public function testFirstReminder() {
        $builders = array();
        //Paid through 2 weeks from now, no reminder sent yet
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'paid_through'=>'+14d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>0, 'creation_time'=>'-400d'));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Your first year of ThinkUp/', $sent_email);
        $this->assertPattern('/A year ago, you were one of the first people in the world to join ThinkUp/', $sent_email);
        $this->assertNoPattern('/about to renew/', $sent_email);

        $sql = "SELECT * FROM subscribers WHERE id=1";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual(1, $data['total_reup_reminders_sent']);
        $this->assertNotNull($data['reup_reminder_last_sent']);
    }

    public function testFirstReminderAlreadySent() {
        $builders = array();
        //Paid through 2 weeks from now, reminder already sent
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

    public function testSecondReminder() {
        $builders = array();
        //Paid through 1 week from now, only 1 reminder sent so far
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'total_payment_reminders_sent'=>1, 'creation_time'=>'-8d', 'payment_reminder_last_sent'=>'-150h',
            'paid_through'=>'+7d', 'subscription_recurrence'=>'12 months', 'is_account_closed'=>0,
            'total_reup_reminders_sent'=>1, 'creation_time'=>'-400d'));

        $controller = new RemindAnnualsAboutReupController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Your ThinkUp membership is about to renew/', $sent_email);
        $this->assertPattern('/ThinkUp will automatically charge your Amazon Payments account next week/',
            $sent_email);

        $sql = "SELECT * FROM subscribers WHERE id=4";
        $stmt = SubscriberMySQLDAO::$PDO->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertTrue($data);
        $this->assertEqual(2, $data['total_reup_reminders_sent']);
        $this->assertNotNull($data['reup_reminder_last_sent']);
    }
}
