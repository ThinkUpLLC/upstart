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
        $payment_dao = new PaymentMySQLDAO();
        $total_payments = $payment_dao->getTotalPayments();
        $this->addToView('total_payments', $total_payments);
        $this->addToView('application_url', UpstartHelper::getApplicationURL());

        /* Begin installation stats */
        $active_total = $subscriber_dao->getTotalActiveInstalls();
        $this->addToView('total_active_installs', $active_total);

        $stalest_dispatch_time_paid = $subscriber_dao->getPaidStalestInstallLastDispatchTime();
        $this->addToView('stalest_dispatch_time_paid', $stalest_dispatch_time_paid);
        $stalest_dispatch_time_not_paid = $subscriber_dao->getNotPaidStalestInstallLastDispatchTime();
        $this->addToView('stalest_dispatch_not_paid', $stalest_dispatch_time_not_paid);

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
