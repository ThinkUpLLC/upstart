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

// Set up DAOs
$subscriber_dao = new SubscriberMySQLDAO();
$subscription_operation_dao = new SubscriptionOperationMySQLDAO();

// Record paid subscriber count
$subscriber_dao->captureCurrentPaidCount();

// Begin payments notifications
// Get total successful payments (re-ups + new subscriptions)
$daily_successful_payments = $subscription_operation_dao->getDailySuccessfulPayments();
// Get new subscriptions
$daily_subscribers = $subscription_operation_dao->getDailySubscribers();

//debug
// echo 'Daily successful payments  ';
// print_r($daily_successful_payments);
// echo 'Daily subscribers  ';
// print_r($daily_subscribers);

if ($daily_subscribers[$today] > $daily_subscribers[$yesterday]) {
    $comparator = "up from";
} elseif ($daily_subscribers[$today] < $daily_subscribers[$yesterday]) {
    $comparator = "down from";
} else {
    $comparator = "the same as";
}
$message = number_format($daily_subscribers[$today]) . " member".
    (($daily_subscribers[$today] == 1)?'':'s') ." subscribed to ThinkUp today";

$todays_reups = $daily_successful_payments[$today] - $daily_subscribers[$today];
$message .= " and ".number_format($todays_reups)." re-upped. ";

$message .= "That's ". $comparator. " ".number_format($daily_subscribers[$yesterday]).
    " subscription". (($daily_subscribers[$yesterday] == 1)?'':'s') ." yesterday. Day before was ".
    number_format($daily_subscribers[$day_before]) . " subscription" .
    (($daily_subscribers[$day_before] == 1)?'':'s').
    ".";

$chart_url = UpstartHelper::buildChartImageURL($daily_successful_payments, $daily_subscribers, 5, 'Payments|Conversions');
$text = $message. '\n'.$chart_url.'\n';
$result = UpstartHelper::postToSlack($channel, $text);
// End payments notifications

// Begin signups notifications
$daily_signups = $subscriber_dao->getDailySignups();

if ($daily_signups[$today] > $daily_signups[$yesterday]) {
    $comparator = "up from";
} elseif ($daily_signups[$today] < $daily_signups[$yesterday]) {
    $comparator = "down from";
} else {
    $comparator = "the same as";
}
$message = number_format($daily_signups[$today]) . " new member".
	(($daily_signups[$today] == 1)?'':'s') ." joined ThinkUp today. ";

$message .= "That's ". $comparator. " ".number_format($daily_signups[$yesterday]).
	" signup". (($daily_signups[$yesterday] == 1)?'':'s') ." yesterday. Day before was ".
    number_format($daily_signups[$day_before]) . " signup" .
    (($daily_signups[$day_before] == 1)?'':'s').
    ".";

$chart_url = UpstartHelper::buildChartImageURL($daily_signups, null, 50, 'Signups');
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

// Begin Saturday night subscribers per week
if (date( "w") == 6) {
    $subs_per_week = $subscriber_dao->getSubscriptionsByWeek();

    $weekly_subs = array();
    $total_subs = 0;
    foreach ($subs_per_week as $sub) {
        $weekly_subs[$sub['date']] = $sub['total_subs'];
        $total_subs += $sub['total_subs'];
        //Probably a better way to get this week's subscriptions than assigning it every loop, but this works
        $this_weeks_subs = $sub['total_subs'];
    }
    $average_weekly_subs = round(($total_subs/count($weekly_subs)));
    //Construct takeaway message, for example,
    //25 conversions this week, (more than/less than/exactly equal to) the 6-week average of 23.
    if ($this_weeks_subs > $average_weekly_subs) {
        $comparator = "more than";
    } elseif ($this_weeks_subs < $average_weekly_subs) {
        $comparator = "less than";
    } else {
        $comparator = "exactly equal to";
    }
    $message = $this_weeks_subs." conversions this week, ".$comparator." the ".count($weekly_subs).
        "-week average of ".$average_weekly_subs.".";

    $chart_url = UpstartHelper::buildChartImageURL($weekly_subs, null, 5, 'Conversions');
    $result = UpstartHelper::postToSlack($channel, $message);
    $result = UpstartHelper::postToSlack($channel, $chart_url);
}
// End Saturday night subscribers by week
