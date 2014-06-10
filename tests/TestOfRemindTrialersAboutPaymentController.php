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

    public function testFirstReminder() {
        $builders = array();
        //Free Trial, 0 reminders sent, signed up 2 days ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'ginatrapani+1@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique1', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>0, 'creation_time'=>'-2d'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Ready to join ThinkUp and get your FREE gift\?/', $sent_email);
        $this->assertPattern('/Loving ThinkUp\? Time to join!/', $sent_email);
    }

    public function testSecondReminder() {
        $builders = array();
        //Free Trial, 1 reminder sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>1, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-150h'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Enjoying ThinkUp\? Join and get a FREE book.../', $sent_email);
        $this->assertPattern('/You\'ve tried ThinkUp for a week.../', $sent_email);
    }

    public function testThirdReminder() {
        $builders = array();
        //Free Trial, 2 reminders sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>2, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-150h'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/Your ThinkUp trial has almost expired!/', $sent_email);
        $this->assertPattern('/Only one day left to join ThinkUp!/', $sent_email);
    }

    public function testFourthReminder() {
        $builders = array();
        //Free Trial, 3 reminders sent, signed up 7 days ago, last sent 40 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'email'=>'ginatrapani+4@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique4', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Free Trial',
            'total_payment_reminders_sent'=>3, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-40h'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/FINAL REMINDER: Don\'t lose your ThinkUp membership!/', $sent_email);
        $this->assertPattern('/Action Required: Your ThinkUp trial is ending/', $sent_email);
    }

    public function testNoReminderDuePaid() {
        $builders = array();
        //Paid, 2 reminders sent, signed up 7 days ago, last sent 60 hours ago
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>5, 'email'=>'ginatrapani+5@example.com',
            'verification_code'=>1234, 'is_email_verified'=>0, 'network_user_name'=>'gtra4', 'full_name'=>'gena davis',
            'thinkup_username'=>'unique5', 'date_installed'=>null, 'is_membership_complimentary'=>0,
            'is_installation_active'=>1, 'last_dispatched'=>'-1d', 'subscription_status'=>'Paid through April 1, 2015',
            'total_payment_reminders_sent'=>2, 'creation_time'=>'-7d', 'payment_reminder_last_sent'=>'-60h'));

        $controller = new RemindTrialersAboutPaymentController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertEqual($sent_email, '');
    }
}
