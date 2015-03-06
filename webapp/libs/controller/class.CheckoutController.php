<?php
class CheckoutController extends AuthController {

    const RECURLY_SUBDOMAIN = 'thinkup';

    const RECURLY_API_KEY = 'f25cf396be0548f5b40308ffcd0c5aff';

    public function authControl() {
        $this->disableCaching();
        //$this->enableCSRFToken();
        $this->setPageTitle('Checkout');
        $this->setViewTemplate('checkout.tpl');

        $logged_in_user = Session::getLoggedInUser();
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail($logged_in_user);
        $this->addToView('subscriber', $subscriber);
        /**
         * Create account with billing id
         * Create subscription with account id
         */
        if (isset($_POST['amazon_billing_agreement_id'])) {
            // Required for the Recurly API
            Recurly_Client::$subdomain = self::RECURLY_SUBDOMAIN;
            Recurly_Client::$apiKey = self::RECURLY_API_KEY;

            $subscription = new Recurly_Subscription();
            $subscription->plan_code = strtolower($subscriber->membership_level).'-monthly';
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
                $this->addSuccessMessage('Subscription created!');
                //@TODO Set subscriber status to paid
            } catch (Recurly_ValidationError $e) {
                $this->addErrorMessage('Oops! There was a problem. '.$e->getMessage());
            }
        } else {
            //@TODO Check if subscriber has an active subscription; if so, add 'cancel subscription' button
            $this->addToView('show_form', true);
        }

        return $this->generateView();
    }
}