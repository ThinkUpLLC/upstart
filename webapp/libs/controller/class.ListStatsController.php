<?php
class ListStatsController extends Controller {
    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('admin-stats.tpl');
        // Set up DAOs
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscription_operation_dao = new SubscriptionOperationMySQLDAO();
        // Get total successful payments (re-ups + new subscriptions)
        $daily_successful_payments = $subscription_operation_dao->getDailySuccessfulPayments(365);
        // Get daily conversions
        $daily_subscribers = $subscription_operation_dao->getDailySubscribers();
        // Get daily signups
        $daily_signups = $subscriber_dao->getDailySignups();
        // Get daily paid subscribers
        $daily_paid_subscribers = $subscriber_dao->getDailyPaidSubscriberCounts();

        // Build charts and add to view
        $chart_url = UpstartHelper::buildChartImageURL($daily_successful_payments, $daily_subscribers, 5,
            'Payments|Conversions');
        $this->addToView('daily_payments_chart_url', $chart_url);

        $chart_url = UpstartHelper::buildChartImageURL($daily_signups, null, 50, 'Signups');
        $this->addToView('daily_signups_chart_url', $chart_url);

        $chart_url = UpstartHelper::buildChartImageURL($daily_paid_subscribers, null, 1000, 'Paid subscribers');
        $this->addToView('daily_paid_subscribers_chart_url', $chart_url);

        // Subs per week
        $subs_per_week = $subscriber_dao->getSubscriptionsByWeek(28);
        $sub_takeaways = $this->getTakeaways($subs_per_week, 'date', 'total_subs', 'conversions');
        // Refunds per week
        $weekly_refunds = $subscription_operation_dao->getWeeklyRefunds();
        $refund_takeaways = $this->getTakeaways($weekly_refunds, 'date', 'total_refunds', 'refunds');

        $chart_url = UpstartHelper::buildChartImageURL($sub_takeaways['weekly_data'], $refund_takeaways['weekly_data'], 10,
            'Conversions|Refunds');
        $this->addToView('weekly_conversions_chart_url', $chart_url);
        $this->addToView('weekly_conversions_message', $sub_takeaways['takeaway_message']);
        $this->addToView('weekly_refunds_message', $refund_takeaways['takeaway_message']);

        // Subs per month
        $subs_per_month = $subscriber_dao->getSubscriptionsByMonth();
        $monthly_sub_takeaways = $this->getTakeaways($subs_per_month, 'date', 'total_subs', 'conversions', 'month');
        $chart_url = UpstartHelper::buildChartImageURL($monthly_sub_takeaways['weekly_data'], null, 50, 'Conversions');
        $this->addToView('monthly_conversions_chart_url', $chart_url);
        $this->addToView('monthly_conversions_message', $monthly_sub_takeaways['takeaway_message']);

        return $this->generateView();
    }

    private function getTakeaways($date_total_items, $date_label, $total_label, $data_label, $time_period = 'week') {
        // Massage array
        $weekly_data_items = array();
        $total_items = 0;
        foreach ($date_total_items as $item) {
            $weekly_data_items[$item[$date_label]] = $item[$total_label];
            $total_items += $item[$total_label];
            //Probably a better way to get this week's subscriptions than assigning it every loop, but this works
            $this_weeks_data = $item[$total_label];
        }
        $average_weekly_data_items = round(($total_items/count($weekly_data_items)));
        // Construct takeaway message, for example,
        // 25 conversions this week, (more than/less than/exactly equal to) the 6-week average of 23.
        if ($this_weeks_data > $average_weekly_data_items) {
            $comparator = "more than";
        } elseif ($this_weeks_data < $average_weekly_data_items) {
            $comparator = "less than";
        } else {
            $comparator = "exactly equal to";
        }
        $takeaway_message = $this_weeks_data." ".$data_label." so far this ".$time_period.", ".$comparator." the "
            .count($weekly_data_items). "-".$time_period." average of ".$average_weekly_data_items.".";
        return array('takeaway_message'=>$takeaway_message, 'weekly_data'=>$weekly_data_items);
    }
}