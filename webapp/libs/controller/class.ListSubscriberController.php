<?php
class ListSubscriberController extends Controller {
    /**
     * Possible values for subscription_status
     * @var array
     */
    var $payment_statuses = array('Free trial', 'Paid', 'Payment failed', 'Refunded', 'Payment pending',
        'Complimentary membership');

    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('admin-index.tpl');

        $page = (isset($_GET['p']))?(integer)$_GET['p']:1;
        if ($page < 1) {
            $page =1;
        }
        $search_term = (isset($_GET['q']))?$_GET['q']:null;
        $subscriber_dao = new SubscriberMySQLDAO();
        if (in_array( $search_term, $this->payment_statuses)) {
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
        $this->addToView('application_url', UpstartHelper::getApplicationURL());

        /* Begin installation stats */
        $active_total = $subscriber_dao->getTotalActiveInstalls();
        $this->addToView('total_active_installs', $active_total);

        $stalest_dispatch_time_paid = $subscriber_dao->getPaidStalestInstallLastDispatchTime();
        $this->addToView('stalest_dispatch_time_paid', $stalest_dispatch_time_paid);
        $stalest_dispatch_time_not_paid = $subscriber_dao->getNotPaidStalestInstallLastDispatchTime();
        $this->addToView('stalest_dispatch_not_paid', $stalest_dispatch_time_not_paid);

        $daily_signups = $subscriber_dao->getDailySignups();
        $this->addToView('total_daily_signups', $daily_signups[date('Y-m-d')]['new_members']);
        $subscription_operation_dao = new SubscriptionOperationMySQLDAO();
        $daily_revenue = $subscription_operation_dao->getDailyRevenue();
        $this->addToView('total_daily_refunds', $daily_revenue[date('Y-m-d')]['refunds']);
        $this->addToView('total_daily_revenue', $daily_revenue[date('Y-m-d')]['revenue']);
        $daily_subscribers = $subscription_operation_dao->getDailySubscribers();
        $this->addToView('total_new_subscribers', $daily_subscribers[date('Y-m-d')]['subscribers']);
        $daily_successful_payments = $subscription_operation_dao->getDailySuccessfulPayments();
        $todays_reups = $daily_successful_payments[date('Y-m-d')]['successful_payments'] -
            $daily_subscribers[date('Y-m-d')]['subscribers'];
        $this->addToView('total_reups', $todays_reups);

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
