<?php
class ListStatsController extends Controller {
    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('admin-stats.tpl');
        // Set up DAOs
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscription_operation_dao = new SubscriptionOperationMySQLDAO();
        // Get total successful payments (re-ups + new subscriptions)
        $daily_successful_payments = $subscription_operation_dao->getDailySuccessfulPayments();
        // Get daily conversions
        $daily_subscribers = $subscription_operation_dao->getDailySubscribers();
        // Get daily signups
        $daily_signups = $subscriber_dao->getDailySignups();

        // Build charts and add to view
        $chart_url = UpstartHelper::buildChartImageURL($daily_successful_payments, $daily_subscribers, 5,
            'Payments|Conversions');
        $this->addToView('daily_payments_chart_url', $chart_url);

        $chart_url = UpstartHelper::buildChartImageURL($daily_signups, null, 50, 'Signups');
        $this->addToView('daily_signups_chart_url', $chart_url);

        // Subs per week
        $subs_per_week = $subscriber_dao->getSubscriptionsByWeek();
        // Massage array
        $weekly_subs = array();
        $total_subs = 0;
        foreach ($subs_per_week as $sub) {
            $weekly_subs[$sub['date']] = $sub['total_subs'];
            $total_subs += $sub['total_subs'];
            //Probably a better way to get this week's subscriptions than assigning it every loop, but this works
            $this_weeks_subs = $sub['total_subs'];
        }
        $average_weekly_subs = round(($total_subs/count($weekly_subs)));
        // Construct takeaway message, for example,
        // 25 conversions this week, (more than/less than/exactly equal to) the 6-week average of 23.
        if ($this_weeks_subs > $average_weekly_subs) {
            $comparator = "more than";
        } elseif ($this_weeks_subs < $average_weekly_subs) {
            $comparator = "less than";
        } else {
            $comparator = "exactly equal to";
        }
        $message = $this_weeks_subs." conversions so far this week, ".$comparator." the ".count($weekly_subs).
            "-week average of ".$average_weekly_subs.".";

        $chart_url = UpstartHelper::buildChartImageURL($weekly_subs, null, 5, 'Conversions');
        $this->addToView('weekly_conversions_chart_url', $chart_url);
        $this->addToView('weekly_conversions_message', $message);

        // Subs per month
        $subs_per_month = $subscriber_dao->getSubscriptionsByMonth();
        // Massage array
        $monthly_subs = array();
        $total_subs = 0;
        foreach ($subs_per_month as $sub) {
            $monthly_subs[$sub['date']] = $sub['total_subs'];
            $total_subs += $sub['total_subs'];
            // Probably a better way to get this month's subscriptions than assigning it every loop, but this works
            $this_months_subs = $sub['total_subs'];
        }
        $average_monthly_subs = round(($total_subs/count($monthly_subs)));
        // Construct takeaway message, for example,
        // 25 conversions this week, (more than/less than/exactly equal to) the 6-week average of 23.
        if ($this_months_subs > $average_monthly_subs) {
            $comparator = "more than";
        } elseif ($this_weeks_subs < $average_monthly_subs) {
            $comparator = "less than";
        } else {
            $comparator = "exactly equal to";
        }
        $message = $this_months_subs." conversions so far this month, ".$comparator." the ".count($monthly_subs).
            "-month average of ".$average_monthly_subs.".";

        $chart_url = UpstartHelper::buildChartImageURL($monthly_subs, null, 50, 'Conversions');
        $this->addToView('monthly_conversions_chart_url', $chart_url);
        $this->addToView('monthly_conversions_message', $message);

        return $this->generateView();
    }
}