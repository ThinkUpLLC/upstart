<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfPaymentReminderController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new PaymentReminderController(true);
        $this->assertIsA($controller, 'PaymentReminderController');
    }

    public function testControl() {
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

        $controller = new PaymentReminderController(true);
        $controller->control();
        $sent_email = Mailer::getLastMail();
        $this->debug($sent_email);
        $this->assertPattern('/finalize your ThinkUp membership/', $sent_email);
    }
}
