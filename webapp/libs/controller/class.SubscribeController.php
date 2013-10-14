<?php
/**
 * Create interface to Amazon Flexible Payment System
 */
class SubscribeController extends Controller {
    public function control() {
        $this->disableCaching(); //Don't cache because click ID/Amazon caller reference must be unique per user
        $this->setViewTemplate('subscribe.tpl');
        $click_dao = new ClickMySQLDAO();
        $caller_reference = $click_dao->insert();
        $this->addToView('caller_reference', $caller_reference);
        SessionCache::put('caller_reference', $caller_reference);
        $subscriber_count_dao = new SubscriberCountMySQLDAO();
        $subscriber_counts = $subscriber_count_dao->getAll();
        $this->addToView('subscriber_counts', $subscriber_counts);

        $selected_level = null;
        if (isset($_GET['level']) && ($_GET['level'] == "member" || $_GET['level'] == "pro"
        || $_GET['level'] == "executive")) {
            $selected_level = htmlspecialchars($_GET['level']);
        }
        foreach (SignUpController::$subscription_levels as $level=>$amount) {
            //Get Amazon URL
            $callback_url = UpstartHelper::getApplicationURL().'new.php?l='.$level;
            $subscribe_url = self::getAmazonFPSURL($caller_reference, $callback_url, $amount);
            $this->addToView('subscribe_'.$level.'_url', $subscribe_url);
            if ($level == $selected_level) {
                $this->addToView('selected_subscribe_url', $subscribe_url);
            }
        }
        $this->addToView('level', $selected_level);

        return $this->generateView();
    }

    private static function getAmazonFPSURL($caller_reference, $callback_url, $amount) {
        $cfg = Config::getInstance();
        $AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');
        $amazon_payment_auth_validity_start = $cfg->getValue('amazon_payment_auth_validity_start');

        $pipeline = new Amazon_FPS_CBUIRecurringTokenPipeline($AWS_ACCESS_KEY_ID, $AWS_SECRET_ACCESS_KEY);

        $pipeline->setMandatoryParameters($caller_reference, $callback_url, $amount, "12 Months");

        //optional parameters
        $pipeline->addParameter("paymentReason", "ThinkUp yearly membership");
        $pipeline->addParameter("validityStart", $amazon_payment_auth_validity_start);
        $pipeline->addParameter("cobrandingUrl",
        UpstartHelper::getApplicationURL(false, false, false)."assets/img/thinkup-logo-transparent.png");
        $pipeline->addParameter("websiteDescription", "ThinkUp");

        return $pipeline->getUrl();
    }
}
