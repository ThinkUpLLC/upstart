<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
require_once dirname(__FILE__) . '/classes/mock.AmazonFPSAPIAccessor.php';

class TestOfMembershipController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
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
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>'Late Bird'));
        return $builders;
    }

    public function testComped() {
        $builders = $this->buildSubscriberWithComplimentaryMembership();
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('comp@example.com');
        $this->setUpInstall($subscriber);

        $this->simulateLogin('comp@example.com');
        $controller = new MembershipController(true);
        $results = $controller->go();
        $this->debug($results);
        $this->assertPattern('/Membership Info/', $results);
        $this->assertPattern('/This is what our database knows./', $results);
        $this->assertPattern('/Complimentary membership/', $results);

        $this->tearDownInstall($subscriber);
    }

    public function testPaidSuccessfully() {
        $builders = $this->buildSubscriberPaid('Success');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
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

        $this->tearDownInstall($subscriber);
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
        return $builders;
    }

    public function testAuthorizationPendingEarlyBird() {
        $builders = $this->buildSubscriberAuthorizationPending('Early Bird');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('authed@example.com');
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

        $this->tearDownInstall($subscriber);
    }

    public function testAuthorizationPendingLateBird() {
        $builders = $this->buildSubscriberAuthorizationPending('Late Bird');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('authed@example.com');
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

        $this->tearDownInstall($subscriber);
    }

    public function testAuthorizationPendingPro() {
        $builders = $this->buildSubscriberAuthorizationPending('Pro');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('authed@example.com');
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

        $this->tearDownInstall($subscriber);
    }

    public function testAuthorizationPendingExec() {
        $builders = $this->buildSubscriberAuthorizationPending('Executive');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('authed@example.com');
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

        $this->tearDownInstall($subscriber);
    }

    private function buildSubscriberPaid($payment_status) {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>'paid@example.com',
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'is_installation_active'=>0, 'date_installed'=>null, 'membership_level'=>'Member'));

        $builders[] = FixtureBuilder::build('payments', array('id'=>100, 'transaction_status'=>$payment_status,
            'timestamp'=>'-10s'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('subscriber_id'=>1, 'payment_id'=>100));
        return $builders;
    }

    public function testPaymentPending() {
        $builders = $this->buildSubscriberPaid('Pending');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
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

        $this->tearDownInstall($subscriber);
    }

    public function testPaymentFailed() {
        $builders = $this->buildSubscriberPaid('Failure');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
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

        $this->tearDownInstall($subscriber);
    }

    public function testInvalidReturnFromAmazonRetriedPayment() {
        $builders = $this->buildSubscriberPaid('Failure');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
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
        $this->assertPattern('/Oops! Something went wrong and our team is looking into it./', $results);
        $paid_through_year = intval(date('Y')) + 1;
        $paid_through_date = date('M j ');
        $this->assertNoPattern('/Paid through/', $results);

        $this->tearDownInstall($subscriber);
    }

    public function testSuccessfulReturnFromAmazonRetriedPayment() {
        $builders = $this->buildSubscriberPaid('Failure');
        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail('paid@example.com');
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

        $this->tearDownInstall($subscriber);
    }
}