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
                    //Display error message, log debug info
                    $this->addErrorMessage($this->generic_error_msg);
                    $this->logError("Amazon returned error: ".$error_message, __FILE__,__LINE__,__METHOD__);
                } else {
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

                        //Set new paid_through date and update status just in case
                        $subscription_helper = new SubscriptionHelper();
                        $subscription_helper->updateSubscriptionStatusAndPaidThrough($subscriber, $op);

                        //Update is_free_trial field in ThinkUp installation
                        $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
                        $trial_ended = $tu_tables_dao->endFreeTrial($subscriber->email);
                        if (!$trial_ended) {
                            $this->logError('Unable to end trial in ThinkUp installation',
                                __FILE__,__LINE__, __METHOD__);
                        }
                        UpstartHelper::postToSlack('#signups',
                            'Ding-ding! A member just subscribed during signup.\nhttps://'.
                            $subscriber->thinkup_username.
                            '.thinkup.com\nhttps://www.thinkup.com/join/admin/subscriber.php?id='.
                            $subscriber->id);
                    } catch (DuplicateSubscriptionOperationException $e) {
                        $this->addSuccessMessage("Whoa there! It looks like you already paid for your ThinkUp ".
                            "subscription. Maybe you refreshed the page in your browser?");
                    }
                }
            } else {
                //Display error message, log debug info
                $this->addErrorMessage($this->generic_error_msg);
                $this->logError('Amazon response invalid', __FILE__,__LINE__, __METHOD__);
            }
        } else if (isset($_GET['code'])) {
            //Strip spaces and go uppercase
            $code_str = str_replace(' ', '', strtoupper($_GET['code']));
            //Redeem code
            $claim_code_dao = new ClaimCodeMySQLDAO();
            $claim_code = $claim_code_dao->get($code_str);
            if (isset($claim_code)) {
                if (!$claim_code->is_redeemed) {
                    $code_redemption_update = $claim_code_dao->redeem($claim_code->code);
                    if ($code_redemption_update > 0) {
                        $subscriber_redemption_update = $subscriber_dao->redeemClaimCode($subscriber->id, $claim_code);
                        if ($subscriber_redemption_update > 0) {
                            $this->addSuccessMessage("It worked! We've applied your coupon code.");
                            UpstartHelper::postToSlack('#signups',
                                'Oh hello! Someone just redeemed a coupon code during signup.'
                                .'\nhttps://'. $subscriber->thinkup_username.
                                '.thinkup.com\nhttps://www.thinkup.com/join/admin/subscriber.php?id='.
                                $subscriber->id);
                        } else {
                            $this->addErrorMessage("Oops! There was a problem processing your code. Please log in "
                                ."and try again on your membership page.");
                        }
                    } else {
                        $this->addErrorMessage("Oops! There was a problem processing your code. Please log in "
                            ."and try again on your membership page.");
                    }
                } else {
                    $this->addErrorMessage("Whoops! It looks like that code has already been used.");
                }
            } else {
                $this->addErrorMessage("That code doesn't seem right. Check it and try again?");
            }
        } else {
            //Display error message, log debug info
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
        return (UpstartHelper::areGetParamsSet(SignUpHelperController::$amazon_simple_pay_subscription_return_params) &&
            isset($_GET['level']) && isset($_GET['recur']));
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
