<?php

class CheckoutController extends UpstartAuthController {

    public function authControl() {
        $logged_in_user = Session::getLoggedInUser();
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail($logged_in_user);

        //Send already-paid user to their Membership page
        if ($subscriber->subscription_status == 'Paid' || $subscriber->is_membership_complimentary) {
            $controller = new MembershipController(true);
            return $controller->go();
        }
        $this->disableCaching();
        $this->setPageTitle('Checkout');
        $this->setViewTemplate('checkout.tpl');

        $normalized_membership_level = strtolower($subscriber->membership_level);
        $normalized_membership_level =
            ($normalized_membership_level == 'late bird')?'member':$normalized_membership_level;
        $normalized_membership_level =
            ($normalized_membership_level == 'early bird')?'member':$normalized_membership_level;
        $normalized_membership_level =
            ($normalized_membership_level == 'exec')?'executive':$normalized_membership_level;

        $amount_monthly = SignUpHelperController::$subscription_levels[$normalized_membership_level]['1 month'];
        $amount_yearly = SignUpHelperController::$subscription_levels[$normalized_membership_level]
            ['12 months discount'];
        $this->addToView('normalized_membership_level', $normalized_membership_level);
        $this->addToView('amount_monthly', $amount_monthly);
        $this->addToView('amount_yearly', $amount_yearly);

        /**
         * Create account with billing id
         * Create subscription with account id
         */
        if (isset($_POST['amazon_billing_agreement_id']) && isset($_POST['plan'])) {
            // Required for the Recurly API
            $cfg = Config::getInstance();
            Recurly_Client::$subdomain = $cfg->getValue('recurly_subdomain');
            Recurly_Client::$apiKey = $cfg->getValue('recurly_api_key');

            $subscription = new Recurly_Subscription();
            $subscription->plan_code = $_POST['plan'];
            $subscription->currency = 'USD';

            $account = new Recurly_Account();
            $account->account_code = $subscriber->id;
            $account->email = $subscriber->email;
            // $account->create(); //Does Recurly do this on $subscription->create() if the account doesn't exist?

            $billing_info = new Recurly_BillingInfo();
            $billing_info->amazon_billing_agreement_id = $_POST['amazon_billing_agreement_id'];

            $account->billing_info = $billing_info;
            $subscription->account = $account;

            try {
                $subscription->create();

                //Update local subscription details
                $subscriber->subscription_status = 'Paid';
                $subscriber->is_via_recurly = true;
                if (strpos($_POST['plan'], 'monthly') !== false) {
                    $subscriber->subscription_recurrence = '1 month';
                } elseif (strpos($_POST['plan'], 'yearly') !== false) {
                    $subscriber->subscription_recurrence = '12 months';
                }
                $paid_through_time = strtotime('+'.$subscriber->subscription_recurrence);
                $paid_through_time = date('Y-m-d H:i:s', $paid_through_time);
                $subscriber->paid_through = $paid_through_time;

                $subscriber_dao->setSubscriptionDetails($subscriber);

                //Send paid user to their insights stream
                if ($subscriber->subscription_status == 'Paid') {
                    $state = 'success';
                }

                //Update is_free_trial field in ThinkUp installation
                $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
                $trial_ended = $tu_tables_dao->endFreeTrial($subscriber->email);
                if (!$trial_ended) {
                    Logger::logError('Unable to end trial in ThinkUp installation',
                        __FILE__,__LINE__, __METHOD__);
                }

                UpstartHelper::postToSlack('#signups',
                    'Ding-ding! A member just paid for a '.$_POST['plan'].' subscription via Recurly.\nhttps://'.
                    $subscriber->thinkup_username.
                    '.thinkup.com\nhttps://www.thinkup.com/join/admin/subscriber.php?id='.
                    $subscriber->id);
            } catch (Recurly_ValidationError $e) {
                $this->addErrorMessage('Oops! '.$e->getMessage());
                $debug = "Recurly_Validation_Error: ". $e->getMessage();
                Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
                $state = 'error';
            }
        } else {
            $state = 'pay';
        }
        $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
            Config::getInstance()->getValue('user_installation_url'));
        $this->addToView('user_installation_url', $user_installation_url);

        $this->addToView('subscriber', $subscriber);

        //Populate view variables
        //Get Context
        //{assign var="context" value=$smarty.get.context} <!-- membership or signup -->
        $new_subscriber_id = SessionCache::get('new_subscriber_id');
        if (isset($new_subscriber_id)) {
            $context = 'signup';
        } else {
            $context = 'membership';
        }
        $this->addToView('context', $context);

        //Get Membership status
        //{assign var="membership_status"} <!-- trial or other(expired, due, failed) -->
        if ($subscriber->subscription_status == 'Free trial') {
            $days_left_in_trial = $subscriber->getDaysLeftInFreeTrial();
            if ($days_left_in_trial < 1) {
                $membership_status = 'expiring trial';
            } else {
                $membership_status = 'trial';
            }
        } else {
            $membership_status = 'due';
        }
        $this->addToView('membership_status', $membership_status);

        //Get state
        //{assign var="state"} <!-- pay or success or error or error-fullname-->
        //@TODO Detect single name error
        $this->addToView('state', $state);

        return $this->generateView();
    }
}