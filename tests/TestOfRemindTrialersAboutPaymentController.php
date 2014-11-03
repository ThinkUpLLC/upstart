<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfRemindTrialersAboutPaymentController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new RemindTrialersAboutPaymentController(true);
        $this->assertIsA($controller, 'RemindTrialersAboutPaymentController');
    }

    public function testFirstReminderMonthlyMember() {
        $builders = array();
        //Free Trial, 0 reminders sent, signed up 3 days ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'membership_level'=>'Member', 'total_payment_reminders_sent'=>0, 'creation_time'=>'-3d'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Join ThinkUp and get your FREE gift!/', $sent_email);
        $this->assertPattern('/Check out your ThinkUp insights at/', $sent_email);
        $this->assertNoPattern('/monthly-pro\.png/', $sent_email);
        $this->assertPattern('/monthly-member\.png/', $sent_email);
    }

    public function testSecondReminderMonthlyMember() {
        $builders = array();
        //Free Trial, 1 reminder sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>1, 'creation_time'=>'-8d', 'payment_reminder_last_sent'=>'-150h',
            'membership_level'=>'Member'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Enjoying ThinkUp\? Join and get even more.../', $sent_email);
        $this->assertPattern('/seen that no other service gives you these/', $sent_email);
        $this->assertPattern('/\$5/', $sent_email);
        $this->assertNoPattern('/\$10/', $sent_email);
    }

    public function testThirdReminderMonthlyMember() {
        $builders = array();
        //Free Trial, 2 reminders sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>2, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-150h',
            'membership_level'=>'Member'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/One day left: Ready to join ThinkUp\?/', $sent_email);
        $this->assertPattern('/Over the past two weeks/', $sent_email);
        $this->assertPattern('/\$5/', $sent_email);
        $this->assertNoPattern('/\$10/', $sent_email);
    }

    public function testFourthReminderMonthlyMember() {
        $builders = array();
        //Free Trial, 3 reminders sent, signed up 7 days ago, last sent 40 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>3, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-40h',
            'membership_level'=>'Member'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Your ThinkUp free trial ends TODAY. Join now!/', $sent_email);
        $this->assertPattern('/lose your personal insights at/', $sent_email);
        $this->assertNoPattern('/monthly-pro\.png/', $sent_email);
        $this->assertPattern('/monthly-member\.png/', $sent_email);
    }

    public function testFirstReminderMonthlyPro() {
        $builders = array();
        //Free Trial, 0 reminders sent, signed up 3 days ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'membership_level'=>'Pro', 'total_payment_reminders_sent'=>0, 'creation_time'=>'-3d'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/-pro\.png/', $sent_email);
    }

    public function testSecondReminderMonthlyPro() {
        $builders = array();
        //Free Trial, 1 reminder sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>1, 'creation_time'=>'-8d', 'payment_reminder_last_sent'=>'-150h',
            'membership_level'=>'Pro'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/\$10/', $sent_email);
    }

    public function testThirdReminderMonthlyPro() {
        $builders = array();
        //Free Trial, 2 reminders sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>2, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-150h',
            'membership_level'=>'Pro'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/\$10/', $sent_email);
    }

    public function testFourthReminderMonthlyPro() {
        $builders = array();
        //Free Trial, 3 reminders sent, signed up 7 days ago, last sent 40 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>3, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-40h',
            'membership_level'=>'Pro'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/-pro\.png/', $sent_email);
    }

    public function testNoReminderDuePaid() {
        $builders = array();
        //Paid, 2 reminders sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>5, 'email'=>'ginatrapani+5@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique5', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid',
            'total_payment_reminders_sent'=>2, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-60h',
            'membership_level'=>'Member'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertEqual($sent_email, '');
    }
}
