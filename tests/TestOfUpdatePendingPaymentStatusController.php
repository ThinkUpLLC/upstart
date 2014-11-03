<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
require_once dirname(__FILE__) . '/classes/mock.AmazonFPSAPIAccessor.php';

class TestOfUpdatePendingPaymentStatusController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new UpdatePendingPaymentStatusController(true);
        $this->assertIsA($controller, 'UpdatePendingPaymentStatusController');
    }

    public function testControlMultiplePaymentsUpdated() {
        //populate payments table with pending transactions
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>1, 'transaction_id'=>'123-success',
            'transaction_status'=>'Pending', 'caller_reference'=>'12345', 'amount'=>'60', 'timestamp'=>time()));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>1, 'subscriber_id'=>1));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'thinkup_username'=>'willowrosenberg',
            'membership_level'=>'Member'));

        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>2,
            'transaction_id'=>'123-failure-no-message', 'transaction_status'=>'Pending', 'caller_reference'=>'12345',
            'amount'=>'60', 'timestamp'=>time()));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>2, 'subscriber_id'=>2));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>2, 'thinkup_username'=>'xanderharris',
            'membership_level'=>'Member'));

        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>3,
            'transaction_id'=>'123-failure-message-with-xml', 'transaction_status'=>'Pending',
            'caller_reference'=>'12345', 'amount'=>'60', 'timestamp'=>time()));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>3, 'subscriber_id'=>3));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>3, 'thinkup_username'=>'buffysummers',
            'membership_level'=>'Member'));

        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>4,
            'transaction_id'=>'123-failure-message-human-readable', 'transaction_status'=>'Pending',
            'caller_reference'=>'12345', 'amount'=>'60', 'timestamp'=>time()));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>4, 'subscriber_id'=>4));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>4, 'thinkup_username'=>'anya',
            'membership_level'=>'Member'));

        $controller = new UpdatePendingPaymentStatusController(true);
        $controller->control();

        $payment_dao = new PaymentMySQLDAO();
        $subscriber_dao = new SubscriberMySQLDAO();

        //assert pending payments status has been updated
        $payment = $payment_dao->getPayment('123-success', '12345');
        $this->assertEqual($payment->transaction_status, 'Success');
        $this->assertEqual($payment->status_message,
            'The transaction was successful and the payment instrument was charged.');
        $subscriber = $subscriber_dao->getByID(1);
        $this->assertEqual($subscriber->subscription_status, 'Paid');
        $this->assertNotNull($subscriber->paid_through);

        $payment = $payment_dao->getPayment('123-failure-no-message', '12345');
        $this->assertEqual($payment->transaction_status, 'Failure');
        $this->assertNull($payment->status_message);
        $subscriber = $subscriber_dao->getByID(2);
        $this->assertEqual($subscriber->subscription_status, 'Payment failed');
        $this->assertNull($subscriber->paid_through);

        $payment = $payment_dao->getPayment('123-failure-message-with-xml', '12345');
        $this->assertEqual($payment->transaction_status, 'Failure');
        //TODO improve this assertion
        $this->assertTrue(strpos($payment->status_message, 'Sender token not active') !== false);
        $subscriber = $subscriber_dao->getByID(3);
        $this->assertEqual($subscriber->subscription_status, 'Payment failed');
        $this->assertNull($subscriber->paid_through);

        $payment = $payment_dao->getPayment('123-failure-message-human-readable', '12345');
        $this->assertEqual($payment->transaction_status, 'Failure');
        $this->assertEqual($payment->status_message, 'Credit Card is no longer valid');
        $subscriber = $subscriber_dao->getByID(4);
        $this->assertEqual($subscriber->subscription_status, 'Payment failed');
        $this->assertNull($subscriber->paid_through);
    }

    public function testControlSuccessfulChargeMemberEmail() {
        //populate payments table with pending transaction
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>1, 'transaction_id'=>'123-success',
            'transaction_status'=>'Pending', 'caller_reference'=>'12345', 'amount'=>'60',
            'timestamp'=>'2014-04-21 14:50:12'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>1, 'subscriber_id'=>1));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'thinkup_username'=>'willowrosenberg',
            'membership_level'=>'Member'));

        $controller = new UpdatePendingPaymentStatusController(true);
        $controller->control();
        $email = Mailer::getLastMail();
        $decoded_email = json_decode($email);
        $body = $decoded_email->global_merge_vars[0]->content;
        $this->debug($body);
        $this->assertPattern('/Thanks for joining ThinkUp!/', $body);
        $this->assertPattern('/You\'re officially a <strong>ThinkUp Member<\/strong>!/', $body);
        $this->assertPattern('/Your membership lasts until <strong>Apr 21, 2015<\/strong>/', $body);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID(1);
        $this->assertEqual($subscriber->subscription_status, 'Paid');
        $this->assertNotNull($subscriber->paid_through);
    }

    public function testControlSuccessfulChargeProEmail() {
        //populate payments table with pending transaction
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>1, 'transaction_id'=>'123-success',
            'transaction_status'=>'Pending', 'caller_reference'=>'12345', 'amount'=>'60',
            'timestamp'=>'2014-04-21 14:50:12'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>1, 'subscriber_id'=>1));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'thinkup_username'=>'willowrosenberg',
            'membership_level'=>'Pro'));

        $controller = new UpdatePendingPaymentStatusController(true);
        $controller->control();
        $email = Mailer::getLastMail();
        $decoded_email = json_decode($email);
        $body = $decoded_email->global_merge_vars[0]->content;
        $this->debug($body);
        $this->assertPattern('/Thanks for joining ThinkUp!/', $body);
        $this->assertPattern('/You\'re officially a <strong>ThinkUp Pro Member<\/strong>!/', $body);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID(1);
        $this->assertEqual($subscriber->subscription_status, 'Paid');
        $this->assertNotNull($subscriber->paid_through);
    }

    public function testControlPendingChargeNoEmail() {
        //populate payments table with pending transaction
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>1,
            'transaction_id'=>'123-continue-pending', 'transaction_status'=>'Pending', 'caller_reference'=>'12345',
            'amount'=>'60', 'timestamp'=>time()));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>1, 'subscriber_id'=>1));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'thinkup_username'=>'willowrosenberg',
            'membership_level'=>'Member'));

        $controller = new UpdatePendingPaymentStatusController(true);
        $controller->control();
        $email = Mailer::getLastMail();
        $decoded_email = json_decode($email);
        $this->assertEqual('', $email);
    }

    public function testControlFailedChargeEmailNoStatusMessage() {
        //populate payments table with pending transaction
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>1,
            'transaction_id'=>'123-failure-no-message', 'transaction_status'=>'Pending', 'caller_reference'=>'12345',
            'amount'=>'60', 'timestamp'=>time()));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>1, 'subscriber_id'=>1));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'thinkup_username'=>'willowrosenberg',
            'membership_level'=>'Member'));

        $controller = new UpdatePendingPaymentStatusController(true);
        $controller->control();
        $email = Mailer::getLastMail();
        $decoded_email = json_decode($email);
        $body = $decoded_email->global_merge_vars[0]->content;
        $this->debug($body);
        $this->assertPattern('/Uh oh! Problem with your ThinkUp payment/', $body);
        $this->assertNoPattern('/The specific message we got from Amazon/', $body);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID(1);
        $this->assertEqual($subscriber->subscription_status, 'Payment failed');
        $this->assertNull($subscriber->paid_through);
    }

    public function testControlFailedChargeEmailWithXMLifiedStatusMessage() {
        //populate payments table with pending transaction
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>1,
            'transaction_id'=>'123-failure-message-with-xml', 'transaction_status'=>'Pending',
            'caller_reference'=>'12345', 'amount'=>'60', 'timestamp'=>'2014-04-21 14:50:12'));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>1, 'subscriber_id'=>1));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'thinkup_username'=>'willowrosenberg',
            'membership_level'=>'Member'));

        $controller = new UpdatePendingPaymentStatusController(true);
        $controller->control();
        $email = Mailer::getLastMail();
        $decoded_email = json_decode($email);
        $body = $decoded_email->global_merge_vars[0]->content;
        $this->debug($body);
        $this->assertPattern('/Uh oh! Problem with your ThinkUp payment/', $body);
        $this->assertNoPattern('/The specific message we got from Amazon/', $body);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID(1);
        $this->assertEqual($subscriber->subscription_status, 'Payment failed');
        $this->assertNull($subscriber->paid_through);
    }

    public function testControlFailedChargeEmailWithHumanReadableStatusMessage() {
        //populate payments table with pending transaction
        $builders = array();
        $builders[] = FixtureBuilder::build('payments', array('payment_id'=>1,
            'transaction_id'=>'123-failure-message-human-readable', 'transaction_status'=>'Pending',
            'caller_reference'=>'12345', 'amount'=>'60', 'timestamp'=>time()));
        $builders[] = FixtureBuilder::build('subscriber_payments', array('payment_id'=>1, 'subscriber_id'=>1));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>1, 'thinkup_username'=>'willowrosenberg',
            'membership_level'=>'Member'));

        $controller = new UpdatePendingPaymentStatusController(true);
        $controller->control();
        $email = Mailer::getLastMail();
        $decoded_email = json_decode($email);
        $body = $decoded_email->global_merge_vars[0]->content;
        $this->debug($body);
        $this->assertPattern('/Uh oh! Problem with your ThinkUp payment/', $body);
        $this->assertPattern('/The specific message we got from Amazon/', $body);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID(1);
        $this->assertEqual($subscriber->subscription_status, 'Payment failed');
        $this->assertNull($subscriber->paid_through);
   }
}