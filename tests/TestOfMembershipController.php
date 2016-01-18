<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfMembershipController extends UpstartUnitTestCase {
    /**
     * Persist the subscriber object so that teardown can uninstall/remove the database.
     * @var Subscriber
     */
    protected $subscriber;
    /**
     * Persist the fixture builders so that teardown can uninstall/remove the database.
     * @var arr
     */
    protected $builders;

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        if (isset($this->subscriber)) {
            $this->tearDownInstall($this->subscriber);
        }
        $this->builders = null;
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new MembershipController(true);
        $this->assertIsA($controller, 'MembershipController');
    }

    public function testNotLoggedIn() {
        $_SERVER['REQUEST_URI'] = '/user/membership.php';
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($controller->redirect_destination);
        //should redirect to login with a redirect $_GET param
        $this->assertPattern('/\?redirect=/', $controller->redirect_destination);
        $this->assertPattern('/membership.php/', $controller->redirect_destination);
    }

    private function buildSubscriberWithComplimentaryMembership() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'comp@example.com',
            'is_membership_complimentary'=>1, 'thinkup_username'=>'xanderharris',
            'subscription_status'=>'Complimentary membership',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>'Late Bird'));
        return $builders;
    }

    private function buildSubscriberWithPaymentDue($level = 'Member', $subscription_recurrence = '1 month') {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'due@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'xanderharris',
            'subscription_status'=>'Payment due', 'is_via_recurly'=>1,
            'subscription_recurrence' => $subscription_recurrence,
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>$level));
        return $builders;
    }

    public function testComped() {
        $this->builders = $this->buildSubscriberWithComplimentaryMembership();
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('comp@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('comp@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertPattern('/Complimentary membership/', $results);
        //Show book links
        $this->assertPattern('/Kindle/', $results);
    }

    public function testPaidSuccessfullyAnnualMember() {
        $this->builders = $this->buildSubscriberPaidAnnual('Paid', 'Member');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        //Close account button on annual paid
        $this->assertPattern('/You will receive a refund/', $results);
        $this->assertPattern('/all your data will be deleted./', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j, ');
        $this->assertPattern('/Paid through '.$paid_through_date.$paid_through_year.'/', $results);
        $this->assertPattern('/50 per year/', $results);
        //Show book links
        $this->assertPattern('/Kindle/', $results);
    }

    public function testPaidSuccessfullyAnnualEarlyBird() {
        $this->builders = $this->buildSubscriberPaidAnnual('Paid', 'Early Bird');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        //Close account button
        $this->assertPattern('/You will receive a refund/', $results);
        $this->assertPattern('/all your data will be deleted./', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j, ');
        $this->assertPattern('/Paid through '.$paid_through_date.$paid_through_year.'/', $results);
        $this->assertPattern('/50 per year/', $results);
    }

    public function testPaidSuccessfullyAnnualLateBird() {
        $this->builders = $this->buildSubscriberPaidAnnual('Paid', 'Late Bird');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        //Close account button on annual paid
        $this->assertPattern('/You will receive a refund/', $results);
        $this->assertPattern('/all your data will be deleted./', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j, ');
        $this->assertPattern('/Paid through '.$paid_through_date.$paid_through_year.'/', $results);
        $this->assertPattern('/50 per year/', $results);
    }

    public function testPaidSuccessfullyAnnualPro() {
        $this->builders = $this->buildSubscriberPaidAnnual('Paid', 'Pro');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        //Close account button on annual paid
        $this->assertPattern('/You will receive a refund/', $results);
        $this->assertPattern('/all your data will be deleted./', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j, ');
        $this->assertPattern('/Paid through '.$paid_through_date.$paid_through_year.'/', $results);
        $this->assertPattern('/120 per year/', $results);
        //Show book links
        $this->assertPattern('/Kindle/', $results);
    }

    public function testPaidSuccessfullyAnnualExec() {
        $this->builders = $this->buildSubscriberPaidAnnual('Paid', 'Exec');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        //Close account button on annual paid
        $this->assertPattern('/You will receive a refund/', $results);
        $this->assertPattern('/all your data will be deleted./', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j, ');
        $this->assertPattern('/Paid through '.$paid_through_date.$paid_through_year.'/', $results);
        $this->assertPattern('/996 per year/', $results);
        //Show book links
        $this->assertPattern('/Kindle/', $results);
    }

    public function testPaidSuccessfullyMonthlyMember() {
        $this->builders = $this->buildSubscriberPaidMonthly('Success', 'Member');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        //Show close account button on monthly paid
        $this->assertPattern('/You will receive a refund/', $results);
        $this->assertPattern('/Paid through/', $results);
        $this->assertPattern('/5 per month/', $results);
        //Show book links
        $this->assertPattern('/Kindle/', $results);
    }

    public function testPaidSuccessfullyMonthlyPro() {
        $this->builders = $this->buildSubscriberPaidMonthly('Success', 'Pro');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        //Show close account button on monthly paid
        $this->assertPattern('/You will receive a refund/', $results);
        $this->assertPattern('/Paid through/', $results);
        $this->assertPattern('/10 per month/', $results);
        //Show book links
        $this->assertPattern('/Kindle/', $results);
    }

    public function testPaidSuccessfullyViaClaimCode() {
        $this->builders = $this->buildSubscriberRedeemedClaimCode();
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('codeclaimed@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('codeclaimed@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        //Show close account button
        $this->assertPattern('/This cannot be undone/', $results);
        $this->assertPattern('/All your data will be deleted./', $results);
        //Don't promise a refund
        $this->assertNoPattern('/You will receive a refund/', $results);
        //Do show when code is good until
        $this->assertPattern('/Paid through/', $results);
        //Don't show recurring subscription details
        $this->assertNoPattern('/10 per month/', $results);
        //Show book links
        $this->assertPattern('/Kindle/', $results);
    }

    public function testEbookDownload() {
        $this->builders = $this->buildSubscriberPaidAnnual('Paid');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Download <em>Insights<\/em> ebook/', $results);
        $this->assertPattern('/book.thinkup.com/', $results);
        $this->assertPattern('/insights.pdf/', $results);
        $this->assertPattern('/insights.mobi/', $results);
        $this->assertPattern('/insights.epub/', $results);
    }

    private function buildSubscriberPaidAnnual($payment_status, $membership_level = 'Member') {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'paid@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>$membership_level,
            'subscription_recurrence'=>'12 months', 'paid_through'=>($payment_status == 'Paid')?'+366d':null,
            'subscription_status'=>$payment_status, 'is_via_recurly'=>1));

        if ($payment_status == 'Paid') {
            self::updateSubscriptionStatus(1);
        }
        return $builders;
    }

    private function buildSubscriberPaidAnnualInvalidRecurlyAccountValidSubscription($payment_status,
        $membership_level = 'Member') {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>101, 'email'=>'paid@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>$membership_level,
            'subscription_recurrence'=>'12 months', 'paid_through'=>($payment_status == 'Paid')?'+366d':null,
            'subscription_status'=>$payment_status, 'is_via_recurly'=>1, 'recurly_subscription_id'=>'abc-valid'));

        if ($payment_status == 'Paid') {
            self::updateSubscriptionStatus(1);
        }
        return $builders;
    }

    private function buildSubscriberPaidAnnualInvalidRecurlyAccountInvalidSubscription($payment_status,
        $membership_level = 'Member') {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>101, 'email'=>'paid@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>$membership_level,
            'subscription_recurrence'=>'12 months', 'paid_through'=>($payment_status == 'Paid')?'+366d':null,
            'subscription_status'=>$payment_status, 'is_via_recurly'=>1, 'recurly_subscription_id'=>'abc-invalid'));

        if ($payment_status == 'Paid') {
            self::updateSubscriptionStatus(1);
        }
        return $builders;
    }

    private function buildSubscriberPaidMonthly($payment_status, $membership_level = "Member") {
        $builders = array();
        $days_in_current_month = date("t");
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'paid@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>$membership_level,
            'subscription_recurrence'=>'1 month', 'paid_through'=>"+".date('t')."d",
            'subscription_status'=>'Free trial', 'is_via_recurly'=>1));

        self::updateSubscriptionStatus(1);
        return $builders;
    }

    private function updateSubscriptionStatus($subscriber_id) {
        //tests shouldn't instantiate the DAO, but in the interest of expedience...
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID($subscriber_id);
        $subscription_helper = new SubscriptionHelper();
        $new_values = $subscription_helper->getSubscriptionStatusAndPaidThrough( $subscriber );
        $subscriber_dao->setSubscriptionStatus($subscriber->id, $new_values['subscription_status']);
        $result = $subscriber_dao->setPaidThrough($subscriber->id, $new_values['paid_through']);
    }

    public function testPaymentFailed() {
        $this->builders = $this->buildSubscriberPaidAnnual('Payment failed');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertPattern('/easy to fix/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);
        //Don't show book links
        $this->assertNoPattern('/Kindle/', $results);
        //Show coupon code entry
        $this->assertPattern('/Got a coupon code/', $results);
    }

    public function testPaymentDueMemberMonthly() {
        $this->builders = $this->buildSubscriberWithPaymentDue($level = 'Member', '1 month');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('due@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('due@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertPattern('/Just 5 bucks a month/', $results);
        $this->assertPattern('/Payment due/', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertNoPattern('/easy to fix/', $results);
        //Don't show book link
        $this->assertNoPattern('/Kindle/', $results);
        $this->assertPattern('/One last step/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);
        //Show coupon code entry
        $this->assertPattern('/Got a coupon code/', $results);
    }

    public function testPaymentDueMemberCouponCode() {
        $this->builders = $this->buildSubscriberWithPaymentDue($level = 'Member', 'None');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('due@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('due@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertPattern('/Just 5 bucks a month/', $results);
        $this->assertPattern('/Payment due/', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertNoPattern('/easy to fix/', $results);
        //Don't show book link
        $this->assertNoPattern('/Kindle/', $results);
        $this->assertPattern('/One last step/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);
        //Show coupon code entry
        $this->assertPattern('/Got a coupon code/', $results);
    }

    public function testPaymentDueMemberYearly() {
        $this->builders = $this->buildSubscriberWithPaymentDue($level = 'Member', '12 months');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('due@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('due@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertPattern('/Just 50 bucks a year/', $results);
        $this->assertPattern('/Payment due/', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertNoPattern('/easy to fix/', $results);
        //Don't show book link
        $this->assertNoPattern('/Kindle/', $results);
        $this->assertPattern('/One last step/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);
        //Show coupon code entry
        $this->assertPattern('/Got a coupon code/', $results);
    }

    public function testPaymentDuePro() {
        $this->builders = $this->buildSubscriberWithPaymentDue($level = 'Pro');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('due@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('due@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertPattern('/Payment due/', $results);
        $this->assertPattern('/Just 10 bucks a month/', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertNoPattern('/easy to fix/', $results);
        //Don't show book link
        $this->assertNoPattern('/Kindle/', $results);
        $this->assertPattern('/One last step/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);
        //Show coupon code entry
        $this->assertPattern('/Got a coupon code/', $results);
    }

    private function buildSubscriberFreeTrialCreated($days_ago) {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'trial@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'subscription_status'=>'Free trial',  'is_installation_active'=>0, 'date_installed'=>null,
            'membership_level'=>'Member', 'creation_time'=>"-".(($days_ago*24)+3)."h", 'is_via_recurly'=>0));
        return $builders;
    }

    private function buildSubscriberRedeemedClaimCode($days_ago=2) {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>5, 'email'=>'codeclaimed@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'spike',
            'subscription_status'=>'Paid',  'is_installation_active'=>0, 'date_installed'=>null,
            'membership_level'=>'Member', 'creation_time'=>"-".(($days_ago*24)+3)."h",
            'subscription_recurrence'=>'None', 'claim_code'=>'abcdedg', 'paid_through'=>'+300d',
            'is_via_recurly'=>0 ));
        return $builders;
    }

    public function testFreeTrialNotExpired() {
        $this->builders = $this->buildSubscriberFreeTrialCreated(10);
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('trial@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('trial@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertPattern('/Subscribe Now/', $results);
        $this->assertPattern('/expires in <strong>4 days/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $this->assertNoPattern('/Paid through/', $results);
        $this->assertNoPattern('/You will receive a refund/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        //Don't show book link
        $this->assertNoPattern('/Kindle/', $results);
        //Show coupon code entry
        $this->assertPattern('/Got a coupon code/', $results);
    }

    public function testFreeTrialExpired() {
        $this->builders = $this->buildSubscriberFreeTrialCreated(15);
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('trial@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('trial@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertPattern('/Subscribe Now/', $results);
        $this->assertPattern('/expired!/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $this->assertNoPattern('/Paid through/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        //Don't show book link
        $this->assertNoPattern('/Kindle/', $results);
        //Show coupon code entry
        $this->assertPattern('/Got a coupon code/', $results);
    }

    private function buildSubscriberAccountClosed() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'closed@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'subscription_status'=>'Free trial',  'is_installation_active'=>0, 'date_installed'=>null,
            'membership_level'=>'Member', 'creation_time'=>"-10d", 'is_account_closed'=>1));
        return $builders;
    }

    public function testAccountClosed() {
        $this->builders = $this->buildSubscriberAccountClosed();
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('closed@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('closed@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertNoPattern('/One last step/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $this->assertNoPattern('/Paid through/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        //Don't show book link
        $this->assertNoPattern('/Kindle/', $results);
    }

    public function testCloseAccountValidCSRFFreeTrial() {
        $this->builders = $this->buildSubscriberFreeTrialCreated(2);
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('trial@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('trial@example.com', false, true);

        //Set close account URL param
        $_POST['close'] = 'true';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/Membership Info/', $results);
        $this->assertNoPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertNoPattern('/easy to fix/', $results);
        $this->assertNoPattern('/Success! Thanks for being a ThinkUp member./', $results);
        $this->assertNoPattern('/There was a problem processing your request. Please try again./', $results);
        $this->assertNoPattern('/Your ThinkUp account has been closed. But there\'s still time to change your mind!/',
            $results);
        $this->assertPattern('/Your ThinkUp account is closed. Thanks for trying ThinkUp!/', $results);
        // Don't send account closure email to free trialers or FPS folks, just subscribers
        $closure_email = Mailer::getLastMail();
        $this->assertEqual('', $closure_email);
    }

    public function testCloseAccountValidCSRFWithPaymentSuccess() {
        $this->builders = $this->buildSubscriberPaidAnnual('Success');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com', false, true);

        //Set close account URL param
        $_POST['close'] = 'true';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Your ThinkUp account is closed, and we&#39;ve issued a refund. '.
            'Thanks for trying ThinkUp!/', $results);

        // Send account closure email
        $closure_email = Mailer::getLastMail();
        $this->assertPattern('/Thanks for trying ThinkUp./', $closure_email);
        $this->assertPattern('/Your ThinkUp account is now closed./', $closure_email);
        $this->assertPattern('/We will credit your Amazon Payments account/', $closure_email);
    }

    public function testCloseAccountValidCSRFWithPaymentSuccessInvalidRecurlyAccountIdValidSub() {
        $this->builders = $this->buildSubscriberPaidAnnualInvalidRecurlyAccountValidSubscription('Success');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com', false, true);

        //Set close account URL param
        $_POST['close'] = 'true';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Your ThinkUp account is closed, and we&#39;ve issued a refund. '.
            'Thanks for trying ThinkUp!/', $results);

        // Send account closure email
        $closure_email = Mailer::getLastMail();
        $this->assertPattern('/Thanks for trying ThinkUp./', $closure_email);
        $this->assertPattern('/Your ThinkUp account is now closed./', $closure_email);
        $this->assertPattern('/We will credit your Amazon Payments account/', $closure_email);
    }

    public function testCloseAccountValidCSRFWithPaymentSuccessInvalidRecurlyAccountIdInvalidSub() {
        $this->builders = $this->buildSubscriberPaidAnnualInvalidRecurlyAccountInvalidSubscription('Success');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com', false, true);

        //Set close account URL param
        $_POST['close'] = 'true';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/We had a problem cancelling your subscription./', $results);

        // Send account closure email
        $closure_email = Mailer::getLastMail();
        $this->assertFalse($closure_email);
    }

    public function testCloseAccountValidCSRFWithPaymentFailure() {
        $this->builders = $this->buildSubscriberPaidAnnual('Payment failed');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com', false, true);

        //Set close account URL param
        $_POST['close'] = 'true';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Your ThinkUp account is closed./', $results);

        // Send account closure email
        $closure_email = Mailer::getLastMail();
        $this->assertPattern('/Thanks for trying ThinkUp./', $closure_email);
        $this->assertPattern('/Your ThinkUp account is now closed./', $closure_email);
        $this->assertPattern('/We will credit your Amazon Payments account/', $closure_email);
    }

    public function testCloseAccountInvalidCSRF() {
        $this->builders = $this->buildSubscriberFreeTrialCreated(2);
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('trial@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('trial@example.com');

        //Set close account URL param
        $_POST['close'] = 'true';

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertNoPattern('/easy to fix/', $results);
        $this->assertNoPattern('/Success! Thanks for being a ThinkUp member./', $results);
        $this->assertPattern('/There was a problem processing your request. Please try again./', $results);
    }

    public function testCloseAccountAlreadyClosedValidCSRF() {
        $this->builders = $this->buildSubscriberFreeTrialCreated(2);
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('trial@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);
        $dao->closeAccount($subscriber->id);

        $this->simulateLogin('trial@example.com', false, true);

        //Set close account URL param
        $_POST['close'] = 'true';
        $_POST['csrf_token'] = parent::CSRF_TOKEN;

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertNoPattern('/easy to fix/', $results);
        $this->assertNoPattern('/Success! Thanks for being a ThinkUp member./', $results);
        $this->assertPattern('/This account is already closed. Please log out./', $results);
        $this->assertNoPattern('/Your ThinkUp account has been closed. But there\'s still time to change your mind!/',
            $results);
    }

    public function testInvalidClaimCode() {
        $this->builders = $this->buildSubscriberFreeTrialCreated(10);
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('trial@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);
        $this->simulateLogin('trial@example.com', false, true);
        $_POST['claim_code'] = 'asdfasd';

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/That code doesn&#39;t seem right. Check it and try again?/', $results);
        $this->assertNoPattern('/Whoops! It looks like that code has already been used/', $results);
        $this->assertNoPattern('/It worked! We&#39;ve applied your coupon code./', $results);
        $this->assertNoPattern('/Oops! There was a problem processing your code. Please try again./', $results);
    }

    public function testRedeemedClaimCode() {
        $this->builders = $this->buildSubscriberFreeTrialCreated(10);
        $this->builders[] = FixtureBuilder::build('claim_codes', array('code'=>'1234567890AB', 'is_redeemed'=>1));

        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('trial@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);
        $this->simulateLogin('trial@example.com', false, true);
        $_POST['claim_code'] = '1234567890AB';

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/That code doesn&#39;t seem right. Check it and try again?/', $results);
        $this->assertPattern('/Whoops! It looks like that code has already been used/', $results);
        $this->assertNoPattern('/It worked! We&#39;ve applied your coupon code./', $results);
        $this->assertNoPattern('/Oops! There was a problem processing your code. Please try again./', $results);
        //Close account button shouldn't promise a refund for coupon codes
        $this->assertNoPattern('/You will receive a refund/', $results);

        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM thinkupstart_'. $subscriber->thinkup_username
            . '.tu_owners o WHERE o.email = "paid@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['is_free_trial'], 0);
    }

    public function testValidClaimCodeWithSpacesLowercase() {
        $this->builders = $this->buildSubscriberFreeTrialCreated(10);
        $this->builders[] = FixtureBuilder::build('claim_codes', array('code'=>'1234567890AB', 'is_redeemed'=>0));

        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('trial@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);
        $this->simulateLogin('trial@example.com', false, true);
        $_POST['claim_code'] = '12345  67890a b ';

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertNoPattern('/That code doesn&#39;t seem right. Check it and try again?/', $results);
        $this->assertNoPattern('/Whoops! It looks like that code has already been used/', $results);
        $this->assertPattern('/It worked! We&#39;ve applied your coupon code./', $results);
        $this->assertNoPattern('/Oops! There was a problem processing your code. Please try again./', $results);
        $this->assertNoPattern('/Pay now with Amazon/', $results);
        $this->assertNoPattern('/Free trial that expires/', $results);

        $stmt = ThinkUpPDODAO::$PDO->query('SELECT o.* FROM thinkupstart_'. $subscriber->thinkup_username
            . '.tu_owners o WHERE o.email = "paid@example.com"');
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEqual($row['is_free_trial'], 0);
    }
}