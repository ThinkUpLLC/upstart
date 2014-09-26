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

// New signups
$subscriber_dao = new SubscriberMySQLDAO();
$daily_signups = $subscriber_dao->getDailySignups();
// New subscribers
$subscription_operation_dao = new SubscriptionOperationMySQLDAO();
$daily_subscribers = $subscription_operation_dao->getDailySubscribers();

$text = "";
$message = "";

if ($daily_subscribers[$today]['subcribers'] > $daily_subscribers[$yesterday]['subscribers']) {
    $comparator = "up from";
} elseif ($daily_subscribers[$today]['subscribers'] < $daily_subscribers[$yesterday]['subscribers']) {
    $comparator = "down from";
} else {
    $comparator = "the same as";
}
$subject = number_format($daily_subscribers[$today]['subscribers']) . " member".
    (($daily_subscribers[$today]['subscribers'] == 1)?'':'s') ." subscribed to ThinkUp today.";

$message = "That's ". $comparator. " ".number_format($daily_subscribers[$yesterday]['subscribers']).
    " subscription". (($daily_subscribers[$yesterday]['subscribers'] == 1)?'':'s') ." yesterday. Day before was ".
    number_format($daily_subscribers[$day_before]['subscribers']) . " subscription" .
    (($daily_subscribers[$day_before]['subscribers'] == 1)?'':'s').
    ".";

$text .= $subject.' '. $message. '\n'.$chart_url;

if ($daily_signups[$today]['new_members'] > $daily_signups[$yesterday]['new_members']) {
    $comparator = "up from";
} elseif ($daily_signups[$today]['new_members'] < $daily_signups[$yesterday]['new_members']) {
    $comparator = "down from";
} else {
    $comparator = "the same as";
}
$subject = number_format($daily_signups[$today]['new_members']) . " new member".
	(($daily_signups[$today]['new_members'] == 1)?'':'s') ." joined ThinkUp today.";

$message = "That's ". $comparator. " ".number_format($daily_signups[$yesterday]['new_members']).
	" signup". (($daily_signups[$yesterday]['new_members'] == 1)?'':'s') ." yesterday. Day before was ".
    number_format($daily_signups[$day_before]['new_members']) . " signup" .
    (($daily_signups[$day_before]['new_members'] == 1)?'':'s').
    ".";

$y_axis_max = ((floor(max($daily_signups[$today]['new_members'], $daily_signups[$yesterday]['new_members'],
    $daily_signups[$day_before]['new_members']) / 100 )) + 1) * 100;

$chart_url = 'https://chart.googleapis.com/chart?cht=lc&chs=500x250&chd=t:'.
    $daily_signups[$day_before]['new_members'].
    ','.$daily_signups[$yesterday]['new_members'].
    ','.$daily_signups[$today]['new_members'].'|'.
    $daily_subscribers[$day_before]['subscribers'].
    ','.$daily_subscribers[$yesterday]['subscribers'].
    ','.$daily_subscribers[$today]['subscribers'].
    '&chxt=x,y&chxl=0:|Day+Before|Yesterday|Today|1:|';

//|50|100|150|200|250|300|350|400|450|500
$total_y_axis_markers = $y_axis_max / 50;
$i = 0;
while ($i < $y_axis_max ) {
    $i = $i+50;
    $chart_url .= '|'.$i;
}

$chart_url .= '&chds=0,'.$y_axis_max;
$chart_url .= "&chco=0000FF,00FF00&chg=50,10";
$text .= $subject.' '. $message. '\n'.$chart_url;

$result = UpstartHelper::postToSlack($channel, $text);
$message = '';
$subject = '';

// Revenue
$daily_revenue = $subscription_operation_dao->getDailyRevenue();
if ($daily_revenue[$today]['revenue'] > $daily_revenue[$yesterday]['revenue']) {
    $comparator = "Up from";
} elseif ($daily_revenue[$today]['revenue'] < $daily_revenue[$yesterday]['revenue']) {
    $comparator = "Down from";
} else {
    $comparator = "Same as";
}
$message = $comparator . " $". number_format($daily_revenue[$yesterday]['revenue']). " yesterday. Day before was $".
    number_format($daily_revenue[$day_before]['revenue']) . ".";
$subject = "$" . number_format($daily_revenue[$today]['revenue']) . " in revenue today.";

$text = $subject.' '. $message;

$result = UpstartHelper::postToSlack($channel, $text);

//Post subscribers per week on Sundays
$subs_per_week = $subscriber_dao->getSubscriptionsByWeek();

foreach ($subs_per_week as $sub) {
    $message = 'Week of '.$sub['date'].": ".$sub["total_subs"]." subscriptions";
    $result = UpstartHelper::postToSlack($channel, $message);
}

