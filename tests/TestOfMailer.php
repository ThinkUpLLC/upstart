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