<?php
class ListSubscriberController extends Controller {
    public function control() {
        $this->setViewTemplate('admin-subscribers.tpl');

        $page = (isset($_GET['p']))?(integer)$_GET['p']:1;
        if ($page < 1) {
            $page =1;
        }
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscribers = $subscriber_dao->getSubscriberList($page, 51);
        $this->addToView('subscribers', $subscribers);
        $total_subscribers = $subscriber_dao->getListTotal();
        $this->addToView('total_subscribers', $total_subscribers);

        $authorization_dao = new AuthorizationMySQLDAO();
        $total_authorizations = $authorization_dao->getTotalAuthorizations();
        $this->addToView('total_authorizations', $total_authorizations);

        $this->addToView('page', $page);
        if (sizeof($subscribers) == 51) {
            array_pop($subscribers);
            $this->addToView('next_page', $page+1);
        }
        if ($page > 1) {
            $this->addToView('prev_page', $page-1);
        }
        return $this->generateView();
    }
}