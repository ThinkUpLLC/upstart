<?php
class ConfirmPaymentController extends SignUpHelperController {
    /**
     * Detect Amazon Payment state, record authorization, charge, and thank member.
     * @return str HTML page
     */
    public function control() {
        $this->setViewTemplate('confirm-payment.tpl');
        //Avoid "unable to write file [really long file]" errors
        $this->disableCaching();
        $new_subscriber_id = SessionCache::get('new_subscriber_id');
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID($new_subscriber_id);
        $this->addToView('subscriber', $subscriber);
        $cfg = Config::getInstance();
        $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
            $cfg->getValue('user_installation_url'));
        $this->addToView('thinkup_url', $user_installation_url);

        if ($this->hasUserReturnedFromAmazon()) {
            if ($this->isAmazonResponseValid()) {
                //@TODO Double-check $_GET[errorMessage] is set if there's an error
                $error_message = isset($_GET["errorMessage"])?$_GET["errorMessage"]:null;
                if ($error_message !== null ) {
                    //@TODO Display error message on page, instead of confirmation message
                    $this->addErrorMessage($this->generic_error_msg);
                    $this->logError("Amazon returned error: ".$error_message, __FILE__,__LINE__,__METHOD__);
                } else {
                    //Simple pay params
                    if (UpstartHelper::areGetParamsSet(SignUpHelperController::$amazon_simple_pay_return_params)) {
                        //Capture Simple Pay return codes
                        $op = new SubscriptionOperation();
                        $op->subscriber_id = $subscriber->id;
                        $op->payment_reason = $_GET['paymentReason'];
                        $op->transaction_amount = $_GET['transactionAmount'];
                        $op->status_code = $_GET['status'];
                        $op->buyer_email = $_GET['buyerEmail'];
                        //@TODO Verify the reference_id starts with the subscriber ID
                        $op->reference_id = $_GET['referenceId'];
                        $op->amazon_subscription_id = $_GET['subscriptionId'];
                        $op->transaction_date = $_GET['transactionDate'];
                        $op->buyer_name = $_GET['buyerName'];
                        $op->operation = $_GET['operation'];
                        $op->recurring_frequency = $_GET['recurringFrequency'];
                        $op->payment_method = $_GET['paymentMethod'];

                        //Check to make sure this isn't a page refresh by catching a DuplicateKey exception
                        try {
                            $subscription_operation_dao = new SubscriptionOperationMySQLDAO();
                            $subscription_operation_dao->insert($op);

                            //@TODO Update subscriber method to get status to access subscription_operations data
                            //Now that user has authed and paid, get current subscription_status
                            $subscription_status = $subscriber->getSubscriptionStatus();
                            //Update subscription_status in the subscriber object
                            $subscriber->subscription_status = $subscription_status;
                            //Update subscription_status in the data store
                            $subscriber_dao->updateSubscriptionStatus($subscriber->id, $subscription_status);
                        } catch (DuplicateSubscriptionOperationException $e) {
                            $this->addSuccessMessage("Whoa there! It looks like you already paid for your ThinkUp ".
                                "subscription. Maybe you refreshed the page in your browser?");
                        }
                    } else {
                        $this->addErrorMessage($this->generic_error_msg);
                        $this->logError('Missing Amazon return parameter', __FILE__,__LINE__, __METHOD__);
                    }
                }
            } else {
                //@TODO Display error message on page, instead of confirmation message
                $this->addErrorMessage($this->generic_error_msg);
                $this->logError('Amazon response invalid', __FILE__,__LINE__, __METHOD__);
            }
        } else {
            //@TODO Display error message on page, instead of confirmation message
        	$this->addErrorMessage($this->generic_error_msg);
            $this->logError('Has not returned from Amazon', __FILE__,__LINE__, __METHOD__);
        }
        return $this->generateView();
    }

    /**
     * Return whether or not user has returned from Amazon with necessary parameters.
     * @return bool
     */
    protected function hasUserReturnedFromAmazon() {
        return (isset($_GET['referenceId']) /* && isset($_GET['tokenID'])*/  && isset($_GET['level'])
            && isset($_GET['status']) && isset($_GET['certificateUrl']) && isset($_GET['signatureMethod'])
            && isset($_GET['signature']) && isset($_GET['recur']));
    }

    /**
     * Return whether or not Amazon signature is valid.
     * @return bool
     */
    protected function isAmazonResponseValid() {
        //Check inputs match internal rules
        $endpoint_url = UpstartHelper::getApplicationURL().'confirm-payment.php';
        $endpoint_url_params = array('level'=>$_GET['level'], 'recur'=>$_GET['recur'] );
        return AmazonFPSAPIAccessor::isAmazonSignatureValid($endpoint_url, $endpoint_url_params);
    }
}
