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

	public function __construct() {
        $cfg = Config::getInstance();
        $this->AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $this->AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');
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
        return true;
    }
}