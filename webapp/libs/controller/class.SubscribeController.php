<?php
/**
 * Create interface to Amazon Flexible Payment System
 */
class SubscribeController extends Controller {
    public function control() {
        $this->setViewTemplate('pledge-subscribe.tpl');
        $click_dao = new ClickMySQLDAO();
        $caller_reference = $click_dao->insert();
        $this->addToView('caller_reference', $caller_reference);
        SessionCache::put('caller_reference', $caller_reference);

        $subscriber_dao = new SubscriberMySQLDAO();
        $total_subscribers = $subscriber_dao->getTotalSubscribers($amount = 0);
        $this->addToView('total_subscribers', $total_subscribers);

        foreach (SignUpController::$subscription_levels as $level=>$amount) {
            //Get Amazon URL
            $callback_url = UpstartHelper::getApplicationURL().'new.php?l='.$level;
            $subscribe_url = self::getAmazonFPSURL($caller_reference, $callback_url, $amount);
            $this->addToView('subscribe_'.$level.'_url', $subscribe_url);

            //Get subscriber totals
            $total_backers = $subscriber_dao->getTotalSubscribers($amount);
            $this->addToView('total_'.$level.'_subscribers', $total_backers);
        }
        return $this->generateView();
    }

    private static function getAmazonFPSURL($caller_reference, $callback_url, $amount) {
        $cfg = Config::getInstance();
        $AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');

        $pipeline = new Amazon_FPS_CBUIRecurringTokenPipeline($AWS_ACCESS_KEY_ID, $AWS_SECRET_ACCESS_KEY);

        $pipeline->setMandatoryParameters($caller_reference, $callback_url, $amount, "12 Months");

        //optional parameters
        $pipeline->addParameter("paymentReason", "ThinkUp monthly subscription");
        $pipeline->addParameter("validityStart", date("U", mktime(12, 0, 0, 1, 1, 2014)));
        $pipeline->addParameter("cobrandingUrl",
        UpstartHelper::getApplicationURL(false, false, false)."assets/img/thinkup-logo-transparent.png");
        $pipeline->addParameter("websiteDescription", "ThinkUp");

        return $pipeline->getUrl();
    }
}
