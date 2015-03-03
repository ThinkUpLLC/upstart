<?php
class CheckoutController extends AuthController {

    const RECURLY_SUBDOMAIN = 'thinkup';

    const RECURLY_API_KEY = 'f25cf396be0548f5b40308ffcd0c5aff';

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
        //$this->enableCSRFToken();
        $this->setPageTitle('Checkout');
        $this->setViewTemplate('checkout.tpl');

        $amount_monthly = SignUpHelperController::$subscription_levels[strtolower($subscriber->membership_level)]
            ['1 month'];
        $amount_yearly = SignUpHelperController::$subscription_levels[strtolower($subscriber->membership_level)]
            ['12 months discount'];
        $this->addToView('amount_monthly', $amount_monthly);
        $this->addToView('amount_yearly', $amount_yearly);

        /**
         * Create account with billing id
         * Create subscription with account id
         */
        if (isset($_POST['amazon_billing_agreement_id']) && isset($_POST['plan'])) {
            // Required for the Recurly API
            Recurly_Client::$subdomain = self::RECURLY_SUBDOMAIN;
            Recurly_Client::$apiKey = self::RECURLY_API_KEY;

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
                if ($_POST['plan'] == 'member-monthly') {
                    $subscriber->subscription_recurrence = '1 month';
                } elseif ($_POST['plan'] == 'member-annual') {
                    $subscriber->subscription_recurrence = '12 months';
                }
                $paid_through_time = strtotime('+'.$subscriber->subscription_recurrence);
                $paid_through_time = date('Y-m-d H:i:s', $paid_through_time);
                $subscriber->paid_through = $paid_through_time;

                $subscriber_dao->setSubscriptionDetails($subscriber);

                //Send paid user to their insights stream
                if ($subscriber->subscription_status == 'Paid') {
                    $this->addToView('state', 'payment_successful');
                }
            } catch (Recurly_ValidationError $e) {
                $this->addErrorMessage('Oops! There was a problem. '.$e->getMessage());
                $this->addToView('state', 'prompt_for_payment');
            }
        } else {
            $this->addToView('state', 'prompt_for_payment');
        }
        $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
            Config::getInstance()->getValue('user_installation_url'));

        $error = "That plan does not exist.";
        $this->addErrorMessage($error);
        $this->addToView('user_installation_url', $user_installation_url);
        $this->addToView('subscriber', $subscriber);

        return $this->generateView();
    }
}