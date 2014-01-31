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

        //BEGIN populating membership_status
        $membership_status = $subscriber->getAccountStatus();
        //Conflate pending status for auths and payments into a single message
        if ($membership_status == 'Authorization pending') {
            $membership_status = 'Payment pending';
        }
        if ($membership_status == 'Authorization failed') {
            $membership_status = 'Payment failed';
        }
        $this->addToView('membership_status', $membership_status);

        // Process payment if returned from Amazon
        if (self::hasUserReturnedFromAmazon()) {
            $internal_caller_reference = SessionCache::get('caller_reference');
            //@TODO Move and genericize isAmazonSignatureValid to AmazonFPSAPIAccessor
            if (isset($internal_caller_reference) && $this->isAmazonResponseValid($internal_caller_reference)) {
                $amazon_caller_reference = $_GET['callerReference'];

                $error_message = isset($_GET["errorMessage"])?$_GET["errorMessage"]:null;
                if ($error_message === null ) {
                    $this->addSuccessMessage("Thanks so much for subscribing to ThinkUp!");
                } else {
                    $this->addErrorMessage($generic_error_msg);
                    $this->logError("Amazon returned error: ".$error_message, __FILE__,__LINE__,__METHOD__);
                }

                //Record authorization
                $authorization_dao = new AuthorizationMySQLDAO();
                $amount = SignUpController::$subscription_levels[$_GET['l']];
                $payment_expiry_date = (isset($_GET['expiry']))?$_GET['expiry']:null;

                try {
                    $authorization_id = $authorization_dao->insert($_GET['tokenID'], $amount, $_GET["status"],
                    $internal_caller_reference, $error_message, $payment_expiry_date);
                } catch (DuplicateAuthorizationException $e) {
                    $this->addSuccessMessage("Whoa there! It looks like you already paid for your ThinkUp ".
                    "subscription.  Did you refresh the page?");
                }

                //@TODO Charge for authorization
            }
        } else {
            // If status is "Payment failed" then send Amazon Payments URL to view and handle charge
            if ($membership_status == 'Payment failed') {
                $callback_url = UpstartHelper::getApplicationURL().'user/membership.php';
                $caller_reference = $subscriber->id.'_'.time();
                $normalized_membership_level = strtolower($subscriber->membership_level);
                $normalized_membership_level =
                    ($normalized_membership_level == 'late bird')?'early bird':$normalized_membership_level;
                if (!in_array( $normalized_membership_level, SignUpController::$subscription_levels)) {
                    throw Exception('No amount found for '.$normalized_membership_level);
                } else {
                    $amount = SignUpController::$subscription_levels[$normalized_membership_level];
                }
                $amazon_url = AmazonFPSAPIAccessor::getAmazonFPSURL( $caller_reference, $callback_url, $amount );
                SessionCache::put('caller_reference', $caller_reference);
                $this->addToView('failed_cc_amazon_link', $amazon_url);
            }
        }
        //END populating membership_status

        //BEGIN populating nav bar icons
        $tu_tables_dao = new ThinkUpTablesMySQLDAO();
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

    private function hasUserReturnedFromAmazon() {
        return (isset($_GET['callerReference'])  && isset($_GET['tokenID'])
        && isset($_GET['status']) && isset($_GET['certificateUrl']) && isset($_GET['signatureMethod'])
        && isset($_GET['signature']) );
    }
}
