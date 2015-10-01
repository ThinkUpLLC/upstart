<?php
class ListSubscriberController extends Controller {
    /**
     * Possible values for subscription_status
     * @var array
     */
    var $payment_statuses = array('Free trial', 'Paid', 'Payment failed', 'Refunded', 'Payment pending',
        'Complimentary membership', 'Payment due');

    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('admin-index.tpl');

        $page = (isset($_GET['p']))?(integer)$_GET['p']:1;
        if ($page < 1) {
            $page =1;
        }
        $search_term = (isset($_GET['q']))?$_GET['q']:null;
        $subscriber_dao = new SubscriberMySQLDAO();
        if (isset($search_term)) {
            $this->addToView('search_term', $search_term);
            if (in_array( $search_term, $this->payment_statuses)) {
                $subscribers = $subscriber_dao->getSubscriberListWithPaymentStatus($search_term, $page, 51);
            } else {
                $subscribers = $subscriber_dao->getSearchResults($search_term, $page, 51);
            }
        } else {
                $subscribers = $subscriber_dao->getSubscriberList($page, 51);
        }
        foreach ($subscribers as $subscriber) {
            $subscriber->paid_through_friendly = date('M j, Y', strtotime($subscriber->paid_through));
        }
        $this->addToView('subscribers', $subscribers);
        $total_paid_subscribers = $subscriber_dao->getPaidTotal();
        $this->addToView('total_paid_subscribers', $total_paid_subscribers['total_paid_subscribers']);
        $this->addToView('total_paid_subscribers_monthly', $total_paid_subscribers['breakdown']['monthly']);
        $this->addToView('total_paid_subscribers_annual', $total_paid_subscribers['breakdown']['annual']);
        $this->addToView('total_paid_subscribers_coupon_codes', $total_paid_subscribers['breakdown']['coupon_codes']);
        $this->addToView('total_paid_subscribers_via_recurly', $total_paid_subscribers['breakdown']['recurly']);
        $this->addToView('application_url', UpstartHelper::getApplicationURL());

        /* Begin installation stats */
        $active_total = $subscriber_dao->getTotalActiveInstalls();
        $this->addToView('total_active_installs', $active_total);

        $stalest_crawl_time_paid = $subscriber_dao->getPaidStalestInstallLastCrawlCompletedTime();
        $this->addToView('stalest_crawl_time_paid', $stalest_crawl_time_paid);
        $stalest_crawl_time_not_paid = $subscriber_dao->getNotPaidStalestInstallLastCrawlCompletedTime();
        $this->addToView('stalest_crawl_not_paid', $stalest_crawl_time_not_paid);

        $daily_signups = $subscriber_dao->getDailySignups(1);
        $total_daily_signups = (isset($daily_signups[date('Y-m-d')]))?$daily_signups[date('Y-m-d')]:0;
        $this->addToView('total_daily_signups', $total_daily_signups);

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
