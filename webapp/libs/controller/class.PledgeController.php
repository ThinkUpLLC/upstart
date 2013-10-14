<?php
class PledgeController extends Controller {
    public function control() {
        $this->setViewTemplate('index.tpl');
        if ($this->shouldRefreshCache() ) {
            $subscriber_count_dao = new SubscriberCountMySQLDAO();
            $subscriber_counts = $subscriber_count_dao->getAll();
            $this->addToView('subscriber_counts', $subscriber_counts);
        }
        return $this->generateView();
    }
}