<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
require_once dirname(__FILE__) . '/classes/mock.AmazonFPSAPIAccessor.php';

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
    }

    public function testPaidSuccessfully() {
        $this->builders = $this->buildSubscriberPaid('Success');
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
        $this->assertPattern('/You will receive a refund/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j, ');
        $this->assertPattern('/Paid through '.$paid_through_date.$paid_through_year.'/', $results);
    }

    public function testEbookDownload() {
        $this->builders = $this->buildSubscriberPaid('Success');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Download the <em>Insights<\/em> book/', $results);
        $this->assertPattern('/book.thinkup.com/', $results);
        $this->assertPattern('/insights.pdf/', $results);
        $this->assertPattern('/insights.mobi/', $results);
        $this->assertPattern('/insights.epub/', $results);
    }

    private function buildSubscriberPaid($payment_status) {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'paid@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>'Member',
            'subscription_recurrence'=>'12 months'));

        $builders[] = FixtureBuilder::build('payments', array('id'=>100, 'transaction_status'=>$payment_status,
            'timestamp'=>'-10s'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>100));

        $this->updateSubscriptionStatus(1);
        return $builders;
    }

    private function updateSubscriptionStatus($subscriber_id) {
        //tests shouldn't instantiate the DAO, but in the interest of expedience...
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber_dao->updateSubscriptionStatus( $subscriber_id);
    }

    public function testPaymentPending() {
        $this->builders = $this->buildSubscriberPaid('Pending');
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
        $this->assertNoPattern('/easy to fix/', $results);
        $this->assertPattern('/Payment pending/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);
    }

    public function testPaymentFailed() {
        $this->builders = $this->buildSubscriberPaid('Failure');
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
    }

    private function buildSubscriberFreeTrialCreated($days_ago) {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'trial@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'subscription_status'=>'Free trial',  'is_installation_active'=>0, 'date_installed'=>null,
            'membership_level'=>'Member', 'creation_time'=>"-".(($days_ago*24)+3)."h"));
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
        $this->assertPattern('/Pay now/', $results);
        $this->assertPattern('/expires in <strong>3 days/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $this->assertNoPattern('/Paid through/', $results);
        $this->assertNoPattern('/You will receive a refund/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
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
        $this->assertPattern('/Pay now/', $results);
        $this->assertPattern('/Expired!/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $this->assertNoPattern('/Paid through/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
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
    }

    public function testInvalidReturnFromAmazonRetriedPayment() {
        $this->builders = $this->buildSubscriberPaid('Failure');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');

        //Set URL params Amazon would return
        $_GET['status'] = 'SS';
        $_GET['certificateUrl'] = "certificate1";
        $_GET['signatureMethod'] = "SHA";
        $_GET['signature'] = 'signature1';
        $_GET['paymentReason'] = "ThinkUp.com membership";
        $_GET['transactionAmount'] = 'USD 5';
        $_GET['referenceId'] = '24_34390d';
        $_GET['subscriptionId'] = '5a81c762-f3ff-4319-9e07-007fffe7d4da';
        $_GET['transactionDate'] = '1407173277';
        $_GET['buyerName'] = 'Angelina Jolie';
        $_GET['buyerEmail'] = 'angelina@example.com';
        $_GET['operation'] = 'pay';
        $_GET['recurringFrequency'] = '1 month';
        $_GET['paymentMethod'] = 'Credit Card';

        //Set this to force the Mock to return false
        $_GET['signatureValidity'] = false;

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertPattern('/easy to fix/', $results);
        $this->assertPattern('/Oops! Something went wrong./', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);
    }

    public function testSuccessfulReturnFromAmazonRetriedPayment() {
        $this->builders = $this->buildSubscriberPaid('Failure');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');

        //Set URL params Amazon would return
        $_GET['status'] = 'SS';
        $_GET['certificateUrl'] = "certificate1";
        $_GET['signatureMethod'] = "SHA";
        $_GET['signature'] = 'signature1';
        $_GET['paymentReason'] = "ThinkUp.com membership";
        $_GET['transactionAmount'] = 'USD 5';
        $_GET['referenceId'] = '24_34390d';
        $_GET['subscriptionId'] = '5a81c762-f3ff-4319-9e07-007fffe7d4da';
        $_GET['transactionDate'] = '1407173277';
        $_GET['buyerName'] = 'Angelina Jolie';
        $_GET['buyerEmail'] = 'angelina@example.com';
        $_GET['operation'] = 'pay';
        $_GET['recurringFrequency'] = '1 month';
        $_GET['paymentMethod'] = 'Credit Card';

        SessionCache::put('caller_reference', 'test-caller-reference');

        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertNoPattern('/easy to fix/', $results);
        $this->assertPattern('/Success! Thanks for being a ThinkUp member./', $results);
        $this->assertNoPattern('/Oops! Something went wrong and our team is looking into it./', $results);
    }

    public function testCloseAccountValidCSRFNoSubscriptionOperation() {
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
        // Don't send account closure email to free trialers, just subscribers
        $closure_email = Mailer::getLastMail();
        $this->assertEqual('', $closure_email);
    }

    public function testCloseAccountValidCSRFWithSubscriptionOperation() {
        $this->builders = $this->buildSubscriberFreeTrialCreated(2);
        $this->builders[] = FixtureBuilder::build('subscription_operations',
            array('amazon_subscription_id'=>'test-sub-id', 'subscriber_id'=> 1, 'recurring_frequency'=>'1 month',
            'transaction_amount'=>'USD 5', 'operation'=>'pay' ));
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
        $this->assertPattern('/Your ThinkUp account is closed, and we&#39;ve issued a refund.  '.
            'Thanks for trying ThinkUp!/', $results);

        $closure_email = Mailer::getLastMail();
        $this->assertPattern('/COPY AND DESIGN GOES HERE/', $closure_email);
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
}