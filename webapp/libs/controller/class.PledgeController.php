<?php
class PledgeController extends Controller {
    public function control() {
        $this->setViewTemplate('index.tpl');
        if ($this->shouldRefreshCache() ) {
            $subscriber_count_dao = new SubscriberCountMySQLDAO();
            $subscriber_counts = $subscriber_count_dao->getAll();
            $this->addToView('subscriber_counts', $subscriber_counts);

            $today = date('z');
            $deadline = date('z', mktime(12, 0, 0, 11, 16, 2013));
            $days_to_go = $deadline - $today;
            $this->addToView('days_to_go', $days_to_go);
        }
        return $this->generateView();
    }
}