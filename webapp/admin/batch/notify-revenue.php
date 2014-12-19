<?php
chdir('..');
chdir('..');
require_once 'init.php';

/**
 * Post signups and revenue numbers to ThinkUp's Slack #signups room.
 */
$channel = "#signups";
//debug
//$channel = "#testbot";

$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime("-1 days"));
$day_before = date('Y-m-d', strtotime("-2 days"));

// Signups
$subscriber_dao = new SubscriberMySQLDAO();
$daily_signups = $subscriber_dao->getDailySignups();
// Payments
$subscription_operation_dao = new SubscriptionOperationMySQLDAO();
// Total successful payments (re-ups + new subscriptions)
$daily_successful_payments = $subscription_operation_dao->getDailySuccessfulPayments();
// New subscriptions
$daily_subscribers = $subscription_operation_dao->getDailySubscribers();

// Begin payments notifications
if ($daily_subscribers[$today]['subscribers'] > $daily_subscribers[$yesterday]['subscribers']) {
    $comparator = "up from";
} elseif ($daily_subscribers[$today]['subscribers'] < $daily_subscribers[$yesterday]['subscribers']) {
    $comparator = "down from";
} else {
    $comparator = "the same as";
}
$message = number_format($daily_subscribers[$today]['subscribers']) . " member".
    (($daily_subscribers[$today]['subscribers'] == 1)?'':'s') ." subscribed to ThinkUp today";

$todays_reups = $daily_successful_payments[$today]['successful_payments'] - $daily_subscribers[$today]['subscribers'];
$message .= " and ".number_format($todays_reups)." re-upped. ";

$message .= "That's ". $comparator. " ".number_format($daily_subscribers[$yesterday]['subscribers']).
    " subscription". (($daily_subscribers[$yesterday]['subscribers'] == 1)?'':'s') ." yesterday. Day before was ".
    number_format($daily_subscribers[$day_before]['subscribers']) . " subscription" .
    (($daily_subscribers[$day_before]['subscribers'] == 1)?'':'s').
    ".";

$chart_url = ChartHelper::buildChartImageURL($daily_successful_payments, $daily_subscribers, 5, 'Payments|Conversions');
$text = $message. '\n'.$chart_url.'\n';

$result = UpstartHelper::postToSlack($channel, $text);
// End payments notifications

// Begin signups notifications
if ($daily_signups[$today]['new_members'] > $daily_signups[$yesterday]['new_members']) {
    $comparator = "up from";
} elseif ($daily_signups[$today]['new_members'] < $daily_signups[$yesterday]['new_members']) {
    $comparator = "down from";
} else {
    $comparator = "the same as";
}
$message = number_format($daily_signups[$today]['new_members']) . " new member".
	(($daily_signups[$today]['new_members'] == 1)?'':'s') ." joined ThinkUp today. ";

$message .= "That's ". $comparator. " ".number_format($daily_signups[$yesterday]['new_members']).
	" signup". (($daily_signups[$yesterday]['new_members'] == 1)?'':'s') ." yesterday. Day before was ".
    number_format($daily_signups[$day_before]['new_members']) . " signup" .
    (($daily_signups[$day_before]['new_members'] == 1)?'':'s').
    ".";

$chart_url = ChartHelper::buildChartImageURL($daily_signups, null, 50, 'Signups');

$text = $message. '\n'.$chart_url;
$result = UpstartHelper::postToSlack($channel, $text);
// End signups notifications

// Begin revenue notifications
$daily_revenue = $subscription_operation_dao->getDailyRevenue();
if ($daily_revenue[$today]['revenue'] > $daily_revenue[$yesterday]['revenue']) {
    $comparator = "Up from";
} elseif ($daily_revenue[$today]['revenue'] < $daily_revenue[$yesterday]['revenue']) {
    $comparator = "Down from";
} else {
    $comparator = "Same as";
}
$message = "$" . number_format($daily_revenue[$today]['revenue']) . " in revenue today. ";
$message .= $comparator . " $". number_format($daily_revenue[$yesterday]['revenue']). " yesterday. Day before was $".
    number_format($daily_revenue[$day_before]['revenue']) . ".";

$result = UpstartHelper::postToSlack($channel, $message);
// End revenue notifications

// On Saturday night, post subscribers per week
if (date( "w") == 6) {
    $subs_per_week = $subscriber_dao->getSubscriptionsByWeek();

    $weekly_subs = array();
    foreach ($subs_per_week as $sub) {
        $message = 'Week of '.$sub['date'].": ".$sub["total_subs"]." subscriptions";
        $weekly_subs[$sub['date']] = $sub['total_subs'];
        $result = UpstartHelper::postToSlack($channel, $message);
    }
    $chart_url = ChartHelper::buildChartImageURL($weekly_subs, null, 5, 'Conversions');
    $result = UpstartHelper::postToSlack($channel, $chart_url);
}
// End Saturday night subscribers by week

/**
 * ChartHelper class
 */
class ChartHelper {
    public static function buildChartImageURL($first_data_set, $second_data_set = null, $y_axis_divisor = 5,
        $chart_key = null) {
        $chart_url = 'https://chart.googleapis.com/chart?cht=lc&chs=1000x300&chd=t:';
        // First data set
        end($first_data_set);
        $last_key = key($first_data_set);
        foreach ($first_data_set as $date=>$successful_payments) {
            $chart_url .= $successful_payments;
            if ($date !== $last_key) {
                $chart_url .= ',';
            }
        }
        if (isset($second_data_set)) {
            // Second data set
            $chart_url .= '|';
            end($second_data_set);
            $last_key = key($second_data_set);
            foreach ($second_data_set as $date=>$total) {
                $chart_url .= $total;
                if ($date !== $last_key) {
                    $chart_url .= ',';
                }
            }
        }
        // X-axis
        $chart_url .= '&chxt=x,y&chxl=0:|';
        foreach ($first_data_set as $date=>$total) {
            $chart_url .= $date;
            $chart_url .= '|';
        }
        $chart_url .='1:|';
        // Y-axis markers
        asort($first_data_set, SORT_NUMERIC);
        $max_count = array_pop($first_data_set);
        $y_axis_max = ((floor($max_count / 2 )) + 1) * 2;
        $total_y_axis_markers = $y_axis_max / 2;
        $i = 0;
        while ($i < $y_axis_max ) {
            $i = $i+$y_axis_divisor;
            $chart_url .= '|'.$i;
        }
        $chart_url .= '&chds=0,'.$y_axis_max;
        if (isset($second_data_set)) {
            $chart_url .= "&chco=0000FF,00FF00";
        } else {
            $chart_url .= "&chco=336699";
        }
        $chart_url .= "&chg=50,10";
        if (isset($chart_key)) {
            $chart_url .= "&chdl=".$chart_key;
        }
        return $chart_url;
    }
}