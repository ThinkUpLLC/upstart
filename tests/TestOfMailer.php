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

    public function testGetSystemMessageHTMLPaymentReminder1() {
        $headline = "Lock in your ThinkUp membership";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'username' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-first.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLPaymentReminder2() {
        $headline = "One quick step needed to keep your ThinkUp account";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'username' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-second.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLPaymentReminder3() {
        $headline = "We don’t want to say goodbye so soon!";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'username' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-third.tpl');
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
        $email_view_mgr->assign('thinkup_username', 'username' );
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
        $email_view_mgr->assign('amount', 60 );
        $email_view_mgr->assign('renewal_date', 'January 15, 2015' );
        $email_view_mgr->assign('thinkup_username', 'username' );
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

    public function testGetSystemMessageHTMLPaymentReminder4() {
        $headline = "We don’t want to say goodbye so soon!";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('thinkup_username', 'username' );
        $body_html = $email_view_mgr->fetch('_email.payment-reminder-fourth.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }
}