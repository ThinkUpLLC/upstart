<?php
class PayNowController extends Controller {
    /**
     * @var array Options for notification frequency
     */
    protected $notification_frequencies = array('daily'=>'Daily','weekly'=>'Weekly', 'never'=>'Never');

    public function control() {
        $this->disableCaching();
        $this->setPageTitle('Pay now and get a free gift');
        $this->setViewTemplate('paynow.tpl');

        $new_subscriber_id = SessionCache::get('new_subscriber_id');
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID($new_subscriber_id);

        //Get Amazon URL
        $caller_reference = $new_subscriber_id.'_'.time();
        $callback_url = UpstartHelper::getApplicationURL().'confirm-payment.php?level='.
            (strtolower($subscriber->membership_level)).'&recur='.
            urlencode($subscriber->subscription_recurrence);
        $amount = SignUpHelperController::$subscription_levels[strtolower($subscriber->membership_level)]
            [$subscriber->subscription_recurrence];
        $api_accessor = new AmazonFPSAPIAccessor();
        $pay_with_amazon_form = $api_accessor->generateSimplePayNowForm('USD '.$amount,
            $subscriber->subscription_recurrence, 'ThinkUp.com membership', $caller_reference, $callback_url);

        $this->addToView('pay_with_amazon_form', $pay_with_amazon_form);

        $cfg = Config::getInstance();
        $user_installation_url = $cfg->getValue('user_installation_url');
        $subscriber->installation_url = str_replace ("{user}", $subscriber->thinkup_username, $user_installation_url);
        $this->addToView('new_subscriber', $subscriber);
        $this->addToView('thinkup_url', $subscriber->installation_url);

        return $this->generateView();
    }
}
