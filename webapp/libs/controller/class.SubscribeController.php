<?php
/**
 * Create interface to Amazon Flexible Payment System
 */
class SubscribeController extends Controller {
    public function control() {
        $this->setViewTemplate('subscribe-index.tpl');
        $click_dao = new ClickMySQLDAO();
        $caller_reference = $click_dao->insert();
        $this->addToView('caller_reference', $caller_reference);
        SessionCache::put('caller_reference', $caller_reference);

        foreach (SignUpController::$subscription_levels as $level=>$amount) {
            $callback_url = UpstartHelper::getApplicationURL().'newsubscriber.php?l='.$level;
            $subscribe_url = self::getAmazonFPSURL($caller_reference, $callback_url, $amount);
            $this->addToView('subscribe_'.$level.'_url', $subscribe_url);
        }

        //DEBUG
        $app_url = UpstartHelper::getApplicationURL();
        $this->addToView('app_url', $app_url);
        $image_url = UpstartHelper::getApplicationURL(false, false, false)."assets/img/thinkup-logo-transparent.png";
        $this->addToView('image_url', $image_url);

        return $this->generateView();
    }

    private static function getAmazonFPSURL($caller_reference, $callback_url, $amount) {
        $cfg = Config::getInstance();
        $AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');

        $pipeline = new Amazon_FPS_CBUIRecurringTokenPipeline($AWS_ACCESS_KEY_ID, $AWS_SECRET_ACCESS_KEY);

        $pipeline->setMandatoryParameters($caller_reference, $callback_url, $amount, "12 Months");

        //optional parameters
        //        date_default_timezone_set("America/New_York");
        //$pipeline->addParameter("paymentMethod", "CC"); //accept only credit card payments
        $pipeline->addParameter("paymentReason", "ThinkUp monthly subscription");
        $pipeline->addParameter("validityStart", date("U", mktime(12, 0, 0, 1, 1, 2014)));
        $pipeline->addParameter("cobrandingUrl",
        UpstartHelper::getApplicationURL(false, false, false)."assets/img/thinkup-logo-transparent.png");
        $pipeline->addParameter("websiteDescription", "ThinkUp");

        return $pipeline->getUrl();
    }
}
