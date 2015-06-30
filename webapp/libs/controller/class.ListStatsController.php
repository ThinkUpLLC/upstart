<?php
class ListStatsController extends Controller {
    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('admin-stats.tpl');
        // Set up DAOs
        $subscriber_dao = new SubscriberMySQLDAO();

        // Get daily signups
        $daily_signups = $subscriber_dao->getDailySignups(90);
        // Get weekly signups
        $weekly_signups = $subscriber_dao->getWeeklySignups();
        // Get daily paid subscribers
        $daily_paid_subscribers = $subscriber_dao->getDailyPaidSubscriberCounts(500);
        // Get paid subscribers on Recurly
        $daily_paid_recurly_subscribers = $subscriber_dao->getDailyPaidRecurlySubscriberCounts(500);

        // Build charts and add to view
        $chart_url = UpstartHelper::buildChartImageURL($daily_signups, null, 100, 'Signups');
        $this->addToView('daily_signups_chart_url', $chart_url);

        $chart_url = UpstartHelper::buildChartImageURL($weekly_signups, null, 200, 'Signups');
        $this->addToView('weekly_signups_chart_url', $chart_url);

        $chart_url = UpstartHelper::buildChartImageURL($daily_paid_subscribers, $daily_paid_recurly_subscribers,
            300, 'Paid subscribers');
        $this->addToView('daily_paid_subscribers_chart_url', $chart_url);

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