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

        // Process payment if returned from Amazon
        if (self::hasUserReturnedFromAmazon()) {
            if ($this->isAmazonResponseValid()) {
                //@TODO Double-check $_GET[errorMessage] is set if there's an error
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

                            //Figure out how many days left in the trial
                            $days_left = $subscriber->getDaysLeftInFreeTrial();
                            UpstartHelper::postToSlack('#signups',
                                'Hooray! Someone just subscribed from their membership page with '
                                .$days_left." day".(($days_left != 1)?'s':'')." left in their trial."
                                .'\nhttps://'. $subscriber->thinkup_username.
                                '.thinkup.com\nhttps://www.thinkup.com/join/admin/subscriber.php?id='.
                                $subscriber->id);
                        } else {
                            $this->addErrorMessage($this->generic_error_msg);
                            $this->logError('Subscription status code is not SF. '. Utils::varDumpToString($op),
                                __FILE__, __LINE__, __METHOD__ );
                        }

                        //Now that user has created a subscription, set new paid_through date and update status
                        $subscription_helper = new SubscriptionHelper();
                        $subscription_helper->updateSubscriptionStatusAndPaidThrough($subscriber, $op);

                        //Update recurrence_frequency in the data store
                        $subscriber_dao->updateSubscriptionRecurrence($subscriber->id, $op->recurring_frequency);

                        //Refresh subscriber object with new field values
                        $subscriber = $subscriber_dao->getByEmail($logged_in_user);

                        //Update is_free_trial field in ThinkUp installation
                        $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
                        $trial_ended = $tu_tables_dao->endFreeTrial($subscriber->email);
                        if (!$trial_ended) {
                            $this->logError('Unable to end trial in ThinkUp installation',
                                __FILE__,__LINE__, __METHOD__);
                        }
                    } catch (DuplicateSubscriptionOperationException $e) {
                        $this->addSuccessMessage("Whoa there! It looks like you already paid for your ThinkUp ".
                            "subscription. Maybe you refreshed the page in your browser?");
                    }
                }
            } else {
                $this->addErrorMessage("Oops! Something went wrong. Please try again or contact us for help.");
                $this->logError('Amazon response invalid', __FILE__,__LINE__,
                __METHOD__);
            }
        }

        //Process claim code
        if (isset($_POST['claim_code'])) {
            //Check if claim code is valid
            $claim_code_dao = new ClaimCodeMySQLDAO();
            //Strip spaces and go uppercase
            $code_str = str_replace(' ', '', strtoupper($_POST['claim_code']));
            $claim_code = $claim_code_dao->get($code_str);
            if (isset($claim_code)) {
                if ($claim_code->is_redeemed) {
                    $this->addErrorMessage('Whoops! It looks like that code has already been used.');
                } else {
                    $code_redemption_update = $claim_code_dao->redeem($claim_code->code);
                    if ($code_redemption_update > 0) {
                        $subscriber_redemption_update = $subscriber_dao->redeemClaimCode($subscriber->id, $claim_code);
                        if ($subscriber_redemption_update > 0) {
                            $this->addSuccessMessage("It worked! We've applied your coupon code.");
                            //Refresh subscriber object with new field values
                            $subscriber = $subscriber_dao->getByEmail($logged_in_user);
                        } else {
                            $this->addErrorMessage("Oops! There was a problem processing your code. Please try again.");
                        }
                    } else {
                        $this->addErrorMessage("Oops! There was a problem processing your code. Please try again.");
                    }
                }
            } else {
                $this->addErrorMessage("That code doesn't seem right. Check it and try again?");
            }
        }

        try {
            if (self::hasUserRequestedAccountClosure() && $this->validateCSRFToken()) {
                if (!$subscriber->is_account_closed) {
                    //Get SubscriptionId for logged-in subscriber
                    $sub_op_dao = new SubscriptionOperationMySQLDAO();
                    $operation = $sub_op_dao->getLatestOperation($subscriber->id);
                    if (isset($operation)) { // Member has paid for subscription, we're going to issue a refund
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
                                $op_cancel->transaction_amount = "USD ".$refund_amount;
                                $op_cancel->status_code = '';
                                $op_cancel->buyer_email = $operation->buyer_email;
                                //@TODO Verify the reference_id starts with the subscriber ID
                                $op_cancel->reference_id = $caller_reference;
                                $op_cancel->amazon_subscription_id = $operation->amazon_subscription_id;
                                $op_cancel->transaction_date = time();
                                $op_cancel->buyer_name = $operation->buyer_name;
                                $op_cancel->operation = 'refund';
                                $op_cancel->recurring_frequency = $operation->recurring_frequency;
                                $op_cancel->payment_method = $operation->payment_method;
                                $sub_op_dao->insert($op_cancel);

                                // Update subscription status
                                $subscription_helper = new SubscriptionHelper();
                                $subscription_helper->updateSubscriptionStatusAndPaidThrough($subscriber, $op_cancel);

                                // Close account
                                $result = $subscriber_dao->closeAccount($subscriber->id);

                                // Send account closure email
                                $email_view_mgr = new ViewManager();
                                $email_view_mgr->caching=false;
                                $template_name = "Upstart System Messages";
                                $api_key = Config::getInstance()->getValue('mandrill_api_key_for_payment_reminders');

                                $subject_line = "Your ThinkUp account has been closed";
                                $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
                                $email_view_mgr->assign('refund_amount', $refund_amount);
                                $body_html = $email_view_mgr->fetch('_email.account-closed.tpl');
                                $headline = "Thanks for trying ThinkUp.";
                                $message = Mailer::getSystemMessageHTML($body_html, $headline);
                                try {
                                    Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line,
                                        $template_name, array('html_body'=>$message), $api_key);
                                    // Log user out with message about closure and refund
                                    $logout_controller = new LogoutController(true);
                                    $logout_controller->addSuccessMessage("Your ThinkUp account is closed, ".
                                        "and we've issued a refund.  Thanks for trying ThinkUp!");

                                    return $logout_controller->control();
                                } catch (Exception $e) {
                                    $this->addErrorMessage($e->getMessage());
                                }
                            } else {
                                //Show user error, log system error
                                $this->logError('Amazon refund response was null. Refund operation was '.
                                    Utils::varDumpToString($op_cancel), __FILE__, __LINE__, __METHOD__);
                                $this->addErrorMessage($this->generic_error_msg);
                            }
                        } catch (Amazon_FPS_Exception $ex) {
                            $debug = "Caught Exception: " . $ex->getMessage() . "\n";
                            $debug .= "Response Status Code: " . $ex->getStatusCode() . "\n";
                            $debug .= "Error Code: " . $ex->getErrorCode() . "\n";
                            $debug .= "Error Type: " . $ex->getErrorType() . "\n";
                            $debug .= "Request ID: " . $ex->getRequestId() . "\n";
                            $debug .= "XML: " . $ex->getXML() . "\n";
                            $this->logError($debug, __FILE__, __LINE__, __METHOD__);
                            $this->addErrorMessage($this->generic_error_msg);
                        }
                    } else { // Free trial, no need to refund
                        // Close account
                        $result = $subscriber_dao->closeAccount($subscriber->id);

                        // Log user out with message about closure and refund
                        $logout_controller = new LogoutController(true);
                        $logout_controller->addSuccessMessage("Your ThinkUp account is closed. ".
                            "Thanks for trying ThinkUp!");
                        return $logout_controller->control();
                    }
                } else {
                    $this->addErrorMessage("This account is already closed. Please log out.");
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
        if ($membership_status == 'Paid') {
            $membership_status = "Paid through ".(date('M j, Y', strtotime($subscriber->paid_through)));
        }
        $this->addToView('membership_status', $membership_status);

        // Add ebook download link
        if ($membership_status != 'Payment failed' && $membership_status != 'Payment pending'
            && $membership_status != 'Free trial') {
            $this->addToView('show_ebook_links', true);
        }

        //Add Free trial status (including if expired, and how many days left)
        if ($membership_status == 'Free trial') {
            $days_left_in_trial = $subscriber->getDaysLeftInFreeTrial();
            if ($days_left_in_trial < 1) {
                $this->addToView('trial_status', 'Expired!');
            } else {
                $this->addToView('trial_status', 'expires in <strong>'.$days_left_in_trial.' day'
                    .(($days_left_in_trial > 1)?'s':'').'</strong>');
            }
        }

        // If status is "Payment failed" or "Free trial" then send Amazon Payments URL to view and handle charge
        if ($membership_status == 'Payment failed' || $membership_status == 'Free trial') {
            $callback_url = UpstartHelper::getApplicationURL().'user/membership.php';
            $caller_reference = $subscriber->id.'_'.time();
            $amount = self::getSubscriptionAmount($subscriber->membership_level, $subscriber->subscription_recurrence);

            $api_accessor = new AmazonFPSAPIAccessor();
            $pay_with_amazon_form = $api_accessor->generateSubscribeForm('USD '.$amount,
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

        //Set Amazon payments link to sandbox for testing
        $this->addToView('amazon_sandbox', $config->getValue('amazon_sandbox'));
        return $this->generateView();
	}

    /**
     * Whether or not user has requested account closure.
     * @return bool
     */
    private function hasUserRequestedAccountClosure() {
        return (isset($_POST['close'])  && $_POST['close'] == 'true');
    }

    /**
     * Return whether or not user has returned from Amazon with necessary parameters.
     * @return bool
     */
    protected function hasUserReturnedFromAmazon() {
        return UpstartHelper::areGetParamsSet(SignUpHelperController::$amazon_simple_pay_subscription_return_params);
    }

    /**
     * Whether or not the response from Amazon has a valid signature.
     * @return bool
     */
    private function isAmazonResponseValid() {
        //Check inputs match internal rules
        $endpoint_url = UpstartHelper::getApplicationURL().'user/membership.php';
        return AmazonFPSAPIAccessor::isAmazonSignatureValid($endpoint_url);
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
