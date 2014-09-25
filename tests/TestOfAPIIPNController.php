<?php
require_once dirname(__FILE__) . '/init.tests.php';
require_once ISOSCELES_PATH.'extlibs/simpletest/autorun.php';
require_once dirname(__FILE__) . '/classes/mock.AmazonFPSAPIAccessor.php';
require_once dirname(__FILE__) . '/classes/mock.SignatureUtilsForOutbound.php';

class TestOfAPIIPNController extends UpstartUnitTestCase {

    public function setUp() {
        parent::setUp();
        $_SESSION = array();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function testConstructor() {
        $controller = new APIIPNController(true);
        $this->assertIsA($controller, 'APIIPNController');
    }

    public function testMissingParamsErrorLog() {
        $_POST = array(
            "paymentReason"=> "ThinkUp.com membership",
            "transactionAmount"=> "USD 5.00",
            "signatureMethod"=> "RSA-SHA1",
            "transactionId"=> "192F72Q6OQR4VOEUQ6KBE8RCK6S6NUABACA"
        );
        $controller = new APIIPNController(true);
        $result = $controller->control();
        $this->assertEqual('', $result);
        // Assert error got logged
        $error_log_dao = new ErrorLogMySQLDAO();
        $errors = $error_log_dao->getErrorList();
        $this->assertEqual(count($errors), 1);
    }

    public function testNoPostVarsSet() {
        $controller = new APIIPNController(true);
        $result = $controller->control();
        $this->assertEqual('', $result);
        // Assert no error got logged
        $error_log_dao = new ErrorLogMySQLDAO();
        $errors = $error_log_dao->getErrorList();
        $this->assertEqual(count($errors), 0);
    }

    public function testControlPaymentInitiated() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscription_operations',
            array('amazon_subscription_id'=>'a9b486d3-95cf-4587-957f-61da63030f55', 'subscriber_id'=>'5',
            'operation'=>'pay', 'payment_reason'=>'ThinkUp.com membership'));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>'5', 'subscription_status'=>'Paid',
            'subscription_recurrence'=>'1 month'));

        $_POST = array(
            "paymentReason"=> "ThinkUp.com membership",
            "transactionAmount"=> "USD 5.00",
            "signatureMethod"=> "RSA-SHA1",
            "transactionId"=> "192F72Q6OQR4VOEUQ6KBE8RCK6S6NUABACA",
            "status"=> "PI",
            "buyerEmail"=> "ameliaearhart@gmail.com",
            "referenceId"=> "60d73_1411407531",
            "recipientEmail"=> "hostmaster@thinkup.com",
            "transactionDate"=> "1411407505",
            "buyerName"=> "Amelia Earhart",
            "subscriptionId"=> "a9b486d3-95cf-4587-957f-61da63030f55",
            "operation"=> "pay",
            "recipientName"=> "ThinkUp, LLC",
            "transactionSerialNumber"=> "1",
            "signatureVersion"=> "2",
            "signature"=> "wYORr8QauK0mBhHqEC5THhauu0qXFBV07aIGobctEjETl2TbpZ8Rk7qOA9EHfWMjjcOm+7obRKCF"
                ."ng+1ZkLFBAklxsoHOg8GdaQH5rBDvJtF+BrZixVHWOMMwiM43L/KgUynH5faiJyrMN1PjpDqPDFR"
                ."RPmb3Hrgmo5aPu45XZmdl2+tnCnIj2Wz4sgON0rpHk8dZQG74Fn0rfT5Jrih9ZyGaQUvz6rw17rG"
                ."PtH0OVUVLMd8Vl3P5KaJ4YzaENQzLvltmPzhYdjyM0aOswkYiD2a8ShUnKKcsRFbNOvj5+qx/yBu"
                ."x0TPEbCVNLP23hNrXiexZwVb6mNhDS9OPRtUEA==",
            "certificateUrl"=>
            "https://fps.amazonaws.com/certs/040714/PKICert.pem?requestId=15nc4iuawep4ysh221xnridwkn9xz8fn6y7jq09rs0tu",
            "paymentMethod"=> "CC"
        );

        $controller = new APIIPNController(true);
        $controller->control();

        //Assert that the subscription_operation was inserted
        $subscription_operation_dao = new SubscriptionOperationMySQLDAO();
        $op = $subscription_operation_dao->getByReferenceID("a9b486d3-95cf-4587-957f-61da63030f55", "60d73_1411407531");
        $this->assertNotNull($op);
        $this->assertEqual($op->reference_id, "60d73_1411407531");

        //Assert that the subscriber's status is correct ("Paid through")
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID($op->subscriber_id);
        $this->assertPattern("/Payment pending/", $subscriber->subscription_status);
    }

    public function testControlPaymentSuccessful() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscription_operations',
            array('amazon_subscription_id'=>'a9b486d3-95cf-4587-957f-61da63030f55', 'subscriber_id'=>'5',
            'operation'=>'pay', 'payment_reason'=>'ThinkUp.com membership'));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>'5', 'subscription_status'=>'Paid',
            'subscription_recurrence'=>'1 month'));

        $_POST = array(
            "paymentReason"=> "ThinkUp.com membership",
            "transactionAmount"=> "USD 5.00",
            "signatureMethod"=> "RSA-SHA1",
            "transactionId"=> "192F72Q6OQR4VOEUQ6KBE8RCK6S6NUABACA",
            "status"=> "PS",
            "buyerEmail"=> "ameliaearhart@gmail.com",
            "referenceId"=> "60d73_1411407531",
            "recipientEmail"=> "hostmaster@thinkup.com",
            "transactionDate"=> "1411407505",
            "buyerName"=> "Amelia Earhart",
            "subscriptionId"=> "a9b486d3-95cf-4587-957f-61da63030f55",
            "operation"=> "pay",
            "recipientName"=> "ThinkUp, LLC",
            "transactionSerialNumber"=> "1",
            "signatureVersion"=> "2",
            "signature"=> "wYORr8QauK0mBhHqEC5THhauu0qXFBV07aIGobctEjETl2TbpZ8Rk7qOA9EHfWMjjcOm+7obRKCF"
                ."ng+1ZkLFBAklxsoHOg8GdaQH5rBDvJtF+BrZixVHWOMMwiM43L/KgUynH5faiJyrMN1PjpDqPDFR"
                ."RPmb3Hrgmo5aPu45XZmdl2+tnCnIj2Wz4sgON0rpHk8dZQG74Fn0rfT5Jrih9ZyGaQUvz6rw17rG"
                ."PtH0OVUVLMd8Vl3P5KaJ4YzaENQzLvltmPzhYdjyM0aOswkYiD2a8ShUnKKcsRFbNOvj5+qx/yBu"
                ."x0TPEbCVNLP23hNrXiexZwVb6mNhDS9OPRtUEA==",
            "certificateUrl"=>
            "https://fps.amazonaws.com/certs/040714/PKICert.pem?requestId=15nc4iuawep4ysh221xnridgoywkn8fn6y7jq09rs0tu",
            "paymentMethod"=> "CC"
        );

        $controller = new APIIPNController(true);
        $controller->control();

        //Assert that the subscription_operation was inserted
        $subscription_operation_dao = new SubscriptionOperationMySQLDAO();
        $op = $subscription_operation_dao->getByReferenceID("a9b486d3-95cf-4587-957f-61da63030f55", "60d73_1411407531");
        $this->assertNotNull($op);
        $this->assertEqual($op->reference_id, "60d73_1411407531");

        //Assert that the subscriber's status is correct ("Paid through")
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID($op->subscriber_id);
        $this->assertPattern("/Paid through/", $subscriber->subscription_status);
    }

    public function testControlSubscriptionCancelled() {
        $builders = array();
        $builders[] = FixtureBuilder::build('subscription_operations',
            array('amazon_subscription_id'=>'a9b486d3-95cf-4587-957f-61da63030f55', 'subscriber_id'=>'5',
            'operation'=>'pay', 'payment_reason'=>'ThinkUp.com membership'));
        $builders[] = FixtureBuilder::build('subscribers', array('id'=>'5', 'subscription_status'=>'Paid',
            'subscription_recurrence'=>'1 month'));

        $_POST = array(
            "statusReason"=> "PaymentFailure",
            "status"=> "SubscriptionCancelled",
            "subscriptionId"=> "a9b486d3-95cf-4587-957f-61da63030f55",
            "signatureVersion"=> "2",
            "signature"=> "wYORr8QauK0mBhHqEC5THhauu0qXFBV07aIGobctEjETl2TbpZ8Rk7qOA9EHfWMjjcOm+7obRKCF"
                ."ng+1ZkLFBAklxsoHOg8GdaQH5rBDvJtF+BrZixVHWOMMwiM43L/KgUynH5faiJyrMN1PjpDqPDFR"
                ."RPmb3Hrgmo5aPu45XZmdl2+tnCnIj2Wz4sgON0rpHk8dZQG74Fn0rfT5Jrih9ZyGaQUvz6rw17rG"
                ."PtH0OVUVLMd8Vl3P5KaJ4YzaENQzLvltmPzhYdjyM0aOswkYiD2a8ShUnKKcsRFbNOvj5+qx/yBu"
                ."x0TPEbCVNLP23hNrXiexZwVb6mNhDS9OPRtUEA==",
            "certificateUrl"=>
            "https://fps.amazonaws.com/certs/040714/PKICert.pem?requestId=15nc4iuawep4ysh221xnridwkn9xz8fn6y7jq09rs0tu",
        );

        $controller = new APIIPNController(true);
        $controller->control();

        //TODO Assert that slack got notified by writing latest slack to a file in the data dir ala latest email
    }
}