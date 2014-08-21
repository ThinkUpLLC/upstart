<?php

class AmazonFPSAPIAccessor {

   /**
	* AWS access key
	* @str
	*/
	var $AWS_ACCESS_KEY_ID;
   /**
	* AWS secret access key
	* @str
	*/
	var $AWS_SECRET_ACCESS_KEY;
    /**
     * AWS environment. Valid values are 'sandbox' or 'prod'
     * @var str
     */
    var $environment;

	public function __construct() {
        $cfg = Config::getInstance();
        $this->AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $this->AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');
        $this->environment = ($cfg->getValue('amazon_sandbox'))?'sandbox':'prod';
    }

    /**
     * Generate Amazon Simple Pay form with Pay Now button markup.
     * @param  str $amount
     * @param  str $recurring_frequency
     * @param  str $description
     * @param  str $reference_id
     * @param  str $return_url
     * @return str
     */
    public function generateSimplePayNowForm($amount, $recurring_frequency, $description, $reference_id, $return_url) {
        try{
            return ButtonGenerator::generateForm($this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY, $amount,
                $recurring_frequency, $description, $reference_id, $immediateReturn=1, $returnUrl=$return_url,
                $abandonUrl=null, $processImmediate=1, $ipnUrl=null, $collectShippingAddress=0,
                $signatureMethod="HmacSHA256", $this->environment);
        } catch (Exception $e) {
            //@TODO handle this more gracefully
            echo 'Exception : ', $e->getMessage(),"\n";
        }
    }

    /**
     * Charge user, record transaction, and report success or error back.
     * @param str $token_id
     * @param int $amount
     * @return bool Did the payment succeed?
     */
    public function invokeAmazonPayAction($subscriber_id, $token_id, $amount) {
        $payment_dao = new PaymentMySQLDAO();
        $subscriber_payment_dao = new SubscriberPaymentMySQLDAO();
        $caller_reference = $subscriber_id.'_'.time();
        $transaction_id = 'test-transaction-id';
        $request_id = 'test-request-id';
        $status = 'Pending';
        $payment_id = $payment_dao->insert($transaction_id, $request_id, $status, $amount, $caller_reference);
        if ($payment_id) {
            $subscriber_payment_dao->insert($subscriber_id, $payment_id);
            return true;
        } else {
            throw new Exception('Payment not inserted');
        }
    }

    /**
     * Get the status of a pending transaction.
     * @param str $transaction_id
     * @return arr status, status_message, caller_reference, transaction_id
     */
    public function getTransactionStatus($transaction_id) {
        if (strpos($transaction_id, 'failure') === false && strpos($transaction_id, 'continue-pending') === false) {
            return array('status'=>'Success',
                'status_message'=>'The transaction was successful and the payment instrument was charged.',
                'caller_reference'=>'12345',
                'transaction_id'=>$transaction_id);
        } elseif (strpos($transaction_id, 'continue-pending') !== false) {
            return array('status'=>'Pending',
                'status_message'=>'Waiting for backend payment processor',
                'caller_reference'=>'12345',
                'transaction_id'=>$transaction_id);
        } else {
            if (strpos($transaction_id, 'no-message') !== false) {
            // no message
                return array('status'=>'Failure',
                    'status_message'=>null,
                    'caller_reference'=>'12345',
                    'transaction_id'=>$transaction_id);
            } elseif (strpos($transaction_id, 'message-with-xml') !== false) {
            // message with xml
                return array('status'=>'Failure',
                    'status_message'=>'Sender token not active.
<?xml version="1.0"?>
<Response><Errors><Error><Code>TokenNotActive_Sender</Code><Message>Sender token not active.</Message></Error></Errors><RequestID>a3cb5e5c-da1f-403a-a05e-03ea9c7407ac</RequestID></Response>',
                    'caller_reference'=>'12345',
                    'transaction_id'=>$transaction_id);
            } elseif (strpos($transaction_id, 'message-human-readable') !== false) {
            // human-readable message
                return array('status'=>'Failure',
                    'status_message'=>'Credit Card is no longer valid',
                    'caller_reference'=>'12345',
                    'transaction_id'=>$transaction_id);
            }
        }
    }

    /**
     * Get a valid Amazon Flexible Payment System payment URL for a given amount.
     * @param  str $caller_reference
     * @param  str $callback_url
     * @param  int $amount
     * @return str URL
     */
    public static function getAmazonFPSURL($caller_reference, $callback_url, $amount) {
        return 'http://amazonsandbox.example.com';
    }

    /**
     * Return whether or not the Amazon redirect signature is valid.
     * @param  str  $endpoint_url        The endpoint URL the Amazon request redirected to
     * @param  arr  $endpoint_url_params Optional endpoint URL parameters
     * @return bool
     */
    public static function isAmazonSignatureValid($endpoint_url, $endpoint_url_params = array()) {
        if (isset($_GET['signatureValidity'] )) {
            return $_GET['signatureValidity'];
        } else {
            return true;
        }
    }

    public function cancelAndRefundSubscription($subscription_id, $refund_amount, $caller_reference) {
        return new Amazon_FPS_Model_CancelSubscriptionAndRefundResponse();
   }
}