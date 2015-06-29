<?php
require_once dirname(__FILE__) . '/init.tests.php';

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

    private function createSystemMessageHTMLPaymentReminderFreeTrial1Through4($membership_level = 'Member') {
        $cfg = Config::getInstance();
        $headline = "Join ThinkUp and get your FREE gift!";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $thinkup_username = 'tyrionlannister';
        $user_installation_url = str_replace('{user}', $thinkup_username,
            $cfg->getValue('user_installation_url'));
        $email_view_mgr->assign('thinkup_url', $user_installation_url );
        $email_view_mgr->assign('membership_level', $membership_level);
        $message = $email_view_mgr->fetch('_email.payment-reminder-trial-1.tpl');
        $this->debug($message);

        $headline = "Enjoying ThinkUp? Join and get even more...";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $thinkup_username = 'tyrionlannister';
        $user_installation_url = str_replace('{user}', $thinkup_username,
            $cfg->getValue('user_installation_url'));
        $email_view_mgr->assign('thinkup_url', $user_installation_url );
        $email_view_mgr->assign('membership_level', $membership_level);
        $message = $email_view_mgr->fetch('_email.payment-reminder-trial-2.tpl');
        $this->debug($message);

        $headline = "One day left: Ready to join ThinkUp?";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $thinkup_username = 'tyrionlannister';
        $user_installation_url = str_replace('{user}', $thinkup_username,
            $cfg->getValue('user_installation_url'));
        $email_view_mgr->assign('thinkup_url', $user_installation_url );
        $email_view_mgr->assign('membership_level', $membership_level);
        $message = $email_view_mgr->fetch('_email.payment-reminder-trial-3.tpl');
        $this->debug($message);

        $headline = "Your ThinkUp free trial ends TODAY. Join now!";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $thinkup_username = 'tyrionlannister';
        $user_installation_url = str_replace('{user}', $thinkup_username,
            $cfg->getValue('user_installation_url'));
        $email_view_mgr->assign('thinkup_url', $user_installation_url );
        $email_view_mgr->assign('membership_level', $membership_level);
        $message = $email_view_mgr->fetch('_email.payment-reminder-trial-4.tpl');
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLPaymentReminderFreeTrial1Through4Member() {
        $this->createSystemMessageHTMLPaymentReminderFreeTrial1Through4($membership_level = 'Member');
    }

    public function testGetSystemMessageHTMLPaymentReminderFreeTrial1Through4Pro() {
        $this->createSystemMessageHTMLPaymentReminderFreeTrial1Through4($membership_level = 'Pro');
    }

    public function testGetSystemMessageHTMLReupReminderOne() {
        $cfg = Config::getInstance();
        $headline = "Your first year of ThinkUp";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $thinkup_username = 'tyrionlannister';
        $user_installation_url = str_replace('{user}', $thinkup_username, $cfg->getValue('user_installation_url'));
        $email_view_mgr->assign('thinkup_url', $user_installation_url );
        $body_html = $email_view_mgr->fetch('_email.annual-reup-notification-1.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLReupReminderTwo() {
        $cfg = Config::getInstance();
        $headline = "Your ThinkUp membership is about to renew";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $thinkup_username = 'tyrionlannister';
        $user_installation_url = str_replace('{user}', $thinkup_username, $cfg->getValue('user_installation_url'));
        $email_view_mgr->assign('thinkup_url', $user_installation_url );
        $body_html = $email_view_mgr->fetch('_email.annual-reup-notification-2.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLFPSTransitionReminderOne() {
        $cfg = Config::getInstance();
        $headline = "Time to renew your ThinkUp membership";
        $headline2 = "Renew your ThinkUp subscription and get 2 months free";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $thinkup_username = 'tyrionlannister';
        $user_installation_url = str_replace('{user}', $thinkup_username, $cfg->getValue('user_installation_url'));
        $email_view_mgr->assign('thinkup_url', $user_installation_url );
        $email_view_mgr->assign('original_subscription_date', 'July 3, 2014' );

        //Member who is getting discount
        $email_view_mgr->assign('is_getting_discount', true );
        $email_view_mgr->assign('member_level', 'Member' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-1.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 2 days before renewal due<br><br>Subject: ".$headline."<br><br>Alt Subject: "
            .$headline2."</h2>");
        $this->assertPattern('/2 MONTHS FREE/', $message);
        $this->assertNoPattern('/only \$100/', $message);
        $this->assertPattern('/only \$50/', $message);
        $this->assertPattern('/On July 3, 2014 you purchased/', $message);
        $this->debug($message);

        //Member who is not getting discount
        $email_view_mgr->assign('is_getting_discount', false );
        $email_view_mgr->assign('member_level', 'Member' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-1.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 2 days before renewal due<br><br>Subject: ".$headline."<br><br>Alt Subject: "
            .$headline2."</h2>");
        $this->assertNoPattern('/2 MONTHS FREE/', $message);
        $this->assertNoPattern('/only \$100/', $message);
        $this->assertNoPattern('/only \$50/', $message);
        $this->assertPattern('/On July 3, 2014 you purchased/', $message);
        $this->debug($message);

        //Pro who is getting discount
        $email_view_mgr->assign('is_getting_discount', true );
        $email_view_mgr->assign('member_level', 'Pro' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-1.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 2 days before renewal due<br><br>Subject: ".$headline."<br><br>Alt Subject: "
            .$headline2."</h2>");
        $this->assertPattern('/2 MONTHS FREE/', $message);
        $this->assertNoPattern('/only \$50/', $message);
        $this->assertPattern('/only \$100/', $message);
        $this->assertPattern('/On July 3, 2014 you purchased/', $message);
        $this->debug($message);

        //Pro who is not getting discount
        $email_view_mgr->assign('is_getting_discount', false );
        $email_view_mgr->assign('member_level', 'Pro' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-1.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 2 days before renewal due<br><br>Subject: ".$headline."<br><br>Alt Subject: "
            .$headline2."</h2>");
        $this->assertNoPattern('/2 MONTHS FREE/', $message);
        $this->assertNoPattern('/only \$50/', $message);
        $this->assertNoPattern('/only \$100/', $message);
        $this->assertPattern('/On July 3, 2014 you purchased/', $message);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLFPSTransitionReminderTwo() {
        $cfg = Config::getInstance();
        $headline = "Action required: Update your ThinkUp payment info";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $thinkup_username = 'tyrionlannister';
        $user_installation_url = str_replace('{user}', $thinkup_username, $cfg->getValue('user_installation_url'));
        $email_view_mgr->assign('thinkup_url', $user_installation_url );

        //Member who is getting discount
        $email_view_mgr->assign('is_getting_discount', true );
        $email_view_mgr->assign('member_level', 'Member' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-2.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 7 days after renewal due<br><br>Subject: ".$headline."</h2>");
        $this->assertPattern('/save \$10/', $message);
        $this->assertNoPattern('/save \$20/', $message);
        $this->debug($message);

        //Member who is not getting discount
        $email_view_mgr->assign('is_getting_discount', false );
        $email_view_mgr->assign('member_level', 'Member' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-2.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 7 days after renewal due<br><br>Subject: ".$headline."</h2>");
        $this->assertNoPattern('/save \$10/', $message);
        $this->assertNoPattern('/save \$20/', $message);
        $this->debug($message);

        //Pro who is getting discount
        $email_view_mgr->assign('is_getting_discount', true );
        $email_view_mgr->assign('member_level', 'Pro' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-2.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 7 days after renewal due<br><br>Subject: ".$headline."</h2>");
        $this->assertNoPattern('/save \$10/', $message);
        $this->assertPattern('/save \$20/', $message);
        $this->debug($message);

        //Pro who is not getting discount
        $email_view_mgr->assign('is_getting_discount', false );
        $email_view_mgr->assign('member_level', 'Pro' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-2.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 7 days after renewal due<br><br>Subject: ".$headline."</h2>");
        $this->assertNoPattern('/save \$10/', $message);
        $this->assertNoPattern('/save \$20/', $message);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLFPSTransitionReminderThree() {
        $cfg = Config::getInstance();
        $headline = "LAST CHANCE to save your ThinkUp account";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $thinkup_username = 'tyrionlannister';
        $user_installation_url = str_replace('{user}', $thinkup_username, $cfg->getValue('user_installation_url'));
        $email_view_mgr->assign('thinkup_url', $user_installation_url );

        //Member who is getting discount
        $email_view_mgr->assign('is_getting_discount', true );
        $email_view_mgr->assign('member_level', 'Member' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-3.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 3 weeks after renewal due<br><br>Subject: ".$headline."</h2>");
        $this->assertPattern('/save \$10/', $message);
        $this->assertNoPattern('/save \$20/', $message);
        $this->debug($message);

        //Member who is not getting discount
        $email_view_mgr->assign('is_getting_discount', false );
        $email_view_mgr->assign('member_level', 'Member' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-3.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 3 weeks after renewal due<br><br>Subject: ".$headline."</h2>");
        $this->assertNoPattern('/save \$10/', $message);
        $this->assertNoPattern('/save \$20/', $message);
        $this->debug($message);

        //Pro who is getting discount
        $email_view_mgr->assign('is_getting_discount', true );
        $email_view_mgr->assign('member_level', 'Pro' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-3.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 3 weeks after renewal due<br><br>Subject: ".$headline."</h2>");
        $this->assertNoPattern('/save \$10/', $message);
        $this->assertPattern('/save \$20/', $message);
        $this->debug($message);

        //Pro who is not getting discount
        $email_view_mgr->assign('is_getting_discount', false);
        $email_view_mgr->assign('member_level', 'Pro' );
        $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-3.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
        $this->debug("<h2>When: 3 weeks after renewal due<br><br>Subject: ".$headline."</h2>");
        $this->assertNoPattern('/save \$10/', $message);
        $this->assertNoPattern('/save \$20/', $message);
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

    public function testGetSystemMessageHTMLAccountHasBeenClosedWithNonZeroRefundAmount() {
        $headline = "Thanks for trying ThinkUp.";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('refund_amount', '2.50' );
        $body_html = $email_view_mgr->fetch('_email.account-closed.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }

    public function testGetSystemMessageHTMLAccountHasBeenClosedWithZeroRefundAmount() {
        $headline = "Thanks for trying ThinkUp.";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('refund_amount', '0.00' );
        $body_html = $email_view_mgr->fetch('_email.account-closed.tpl');
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        $this->debug($message);
    }
}