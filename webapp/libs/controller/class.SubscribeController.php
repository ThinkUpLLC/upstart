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

        $selected_level = null;
        if (isset($_GET['level']) && ($_GET['level'] == "member" || $_GET['level'] == "pro"
        || $_GET['level'] == "executive" || $_GET['level'] == "earlybird")) {
            $selected_level = htmlspecialchars($_GET['level']);
        }
        foreach (SignUpController::$subscription_levels as $level=>$amount) {
            //Get Amazon URL
            $callback_url = UpstartHelper::getApplicationURL().'new.php?l='.$level;
            $subscribe_url = AmazonFPSAPIAccessor::getAmazonFPSURL($caller_reference, $callback_url, $amount);
            $this->addToView('subscribe_'.$level.'_url', $subscribe_url);
            if ($level == $selected_level) {
                $this->addToView('selected_subscribe_url', $subscribe_url);
            }
        }
        $this->addToView('level', $selected_level);

        return $this->generateView();
    }
}
