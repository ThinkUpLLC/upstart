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
        $this->assertPattern('/Download your copy of/', $results);
        $this->assertPattern('/book.thinkup.com/', $results);
        $this->assertPattern('/insights.pdf/', $results);
        $this->assertPattern('/insights.mobi/', $results);
        $this->assertPattern('/insights.epub/', $results);
    }

    private function buildSubscriberAuthorizationPending($level) {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'authed@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'buffysummers',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>$level));

        $builders[] = FixtureBuilder::build('authorizations', array('id'=>100, 'timestamp'=>'-0s', 'amount'=>50,
            'error_message'=>null));
        $builders[] = FixtureBuilder::build('subscriber_authorizations', array('subscriber_id'=>1,
            'authorization_id'=>100));

        $this->updateSubscriptionStatus(1);
        return $builders;
    }

    public function testAuthorizationPendingEarlyBird() {
        $this->builders = $this->buildSubscriberAuthorizationPending('Early Bird');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('authed@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('authed@example.com');
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

    public function testAuthorizationPendingLateBird() {
        $this->builders = $this->buildSubscriberAuthorizationPending('Late Bird');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('authed@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('authed@example.com');
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

    public function testAuthorizationPendingPro() {
        $this->builders = $this->buildSubscriberAuthorizationPending('Pro');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('authed@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('authed@example.com');
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

    public function testAuthorizationPendingExec() {
        $this->builders = $this->buildSubscriberAuthorizationPending('Executive');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('authed@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('authed@example.com');
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

    private function buildSubscriberPaid($payment_status) {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'paid@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>'Member'));

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

    private function buildSubscriberPaymentDue() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'due@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>'Member'));
        return $builders;
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

    public function testPaymentDue() {
        $this->builders = $this->buildSubscriberPaymentDue();
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
        $this->assertNoPattern('/Complimentary membership/', $results);
        $this->assertPattern('/One last step/', $results);
        $this->assertPattern('/Payment due/', $results);
        $this->assertNoPattern('/Payment pending/', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);
    }

    public function testInvalidReturnFromAmazonRetriedPayment() {
        $this->builders = $this->buildSubscriberPaid('Failure');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin('paid@example.com');

        //Set URL params Amazon would return
        $_GET['callerReference'] = 'test-caller-reference';
        $_GET['tokenID'] = 'test-token-id';
        $_GET['status'] = 'test-status';
        $_GET['certificateUrl'] = 'test-certificate-url';
        $_GET['signatureMethod'] = 'test-signature-method';
        $_GET['signature'] = 'test-signature';

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
        $_GET['callerReference'] = 'test-caller-reference';
        $_GET['tokenID'] = 'test-token-id';
        $_GET['status'] = 'SC';
        $_GET['certificateUrl'] = 'test-certificate-url';
        $_GET['signatureMethod'] = 'test-signature-method';
        $_GET['signature'] = 'test-signature';

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
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);
    }
}