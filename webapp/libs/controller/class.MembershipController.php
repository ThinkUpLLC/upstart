<?php
class MembershipController extends AuthController {

    public function authControl() {
        $this->setPageTitle('Membership Info');
        $this->setViewTemplate('user.membership.tpl');
        $this->disableCaching();
        $this->enableCSRFToken();

        $logged_in_user = Session::getLoggedInUser();
        $this->addToView('logged_in_user', $logged_in_user);
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail($logged_in_user);
        $this->addToView('subscriber', $subscriber);

        $config = Config::getInstance();
        $subscription_date = new DateTime(substr($subscriber->creation_time,8,2).'-'.
            substr($subscriber->creation_time,5,2).
            '-'.substr($subscriber->creation_time,0,4));

        $this->addToView('subscription_date', $subscription_date);
        $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
            $config->getValue('user_installation_url'));
        $this->addToView('thinkup_url', $user_installation_url);

        //@TODO Rewrite this to handle Amazon Simple Pay parameters
        // Process payment if returned from Amazon
        if (self::hasUserReturnedFromAmazon()) {
            $internal_caller_reference = SessionCache::get('caller_reference');
            if (isset($internal_caller_reference) && $this->isAmazonResponseValid($internal_caller_reference)) {
                if (UpstartHelper::areGetParamsSet(SignUpHelperController::$amazon_simple_pay_return_params)) {
                    $error_message = isset($_GET["errorMessage"])?$_GET["errorMessage"]:null;
                    if ($error_message !== null ) {
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

                            if ($op->status_code !== 'SF') {
                                $this->addSuccessMessage("Success! Thanks for being a ThinkUp member.");
                            }

                            //Now that user has created a subscription, generate up-to-date subscription_status
                            $subscription_status = $subscriber->getSubscriptionStatus();
                            //Update subscription_status in the subscriber object
                            $subscriber->subscription_status = $subscription_status;
                            //Update subscription_status in the data store
                            $subscriber_dao->updateSubscriptionStatus($subscriber->id, $subscription_status);
                            //Update recurrence_frequency in the data store
                            $subscriber_dao->updateSubscriptionRecurrence($subscriber->id, $op->recurring_frequency);
                        } catch (DuplicateSubscriptionOperationException $e) {
                            $this->addSuccessMessage("Whoa there! It looks like you already paid for your ThinkUp ".
                                "subscription. Maybe you refreshed the page in your browser?");
                        }
                    }
                } else {
                    $this->addErrorMessage($this->generic_error_msg);
                    $this->logError('Missing Amazon return parameter', __FILE__,__LINE__, __METHOD__);
                }
            } else {
                $this->addErrorMessage("Oops! Something went wrong. Please try again or contact us for help.");
                $this->logError('Internal caller reference not set or Amazon response invalid', __FILE__,__LINE__,
                __METHOD__);
            }
        }

        try {
            if (self::hasUserRequestedAccountClosure() && $this->validateCSRFToken()) {
                //Get SubscriptionId for logged-in subscriber
                $sub_op_dao = new SubscriptionOperationMySQLDAO();
                $operation = $sub_op_dao->getLatestOperation($subscriber->id);
                if (isset($operation)) {
                    try {
                        //Issue CancelAndRefund call to Amazon API
                        $api_accessor = new AmazonFPSAPIAccessor();
                        //Calculate refund
                        $refund_amount = $sub_op_dao->calculateProRatedMonthlyRefund($subscriber->id);
                        //Create a callerReference
                        $caller_reference = $subscriber->id.'_'.time();
                        $response = $api_accessor->cancelAndRefundSubscription($operation->amazon_subscription_id,
                            $refund_amount, $caller_reference);

                        // If request ID is set correctly, add subscription operation and close account
                        if (isset($response)) {
                            // Add subscription operation
                            $op_cancel = new SubscriptionOperation();
                            $op_cancel->subscriber_id = $subscriber->id;
                            $op_cancel->payment_reason = "Refund due to cancellation";
                            $op_cancel->transaction_amount = $refund_amount;
                            $op_cancel->status_code = '';
                            $op_cancel->buyer_email = $operation->buyer_email;
                            //@TODO Verify the reference_id starts with the subscriber ID
                            $op_cancel->reference_id = $caller_reference;
                            $op_cancel->amazon_subscription_id = $operation->amazon_subscription_id;
                            $op_cancel->transaction_date = time();
                            $op_cancel->buyer_name = $operation->buyer_name;
                            $op_cancel->operation = 'cancel';
                            $op_cancel->recurring_frequency = $operation->recurring_frequency;
                            $op_cancel->payment_method = $operation->payment_method;
                            $sub_op_dao->insert($op_cancel);

                            // Close account
                            $result = $subscriber_dao->closeAccount($subscriber->id);
                            if ($result > 0) {
                                //@TODO log user out with message about closure and refund.
                                $this->addSuccessMessage("Your ThinkUp account has been closed. ".
                                    "But there's still time to change your mind!");
                                $subscriber->is_account_closed = true;
                                $this->addToView('subscriber', $subscriber);
                            } else {
                                //@TODO show user error, log system error
                            }
                        } else {
                            //@TODO show user error, log system error
                        }
                    } catch (Amazon_FPS_Exception $ex) {
                        //@TODO show user error, log system error
                        $debug = "Caught Exception: " . $ex->getMessage() . "\n";
                        $debug .= "Response Status Code: " . $ex->getStatusCode() . "\n";
                        $debug .= "Error Code: " . $ex->getErrorCode() . "\n";
                        $debug .= "Error Type: " . $ex->getErrorType() . "\n";
                        $debug .= "Request ID: " . $ex->getRequestId() . "\n";
                        $debug .= "XML: " . $ex->getXML() . "\n";
                        print_r($debug);
                    }
                }
            }

            if (self::hasUserRequestedAccountReopening() && $this->validateCSRFToken()) {
                $result = $subscriber_dao->openAccount($subscriber->id);
                if ($result > 0) {
                    $this->addSuccessMessage("Your ThinkUp account has been re-opened!");
                    $subscriber->is_account_closed = false;
                    $this->addToView('subscriber', $subscriber);
                }
            }
        } catch (InvalidCSRFTokenException $e) {
            $this->addErrorMessage("There was a problem processing your request. Please try again.");
        }

        //BEGIN populating membership_status for view
        $membership_status = $subscriber->subscription_status;
        //Conflate pending status for auths and payments into a single message
        if ($membership_status == 'Authorization pending') {
            $membership_status = 'Payment pending';
        }
        if ($membership_status == 'Authorization failed') {
            $membership_status = 'Payment failed';
        }
        $this->addToView('membership_status', $membership_status);

        // Add ebook download link
        if ($membership_status != 'Payment failed' && $membership_status != 'Payment pending'
            && $membership_status != 'Free trial') {
            $this->addToView('show_ebook_links', true);
        }

        //Add Free trial status (including if expired, and how many days left)
        if ($membership_status == 'Free trial') {
            $creation_date = new DateTime($subscriber->creation_time);
            $now = new DateTime();
            $end_of_trial = $creation_date->add(new DateInterval('P14D'));
            if ($end_of_trial < $now) {
                $this->addToView('trial_status', 'Expired!');
            } else {
                $datetime1 = new DateTime('2009-10-11');
                $datetime2 = new DateTime('2009-10-13');
                $interval = $now->diff($end_of_trial);
                $this->addToView('trial_status', 'expires in <strong>'.$interval->format('%a days').'</strong>');
            }
        }

        // If status is "Payment failed" or "Free trial" then send Amazon Payments URL to view and handle charge
        if ($membership_status == 'Payment failed' || $membership_status == 'Free trial') {
            $callback_url = UpstartHelper::getApplicationURL().'user/membership.php';
            $caller_reference = $subscriber->id.'_'.time();
            $amount = self::getSubscriptionAmount($subscriber->membership_level, $subscriber->subscription_recurrence);

            $api_accessor = new AmazonFPSAPIAccessor();
            $pay_with_amazon_form = $api_accessor->generateSimplePayNowForm('USD '.$amount,
                $subscriber->subscription_recurrence, 'ThinkUp.com monthly membership',
                $caller_reference, $callback_url);

            $this->addToView('pay_with_amazon_form', $pay_with_amazon_form);

            SessionCache::put('caller_reference', $caller_reference);
            if ($membership_status == 'Free trial') {
                $this->addToView('amazon_form', $pay_with_amazon_form);
            } else {
                $this->addToView('failed_cc_amazon_form', $pay_with_amazon_form);
            }

            if ($membership_status == 'Payment failed') {
                $this->addToView('failed_cc_amazon_text',
                    "There was a problem with your payment. But it's easy to fix!");
            } else {
                $this->addToView('failed_cc_amazon_text', "One last step to complete your ThinkUp membership!");
            }
        }
        //END populating membership_status

        // Add ebook download link for members who have paid successfully or been comped
        if ( (strpos( $membership_status, 'Paid through') !== false)  || $subscriber->is_membership_complimentary ) {
            $this->addToView('ebook_download_link_pdf', 'http://book.thinkup.com/insights.pdf');
            $this->addToView('ebook_download_link_kindle', 'http://book.thinkup.com/insights.mobi');
            $this->addToView('ebook_download_link_epub', 'http://book.thinkup.com/insights.epub');
        }

        //BEGIN populating nav bar icons
        $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
        $instances = $tu_tables_dao->getInstancesWithStatus($subscriber->thinkup_username, 2);

        //Start off assuming connection doesn't exist
        $connection_status = array('facebook'=>'inactive', 'twitter'=>'inactive');
        foreach ($instances as $instance) {
            if ($instance['auth_error'] != '') {
                $connection_status[$instance['network']] = 'error';
            } else { //connection exists, so it's active
                $connection_status[$instance['network']] = 'active';
            }
        }
        $this->addToView('facebook_connection_status', $connection_status['facebook']);
        $this->addToView('twitter_connection_status', $connection_status['twitter']);
        //END populating nav bar icons

        return $this->generateView();
	}

    /**
     * Whether or not user has requested account closure.
     * @return bool
     */
    private function hasUserRequestedAccountClosure() {
        return (isset($_GET['close'])  && $_GET['close'] == 'true');
    }

    /**
     * Whether or not user has requested account re-opening.
     * @return bool
     */
    private function hasUserRequestedAccountReopening() {
        return (isset($_GET['reopen'])  && $_GET['reopen'] == 'true');
    }

    /**
     * Whether or not user has returned from paying at Amazon.
     * @return bool
     */
    private function hasUserReturnedFromAmazon() {
        return (isset($_GET['callerReference'])  && isset($_GET['tokenID'])
        && isset($_GET['status']) && isset($_GET['certificateUrl']) && isset($_GET['signatureMethod'])
        && isset($_GET['signature']) );
    }

    /**
     * Whether or not the response from Amazon is valid - if caller reference matches, if signature verifies.
     * @param  str  $internal_caller_reference
     * @return bool
     */
    private function isAmazonResponseValid($internal_caller_reference) {
        //Check inputs match internal rules
        $endpoint_url = UpstartHelper::getApplicationURL().'user/membership.php';
        return ($internal_caller_reference == $_GET['callerReference']
        && AmazonFPSAPIAccessor::isAmazonSignatureValid($endpoint_url));
    }

    /**
     * Get amount a subscription type costs per year in US Dollars.
     * @param  str $membership_level Early Bird, Member, Pro, Exec, Late Bird
     * @param  str $recurrence_frequency '1 month' or '12 months'
     * @return int
     */
    private function getSubscriptionAmount($membership_level, $recurrence_frequency) {
        $normalized_membership_level = strtolower($membership_level);
        $normalized_membership_level =
            ($normalized_membership_level == 'late bird')?'earlybird':$normalized_membership_level;
        $normalized_membership_level =
            ($normalized_membership_level == 'early bird')?'earlybird':$normalized_membership_level;
        $normalized_membership_level =
            ($normalized_membership_level == 'exec')?'executive':$normalized_membership_level;
        if (!in_array( $normalized_membership_level, array_keys(SignUpHelperController::$subscription_levels))) {
            throw new Exception('No amount found for '.$normalized_membership_level);
        } else {
            if (!in_array($recurrence_frequency,
                array_keys(SignUpHelperController::$subscription_levels[$normalized_membership_level]))) {
                throw new Exception('No amount found for '.$normalized_membership_level. " ".$recurrence_frequency);
            } else {
                return
                    SignUpHelperController::$subscription_levels[$normalized_membership_level][$recurrence_frequency];
            }
        }
    }
}
