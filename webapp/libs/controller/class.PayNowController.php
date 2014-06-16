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
            (strtolower($subscriber->membership_level));
        $amount = SignUpHelperController::$subscription_levels[strtolower($subscriber->membership_level)];
        $pay_with_amazon_url = AmazonFPSAPIAccessor::getAmazonFPSURL($caller_reference, $callback_url, $amount);

        $this->addToView('pay_with_amazon_url', $pay_with_amazon_url);

        $cfg = Config::getInstance();
        $user_installation_url = $cfg->getValue('user_installation_url');
        $subscriber->installation_url = str_replace ("{user}", $subscriber->thinkup_username, $user_installation_url);
        $this->addToView('new_subscriber', $subscriber);

        return $this->generateView();
    }
}
