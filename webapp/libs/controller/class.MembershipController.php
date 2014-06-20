<?php
class MembershipController extends AuthController {

    public function authControl() {
        $this->setPageTitle('Membership Info');
        $this->setViewTemplate('user.membership.tpl');
        $this->disableCaching();

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
            $internal_caller_reference = SessionCache::get('caller_reference');
            if (isset($internal_caller_reference) && $this->isAmazonResponseValid($internal_caller_reference)) {
                $amazon_caller_reference = $_GET['callerReference'];

                $error_message = isset($_GET["errorMessage"])?$_GET["errorMessage"]:null;
                if ($error_message !== null ) {
                    $this->addErrorMessage($this->generic_error_msg);
                    $this->logError("Amazon returned error: ".$error_message, __FILE__,__LINE__,__METHOD__);
                } else {
                    //Record authorization
                    $authorization_dao = new AuthorizationMySQLDAO();
                    $amount = self::getSubscriptionAmount($subscriber->membership_level);
                    $payment_expiry_date = (isset($_GET['expiry']))?$_GET['expiry']:null;
                    $token_id = (isset($_GET['tokenID']))?$_GET['tokenID']:null;
                    try {
                        $authorization_id = $authorization_dao->insert($token_id, $amount, $_GET["status"],
                            $internal_caller_reference, $error_message, $payment_expiry_date);

                        //Save authorization ID and subscriber ID in subscriber_authorizations table.
                        $subscriber_authorization_dao = new SubscriberAuthorizationMySQLDAO();
                        $subscriber_authorization_dao->insert($subscriber->id, $authorization_id);

                        //Charge for authorization
                        $api_accessor = new AmazonFPSAPIAccessor();
                        $is_charge_successful = $api_accessor->invokeAmazonPayAction($subscriber->id, $token_id,
                            $amount);

                        if ($is_charge_successful) {
                            $this->addSuccessMessage("Success! Thanks for being a ThinkUp member.");
                        } else {
                            $this->addErrorMessage("Oops! Something went wrong. ".
                                "Please try again or contact us for help.");
                            $this->logError('Amazon charge was unsuccessful', __FILE__,__LINE__, __METHOD__);
                        }
                        //Now that user has authed and paid, get current subscription_status
                        $subscription_status = $subscriber->getSubscriptionStatus();
                        //Update subscription_status in the subscriber object
                        $subscriber->subscription_status = $subscription_status;
                        //Update subscription_status in the data store
                        $subscriber_dao->updateSubscriptionStatus($subscriber->id, $subscription_status);
                    } catch (DuplicateAuthorizationException $e) {
                        $this->addSuccessMessage("Whoa there! It looks like you already paid for your ThinkUp ".
                        "membership.  Did you refresh the page?");
                    }
                }
            } else {
                $this->addErrorMessage("Oops! Something went wrong. Please try again or contact us for help.");
                $this->logError('Internal caller reference not set or Amazon response invalid', __FILE__,__LINE__,
                __METHOD__);
            }
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

        //Add Free trial status (including if expired, and how many days left)
        $this->addToView('membership_status', $membership_status);
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
                $this->addToView('trial_status', 'Your free trial expires in '.$interval->format('%a days'));
            }
        }

        // If status is "Payment failed" or "Payment due" then send Amazon Payments URL to view and handle charge
        if ($membership_status == 'Payment failed' || $membership_status == 'Payment due'
            || $membership_status == 'Free trial') {
            $callback_url = UpstartHelper::getApplicationURL().'user/membership.php';
            $caller_reference = $subscriber->id.'_'.time();
            $amount = self::getSubscriptionAmount($subscriber->membership_level);
            $amazon_url = AmazonFPSAPIAccessor::getAmazonFPSURL( $caller_reference, $callback_url, $amount );
            SessionCache::put('caller_reference', $caller_reference);
            $this->addToView('failed_cc_amazon_link', $amazon_url);
        }
        //END populating membership_status

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
     * @return int
     */
    private function getSubscriptionAmount($membership_level) {
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
            return SignUpHelperController::$subscription_levels[$normalized_membership_level];
        }
    }
}
