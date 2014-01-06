<?php
class ListSubscriberController extends Controller {
    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('admin-index.tpl');

        $page = (isset($_GET['p']))?(integer)$_GET['p']:1;
        if ($page < 1) {
            $page =1;
        }
        $search_term = (isset($_GET['q']))?$_GET['q']:null;
        $subscriber_dao = new SubscriberMySQLDAO();
        if ($search_term != null ) {
            $subscribers = $subscriber_dao->getSearchResults($search_term, $page, 51);
        } else {
            $subscribers = $subscriber_dao->getSubscriberList($page, 51);
        }
        $this->addToView('search_term', $search_term);
        $this->addToView('subscribers', $subscribers);
        $total_subscribers = $subscriber_dao->getListTotal();
        $this->addToView('total_subscribers', $total_subscribers);
        $authorization_dao = new AuthorizationMySQLDAO();
        $total_authorizations = $authorization_dao->getTotalAuthorizations();
        $this->addToView('total_authorizations', $total_authorizations);
        $this->addToView('application_url', UpstartHelper::getApplicationURL());

        /* Begin installation stats */
        $active_total = $subscriber_dao->getTotalActiveInstalls();
        $this->addToView('total_active_installs', $active_total);

        $stalest_dispatch_time_10k_up = $subscriber_dao->getStalestInstall10kAndUpLastDispatchTime();
        $this->addToView('stalest_dispatch_time_10k_up', $stalest_dispatch_time_10k_up);
        $stalest_dispatch_time_1k_to_10k = $subscriber_dao->getStalestInstall1kTo10kLastDispatchTime();
        $this->addToView('stalest_dispatch_time_1k_to_10k', $stalest_dispatch_time_1k_to_10k);
        $stalest_dispatch_time = $subscriber_dao->getStalestInstallLastDispatchTime();
        $this->addToView('stalest_dispatch_time', $stalest_dispatch_time);

        try {
            $worker_status = Dispatcher::getNagiosCheckStatus();
            if (strrpos($worker_status, 'NOT OK') !== false) {
                $this->addToView('workers_ok', false);
            } else {
                $this->addToView('workers_ok', true);
            }
            $this->addToView('worker_status', $worker_status);
        } catch (JSONDecoderException $e) {
            $this->addToView('workers_ok', false);
            $this->addToView('worker_status', 'Nagios Status Check Failed');
        }
        /* End installation stats */

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
