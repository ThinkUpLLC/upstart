<?php
class MembershipController extends UpstartAuthController {

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

                            //Update is_free_trial field in ThinkUp installation
                            $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
                            $trial_ended = $tu_tables_dao->endFreeTrial($subscriber->email);
                            if (!$trial_ended) {
                                Logger::logError('Unable to end trial in ThinkUp installation',
                                    __FILE__,__LINE__, __METHOD__);
                            }
                            UpstartHelper::postToSlack('#thinkup-signups',
                                'Yes! Someone just redeemed a coupon code on their membership page.'
                                .'\nhttps://'. $subscriber->thinkup_username.
                                '.thinkup.com\nhttps://www.thinkup.com/join/admin/subscriber.php?id='.
                                $subscriber->id);
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
                    if ($subscriber->is_via_recurly) {
                        if ($this->cancelRecurlySubscription($subscriber)) {
                            // Log user out with message about closure and refund
                            $logout_controller = new LogoutController(true);
                            $logout_controller->addSuccessMessage("Your ThinkUp account is closed, ".
                                "and we've issued a refund. Thanks for trying ThinkUp!");
                            return $logout_controller->control();
                        }
                    } else {
                        // This subscriber is an FPS or free trial user
                        // We can no longer issue refunds through FPS because the API doesn't exist anymore!
                        // Close account and don't send an email about it
                        $result = $subscriber_dao->closeAccount($subscriber->id);

                        //Log user out with message about closure and refund
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
                $this->addToView('trial_status', 'expired');
            } else {
                $this->addToView('trial_status', 'expires in <strong>'.$days_left_in_trial.' day'
                    .(($days_left_in_trial > 1)?'s':'').'</strong>');
            }
        }

        // If status is "Payment failed" or "Free trial" or "Payment due"
        // then send Amazon Payments URL to view and handle charge
        if ($membership_status == 'Payment failed' || $membership_status == 'Free trial'
            || $membership_status == 'Payment due') {

            $this->addToView('show_checkout_button', true);

            if ($membership_status == 'Payment failed') {
                $this->addToView('failed_cc_amazon_text',
                    "There was a problem with your payment. But it's easy to fix!");
            } else {
                $this->addToView('failed_cc_amazon_text', "One last step to complete your ThinkUp membership!");
            }
            $checkout_button = SubscriptionHelper::getCheckoutButton($subscriber);
            $this->addToView('checkout_button', $checkout_button);
        }
        //END populating membership_status

        // Add ebook download link for members who have paid successfully or been comped
        if ( $subscriber->subscription_status == 'Paid' || $subscriber->is_membership_complimentary ) {
            $this->addToView('ebook_download_link_pdf', 'http://book.thinkup.com/insights.pdf');
            $this->addToView('ebook_download_link_kindle', 'http://book.thinkup.com/insights.mobi');
            $this->addToView('ebook_download_link_epub', 'http://book.thinkup.com/insights.epub');
        }

        //BEGIN populating nav bar icons
        $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
        $instances = $tu_tables_dao->getInstancesWithStatus($subscriber->thinkup_username, 2);

        //Start off assuming connection doesn't exist
        $connection_status = array('facebook'=>'inactive', 'twitter'=>'inactive', 'instagram'=>'inactive');
        foreach ($instances as $instance) {
            if ($instance['auth_error'] != '') {
                $connection_status[$instance['network']] = 'error';
            } else { //connection exists, so it's active
                $connection_status[$instance['network']] = 'active';
            }
        }
        $this->addToView('facebook_connection_status', $connection_status['facebook']);
        $this->addToView('twitter_connection_status', $connection_status['twitter']);
        $this->addToView('instagram_connection_status', $connection_status['instagram']);
        //END populating nav bar icons

        //Set Amazon payments link to sandbox for testing
        $this->addToView('amazon_sandbox', $config->getValue('amazon_sandbox'));

        return $this->generateView();
    }
    /**
     * Send account closure email and notify Slack of account closure.
     * @param  Subscriber $subscriber
     * @param  int $refund_amount
     * @param  str $account_type
     * @return bool
     */
    private function sendAccountClosureEmail(Subscriber $subscriber, $refund_amount, $account_type = '') {
        // Send account closure email
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $template_name = "Upstart System Messages";
        $api_key = Config::getInstance()->getValue('mandrill_api_key_for_payment_reminders');

        $subject_line = "Your ThinkUp account has been closed";
        $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
        $refund_amount = number_format($refund_amount, 2);
        $email_view_mgr->assign('refund_amount', $refund_amount);
        $body_html = $email_view_mgr->fetch('_email.account-closed.tpl');
        $headline = "Thanks for trying ThinkUp.";
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        try {
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            UpstartHelper::postToSlack('#thinkup-signups',
                $subscriber->thinkup_username.' account is closed ('.$account_type.'). Refunded $'.$refund_amount."."
                .'\nhttps://www.thinkup.com/join/admin/subscriber.php?id='. $subscriber->id);
        } catch (Exception $e) {
            $this->addErrorMessage($e->getMessage());
            return false;
        }
        return true;
    }
    /**
     * Whether or not user has requested account closure.
     * @return bool
     */
    private function hasUserRequestedAccountClosure() {
        return (isset($_POST['close'])  && $_POST['close'] == 'true');
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
    /**
     * Cancel and issue refund for a subscription paid via Recurly.
     * @param  Subscriber $subscriber
     * @return bool
     */
    private function cancelRecurlySubscription(Subscriber $subscriber) {
        // Required for the Recurly API
        $cfg = Config::getInstance();
        Recurly_Client::$subdomain = $cfg->getValue('recurly_subdomain');
        Recurly_Client::$apiKey = $cfg->getValue('recurly_api_key');
        //@TODO catch any Recurly exceptions
        $subscriptions = Recurly_SubscriptionList::getForAccount($subscriber->id);
        foreach ($subscriptions as $subscription) {
            if ($subscription->state == 'active') {
                $subscription->terminateAndPartialRefund();
                // Close account
                $subscriber_dao = new SubscriberMySQLDAO();
                $result = $subscriber_dao->setSubscriptionStatus($subscriber->id, "Refunded");
                $result = $subscriber_dao->closeAccount($subscriber->id);
                // Send account closure email
                if ($this->sendAccountClosureEmail($subscriber, 0, 'Recurly')) {
                    return true;
                }
            }
        }
        return false;
    }
}