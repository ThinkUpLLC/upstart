<?php
class PledgeController extends Controller {
    public function control() {
        $this->setViewTemplate('index.tpl');

        $subscriber_dao = new SubscriberMySQLDAO();
        $total_subscribers = $subscriber_dao->getTotalSubscribers($amount = 0);
        $this->addToView('total_subscribers', $total_subscribers);
        foreach (SignUpController::$subscription_levels as $level=>$amount) {
            $total_backers = $subscriber_dao->getTotalSubscribers($amount);
            $this->addToView('total_'.$level.'_subscribers', $total_backers);
        }
        return $this->generateView();
    }
}