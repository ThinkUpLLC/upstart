<?php
class PledgeController extends Controller {
    public function control() {
        $this->setViewTemplate('index.tpl');
        $subscriber_count_dao = new SubscriberCountMySQLDAO();
        $subscriber_counts = $subscriber_count_dao->getAll();
        $this->addToView('subscriber_counts', $subscriber_counts);
        return $this->generateView();
    }
}