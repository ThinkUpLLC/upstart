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

	public function __construct($use_deprecated_tokens = false) {
        $cfg = Config::getInstance();
        if ($use_deprecated_tokens) {
            $this->AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID_DEPREC');
            $this->AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY_DEPREC');
        } else {
            $this->AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
            $this->AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');
        }
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
    public function generateSubscribeForm($amount, $recurring_frequency, $description, $reference_id, $return_url) {
        try{
            return ButtonGenerator::generateSubscriptionForm($this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY,
                $amount, $recurring_frequency, $description, $reference_id, $immediateReturn=1, $returnUrl=$return_url,
                $abandonUrl=null, $processImmediate=1, $ipnUrl=null, $collectShippingAddress=0,
                $signatureMethod="HmacSHA256", $this->environment);
        } catch (Exception $e) {
            //@TODO handle this more gracefully
            echo 'Exception : ', $e->getMessage(),"\n";
        }
    }

    /**
     * Generate Amazon Simple Pay form with Pay Now button markup.
     * @param  str $amount
     * @param  str $description
     * @param  str $reference_id
     * @param  str $return_url
     * @return str
     */
    public function generateStandardForm($amount, $description, $reference_id, $return_url) {
        try{
            return ButtonGenerator::generateStandardForm($this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY,
                $amount, $description, $reference_id, $immediateReturn=1, $returnUrl=$return_url,
                $abandonUrl=null, $processImmediate=1, $ipnUrl=null, $collectShippingAddress=0,
                $signatureMethod="HmacSHA256", $this->environment);
        } catch (Exception $e) {
            //@TODO handle this more gracefully
            echo 'Exception : ', $e->getMessage(),"\n";
        }
    }

    /**
     * Cancel Simple Pay subscription.
     * @param str $subscription_id
     * @param str $refund_amount
     * @param str $caller_reference
     * @return Amazon_FPS_Model_CancelSubscriptionAndRefundResponse
     */
    public function cancelAndRefundSubscription($subscription_id, $refund_amount, $caller_reference) {
        $service = new Amazon_FPS_Client($this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY);
        $params = array();
        //REQUIRED PARAMS:
        $params['SubscriptionId'] = $subscription_id;
        $params['RefundAmount'] = array('Value'=>$refund_amount, 'CurrencyCode'=>'USD');
        $params['CallerReference'] = $caller_reference;

        return $service->cancelSubscriptionAndRefund($params);
    }

    /**
     * Refund FPS transaction.
     * @param str $caller_reference
     * @param str $transaction_id
     * @param str $amount
     * @return Amazon_FPS_Model_CancelSubscriptionAndRefundResponse
     */
    public function refundPayment($caller_reference, $transaction_id, $refund_amount) {
        $service = new Amazon_FPS_Client($this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY);
        $params = array();
        //REQUIRED PARAMS:
        $params['TransactionId'] = $transaction_id;
        $params['RefundAmount'] = array('Value'=>$refund_amount, 'CurrencyCode'=>'USD');
        $params['CallerReference'] = $caller_reference;

        return $service->refund($params);
    }

    /**
     * Charge user, record transaction, and report success or error back.
     * @param str $token_id
     * @param int $amount
     * @return bool Did the payment succeed?
     */
    public function invokeAmazonPayAction($subscriber_id, $token_id, $amount) {
    	//@TODO Verify subscriber exists
    	//@TODO Verify authorization exists

        $service = new Amazon_FPS_Client($this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY);

        $caller_reference = $subscriber_id.'_'.time();
        $payment_dao = new PaymentMySQLDAO();
        $subscriber_payment_dao = new SubscriberPaymentMySQLDAO();
        try {
            $params = array();
            $amount_params = array('Value'=>$amount, 'CurrencyCode'=>'USD');
            //REQUIRED PARAMS:
            $params['CallerReference'] = $caller_reference;
            $params['SenderTokenId'] = $token_id;
            $params['TransactionAmount'] = $amount_params;

            $request_object = new Amazon_FPS_Model_PayRequest($params);
            $response = $service->pay($request_object);

            $request_id = null;
            if ($response->isSetResponseMetadata()) {
                $responseMetadata = $response->getResponseMetadata();
                $request_id = $responseMetadata->getRequestId();
            }
            if ($response->isSetPayResult()) {
                $payResult = $response->getPayResult();
                $transaction_id = $payResult->getTransactionId();
                $status = $payResult->getTransactionStatus();
                $payment_id = $payment_dao->insert($transaction_id, $request_id, $status, $amount, $caller_reference);
                if ($payment_id) {
                    $subscriber_payment_dao->insert($subscriber_id, $payment_id);
                    return true;
                }
               $message = "Unable to store payment\n".$response->getXML();
               $payment_id = $payment_dao->insert(0, $request_id, '', 0, $caller_reference, $message);
               return false;
            }

            $message = "PayResult not returned\n".$response->getXML();
            $payment_id = $payment_dao->insert(0, $request_id, '', 0, $caller_reference, $message);
            return false;
        } catch (Amazon_FPS_Exception $ex) {
            $request_id = $ex->getRequestId();
            $message = $ex->getMessage() ."\n" . $ex->getXML();
            $payment_id = $payment_dao->insert(0, $request_id, '', 0, $caller_reference, $message);
            if ($payment_id) {
                $subscriber_payment_dao->insert($subscriber_id, $payment_id);
            }
        }
        return false;
    }

    /**
     * Get the status of a pending transaction.
     * @param str $transaction_id
     * @return arr status, status_message, caller_reference, transaction_id
     */
    public function getTransactionStatus($transaction_id) {
        $service = new Amazon_FPS_Client($this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY);
        $params = array();
        $params['TransactionId'] = $transaction_id;
        $request_object = new Amazon_FPS_Model_GetTransactionStatusRequest($params);
        $response = $service->getTransactionStatus($request_object);

        if ($response->isSetGetTransactionStatusResult()) {
            $status_result = $response->getGetTransactionStatusResult();
            $status = $status_result->getTransactionStatus();
            $status_message = $status_result->StatusMessage;
            $caller_reference = $status_result->CallerReference;
            $transaction_id_returned = $status_result->TransactionId;
            return array('status'=>$status, 'status_message'=>$status_message, 'caller_reference'=>$caller_reference,
            'transaction_id'=>$transaction_id_returned);
        } else {
            $message = "Transaction status not returned\n".$response->getXML();
            throw new Exception($message);
        }
    }

    /**
     * Get a valid Amazon Flexible Payment System payment URL for a given amount.
     * @param  str $caller_reference
     * @param  str $callback_url
     * @param  int $amount
     * @return str URL
     */
    public function getAmazonFPSURL($caller_reference, $callback_url, $amount) {
        $pipeline = new Amazon_FPS_CBUIRecurringTokenPipeline($this->AWS_ACCESS_KEY_ID,
            $this->AWS_SECRET_ACCESS_KEY);

        $pipeline->setMandatoryParameters($caller_reference, $callback_url, $amount, "12 Months");

        //optional parameters
        $pipeline->addParameter("paymentReason", "ThinkUp yearly membership");
        // If validityStart is not specified, then it defaults to now
        //$amazon_payment_auth_validity_start = $cfg->getValue('amazon_payment_auth_validity_start');
        //$pipeline->addParameter("validityStart", $amazon_payment_auth_validity_start);
        $pipeline->addParameter("cobrandingUrl",
        UpstartHelper::getApplicationURL(false, false, false)."assets/img/thinkup-logo-transparent.png");
        $pipeline->addParameter("websiteDescription", "ThinkUp");

        return $pipeline->getUrl();
    }

    /**
     * Return whether or not the Amazon redirect signature is valid.
     * @param  str  $endpoint_url        The endpoint URL the Amazon request redirected to
     * @param  arr  $endpoint_url_params Optional endpoint URL parameters
     * @return bool
     */
    public function isAmazonSignatureValid($endpoint_url, $endpoint_url_params = array()) {
        $service = new Amazon_FPS_Client($this->AWS_ACCESS_KEY_ID, $this->AWS_SECRET_ACCESS_KEY);

        try {
            $request_params_array = array();
            $endpoint_params_array = array();
            foreach ($_GET as $key => $value) {
                if (!in_array( $key, $endpoint_url_params)) {
                    $request_params_array[$key] = $value;
                } else {
                    $endpoint_params_array[$key] = $value;
                }
            }
            $request_params_str = http_build_query($request_params_array);
            if (sizeof($endpoint_params_array) > 0) {
                $endpoint_params_str = http_build_query($endpoint_params_array);
                $endpoint_url = '?'.$endpoint_params_str;
            }
            $request_array = array('UrlEndPoint'=>$endpoint_url, 'HttpParameters'=>$request_params_str);
            //print_r($request_array);
            $request_object = new Amazon_FPS_Model_VerifySignatureRequest($request_array);
            //            echo "<pre>";
            //            print_r($request_object);
            //            echo "</pre>";
            $response = $service->verifySignature($request_object);

            $verifySignatureResult = $response->getVerifySignatureResult();
            $result = $verifySignatureResult->getVerificationStatus();
            if ($result == 'Success') {
                return true;
            }
        } catch (Amazon_FPS_Exception $ex) {
            //@TODO Log these error details into error log table
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
        }
        return false;
    }
}