<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';

class TestOfMailer extends UpstartBasicUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testGetSystemMessageHTMLPaymentReminderAbandons1Through3() {
        $headline = "Lock in your ThinkUp membership";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'tyrionlannister' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-abandon-1.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);

        $headline = "One quick step needed to keep your ThinkUp account";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'tyrionlannister' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-abandon-2.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);

        $headline = "We don’t want to say goodbye so soon!";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'tyrionlannister' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-abandon-3.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLPaymentReminderFreeTrial1Through4() {
        $headline = "Loving ThinkUp? Time to join!";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'tyrionlannister' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-trial-1.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);

        $headline = "You've tried ThinkUp for a week...";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'tyrionlannister' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-trial-2.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);

        $headline = "Only one day left to join ThinkUp!";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'tyrionlannister' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-trial-3.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);

        $headline = "Action Required: Your ThinkUp trial is ending";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'tyrionlannister' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-trial-4.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLPaymentChargeSuccessfulMember() {
        $headline = "Thanks for joining ThinkUp!";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('member_level', 'Member' );
        $email_view_mgr->assign('amount', 60 );
        $email_view_mgr->assign('renewal_date', 'January 15, 2015' );
        $email_view_mgr->assign('thinkup_username', 'tyrionlannister' );
        $email_view_mgr->assign('installation_url', 'asdf.thinkup.com');
        $body_html = $email_view_mgr->fetch('_email.payment-charge-successful.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLPaymentChargeSuccessfulPro() {
        $headline = "Thanks for joining ThinkUp!";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('member_level', 'Pro' );
        $email_view_mgr->assign('amount', 120 );
        $email_view_mgr->assign('renewal_date', 'January 15, 2015' );
        $email_view_mgr->assign('thinkup_username', 'tyrionlannister' );
        $email_view_mgr->assign('installation_url', 'asdf.thinkup.com');
        $body_html = $email_view_mgr->fetch('_email.payment-charge-successful.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLPaymentChargeFailureNoAdditionalInfo() {
        $headline = "Uh oh! Problem with your ThinkUp payment";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('amazon_error_message', null );
        $body_html = $email_view_mgr->fetch('_email.payment-charge-failure.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLPaymentChargeFailureWithAdditionalInfo() {
        $headline = "Uh oh! Problem with your ThinkUp payment";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('amazon_error_message', "Card has Expired" );
        $body_html = $email_view_mgr->fetch('_email.payment-charge-failure.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }
}