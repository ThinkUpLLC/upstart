<?php
require_once dirname(__FILE__) . '/init.tests.php';

class TestOfCheckoutController extends UpstartUnitTestCase {
    /**
     * Currently installed subscriber.
     * @var Subscriber
     */
    var $subscriber;

    /**
     * Currently built data fixtures.
     * @var array
     */
    var $builders;

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        if (isset($this->subscriber)) {
            $this->tearDownInstall($this->subscriber);
        }
        $this->builders = null;
        //SessionCache::clearAllKeys();
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new CheckoutController(true);
        $this->assertIsA($controller, 'CheckoutController');
    }

    public function testNotLoggedIn() {
        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertPattern( '/Log in/', $result );
        $this->assertNoPattern( '/Check out/', $result );
    }

    public function testCheckoutWhenAlreadyPaid() {
        $this->builders = $this->buildSubscriberAndLogIn($email='paid@example.com', $subscription_status='Paid');

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertNoPattern('/Log in/', $result );
        $this->assertNoPattern('/Checkout/', $result );
        $this->assertNoPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertNoPattern('/Select your plan:/', $result );
        $this->assertNoPattern("/It\'s safe and easy with your Amazon account./", $result );
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        //Should drop user off at Membership page
        $this->assertPattern('/Membership Info/', $result );
        $this->assertPattern('/This is what our database knows./', $result );

        $this->debug($result);
    }

    public function testSignupTrialPay() {
        $this->builders = $this->buildSubscriberAndLogIn($email='trial@example.com', $subscription_status='Free trial');

        SessionCache::put('new_subscriber_id', 1);

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertNoPattern('/Log in/', $result );
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertPattern('/Select your plan:/', $result );
        $this->assertPattern('/\$5/', $result );
        $this->assertPattern('/\$50/', $result );
        $this->assertPattern("/It\'s safe and easy with your Amazon account./", $result );
        $this->assertPattern("/No thanks, I\'ll do this later./", $result );

        $this->debug($result);
    }

    public function testSignupTrialSuccess() {
        $this->builders = $this->buildSubscriberAndLogIn($email='trial@example.com', $subscription_status='Free trial');

        SessionCache::put('new_subscriber_id', 1);

        $_POST['amazon_billing_agreement_id']  = 'billing-id-success';
        $_POST['plan'] = 'member-monthly';

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Thanks! Your payment is complete./', $result );
        $this->assertPattern('/Go to your ThinkUp/', $result );
        $this->assertPattern('/Your insights are almost ready/', $result );

        $this->assertNoPattern('/Log in/', $result );
        $this->assertNoPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertNoPattern('/Select your plan:/', $result );
        $this->assertNoPattern("/It\'s safe and easy with your Amazon account./", $result );
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        $this->debug($result);
    }

    public function testSignupTrialError() {
        $this->builders = $this->buildSubscriberAndLogIn($email='trial@example.com', $subscription_status='Free trial');

        $_POST['amazon_billing_agreement_id']  = 'billing-id-error';
        $_POST['plan'] = 'member-monthly';

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Whoops, sorry!/', $result );
        $this->assertPattern('/There was problem processing your payment. Please try again./', $result );
        $this->assertPattern('/Select your plan:/', $result );
        $this->assertPattern("/No thanks, I\'ll do this later./", $result );

        $this->assertNoPattern('/Thanks! Your payment is complete./', $result );
        $this->assertNoPattern('/Go to your ThinkUp/', $result );
        $this->assertNoPattern('/Log in/', $result );
        $this->assertNoPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertNoPattern("/It\'s safe and easy with your Amazon account./", $result );

        $this->debug($result);
    }

    //@TODO Waiting on Recurly support ticket - unable to trigger this error just yet
    // public function testSignupTrialErrorLastName() {
    // }

    public function testMembershipTrialPay() {
        $this->builders = $this->buildSubscriberAndLogIn($email='trial@example.com', $subscription_status='Free trial');

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertNoPattern('/Log in/', $result );
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertPattern('/Select your plan:/', $result );
        $this->assertPattern("/It\'s safe and easy with your Amazon account./", $result );
        $this->assertPattern("/No thanks, I\'ll do this later./", $result );

        $this->debug($result);
    }

    public function testMembershipTrialSuccess() {
        $this->builders = $this->buildSubscriberAndLogIn($email='trial@example.com', $subscription_status='Free trial');

        $_POST['amazon_billing_agreement_id']  = 'billing-id-success';
        $_POST['plan'] = 'member-monthly';

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Thanks! Your payment is complete./', $result );
        $this->assertPattern('/Go to your ThinkUp/', $result );

        //Didn't just start the free trial, so don't say 'almost ready'
        $this->assertNoPattern('/Your insights are almost ready/', $result );

        $this->assertNoPattern('/Log in/', $result );
        $this->assertNoPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertNoPattern('/Select your plan:/', $result );
        $this->assertNoPattern("/It\'s safe and easy with your Amazon account./", $result );
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        $this->debug($result);
    }

    public function testMembershipTrialError() {
        $this->builders = $this->buildSubscriberAndLogIn($email='trial@example.com', $subscription_status='Free trial');

        $_POST['amazon_billing_agreement_id']  = 'billing-id-error';
        $_POST['plan'] = 'member-monthly';

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Whoops, sorry!/', $result );
        $this->assertPattern('/There was problem processing your payment. Please try again./', $result );
        $this->assertPattern('/Select your plan:/', $result );
        $this->assertPattern("/No thanks, I\'ll do this later./", $result );

        $this->assertNoPattern('/Thanks! Your payment is complete./', $result );
        $this->assertNoPattern('/Go to your ThinkUp/', $result );
        $this->assertNoPattern('/Log in/', $result );
        $this->assertNoPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertNoPattern("/It\'s safe and easy with your Amazon account./", $result );

        $this->debug($result);
    }

    //@TODO Waiting on Recurly support ticket - unable to trigger this error just yet
    // public function testMembershipTrialErrorLastName() {
    // }

    public function testMembershipExpiredTrialPay() {
        $this->builders = $this->buildSubscriberAndLogIn($email='expired@example.com',
            $subscription_status='Free trial', $created_days_ago = 15);

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertNoPattern('/Log in/', $result );
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertPattern('/Select your plan:/', $result );
        $this->assertPattern("/It\'s safe and easy with your Amazon account./", $result );

        //Don't give the option to delay
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        $this->debug($result);
    }

    public function testMembershipExpiredTrialSuccess() {
        $this->builders = $this->buildSubscriberAndLogIn($email='trial@example.com', $subscription_status='Free trial');

        $_POST['amazon_billing_agreement_id']  = 'billing-id-success';
        $_POST['plan'] = 'member-monthly';

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Thanks! Your payment is complete./', $result );
        $this->assertPattern('/Go to your ThinkUp/', $result );

        //Didn't just start the free trial, so don't say 'almost ready'
        $this->assertNoPattern('/Your insights are almost ready/', $result );

        $this->assertNoPattern('/Log in/', $result );
        $this->assertNoPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertNoPattern('/Select your plan:/', $result );
        $this->assertNoPattern("/It\'s safe and easy with your Amazon account./", $result );
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        $this->debug($result);
    }

    public function testMembershipExpiredTrialError() {
        $this->builders = $this->buildSubscriberAndLogIn($email='trial@example.com', $subscription_status='Free trial',
            $created_days_ago = 15);

        $_POST['amazon_billing_agreement_id']  = 'billing-id-error';
        $_POST['plan'] = 'member-monthly';

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Whoops, sorry!/', $result );
        $this->assertPattern('/There was problem processing your payment. In order to keep your account in good '.
            'standing, please try again. If you get stuck/', $result );
        $this->assertPattern('/Select your plan:/', $result );

        //Trial has expired, don't let user delay
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        $this->assertNoPattern('/Thanks! Your payment is complete./', $result );
        $this->assertNoPattern('/Go to your ThinkUp/', $result );
        $this->assertNoPattern('/Log in/', $result );
        $this->assertNoPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertNoPattern("/It\'s safe and easy with your Amazon account./", $result );

        $this->debug($result);
    }

    //@TODO Waiting on Recurly support ticket - unable to trigger this error just yet
    // public function testMembershipExpiredTrialErrorLastName() {
    // }

    public function testProMembershipPaymentFailedPay() {
        $this->builders = $this->buildSubscriberAndLogIn($email='pro-failed-payment@example.com',
            $subscription_status='Payment failed', null, 'Pro');

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertNoPattern('/Log in/', $result );
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertPattern('/Select your plan:/', $result );
        $this->assertPattern('/\$10/', $result );
        $this->assertPattern('/\$100/', $result );
        $this->assertPattern("/It\'s safe and easy with your Amazon account./", $result );

        //Don't allow user to delay
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        $this->debug($result);
    }

    public function testEarlyBirdMembershipPaymentFailedPay() {
        $this->builders = $this->buildSubscriberAndLogIn($email='earlybird-failed-payment@example.com',
            $subscription_status='Payment failed', null, 'Early Bird');

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertNoPattern('/Log in/', $result );
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertPattern('/Select your plan:/', $result );
        $this->assertPattern('/\$5/', $result );
        $this->assertPattern('/\$50/', $result );
        $this->assertPattern("/It\'s safe and easy with your Amazon account./", $result );

        //Don't allow user to delay
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        $this->debug($result);
    }

    public function testLateBirdMembershipPaymentFailedPay() {
        $this->builders = $this->buildSubscriberAndLogIn($email='earlybird-failed-payment@example.com',
            $subscription_status='Payment failed', null, 'Late Bird');

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertNoPattern('/Log in/', $result );
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertPattern('/Select your plan:/', $result );
        $this->assertPattern('/\$5/', $result );
        $this->assertPattern('/\$50/', $result );
        $this->assertPattern("/It\'s safe and easy with your Amazon account./", $result );

        //Don't allow user to delay
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        $this->debug($result);
    }

    public function testProMembershipPaymentFailedError() {
        $this->builders = $this->buildSubscriberAndLogIn($email='pro-failed-payment@example.com',
            $subscription_status='Payment failed', null, 'Pro');

        $_POST['amazon_billing_agreement_id']  = 'billing-id-error';
        $_POST['plan'] = 'member-monthly';

        $controller = new CheckoutController(true);
        $result = $controller->go();
        $this->assertPattern('/Checkout/', $result );
        $this->assertPattern('/Whoops, sorry!/', $result );
        $this->assertPattern('/There was problem processing your payment. In order to keep your account in good '.
            'standing, please try again. If you get stuck/', $result );
        $this->assertPattern('/Select your plan:/', $result );
        $this->assertPattern('/\$10/', $result );
        $this->assertPattern('/\$100/', $result );

        //Last payment failed, this payment is due, don't let user delay
        $this->assertNoPattern("/No thanks, I\'ll do this later./", $result );

        $this->assertNoPattern('/Thanks! Your payment is complete./', $result );
        $this->assertNoPattern('/Go to your ThinkUp/', $result );
        $this->assertNoPattern('/Log in/', $result );
        $this->assertNoPattern('/Subscribe to ThinkUp today!/', $result );
        $this->assertNoPattern("/It\'s safe and easy with your Amazon account./", $result );

        $this->debug($result);
    }

    private function buildSubscriberAndLogIn($email, $subscription_status, $created_days_ago = null,
        $membership_level = 'Member') {
        if (!isset($created_days_ago)) {
            $creation_time = '-1h';
        } else {
            $creation_time = '-'.($created_days_ago*24).'h';
        }
        $builders = array();
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'email'=>$email,
            'is_membership_complimentary'=>0, 'thinkup_username'=>'willowrosenberg',
            'subscription_status'=>$subscription_status,  'is_installation_active'=>0, 'date_installed'=>null,
            'membership_level'=>$membership_level, 'creation_time'=>$creation_time, 'is_via_recurly'=>0));

        $dao = new SubscriberMySQLDAO();
        $subscriber = $dao->getByEmail($email);
        $this->subscriber = $subscriber;
        $this->setUpInstall($subscriber);

        $this->simulateLogin($email);
        return $builders;
    }
}