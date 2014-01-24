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
}