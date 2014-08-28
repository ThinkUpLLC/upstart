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
$message = "";
if ($daily_signups[$today]['new_members'] > $daily_signups[$yesterday]['new_members']) {
    $message .= "up from";
} elseif ($daily_signups[$today]['new_members'] < $daily_signups[$yesterday]['new_members']) {
    $message .= "down from";
} else {
    $message .= "the same as";
}
$subject = number_format($daily_signups[$today]['new_members']) . " new member".
	(($daily_signups[$today]['new_members'] == 1)?'':'s') ." joined ThinkUp today.";

$message = "That's ". $message. " ".number_format($daily_signups[$yesterday]['new_members']).
	" signup". (($daily_signups[$yesterday]['new_members'] == 1)?'':'s') ." yesterday. Day before was ".
    number_format($daily_signups[$day_before]['new_members']) . " signup" .
    (($daily_signups[$day_before]['new_members'] == 1)?'':'s').
    ".";

$y_axis_max = ((floor(max($daily_signups[$today]['new_members'], $daily_signups[$yesterday]['new_members'],
    $daily_signups[$day_before]['new_members']) / 100 )) + 1) * 100;

$chart_url = 'https://chart.googleapis.com/chart?cht=lc&chs=500x250&chd=t:'.
    $daily_signups[$day_before]['new_members'].
    ','.$daily_signups[$yesterday]['new_members'].
    ','.$daily_signups[$today]['new_members'].
    '&chxt=x,y&chxl=0:|Day+Before|Yesterday|Today|1:|';

//|50|100|150|200|250|300|350|400|450|500
$total_y_axis_markers = $y_axis_max / 50;
$i = 0;
while ($i < $y_axis_max ) {
    $i = $i+50;
    $chart_url .= '|'.$i;
}

$chart_url .= '&chds=0,'.$y_axis_max;
$text = $subject.'\n'. $message. '\n'.$chart_url;

$result = UpstartHelper::postToSlack($channel, $text);
$message = null;
$subject = null;


// Revenue
$subscription_operation_dao = new SubscriptionOperationMySQLDAO();
$daily_revenue = $subscription_operation_dao->getDailyRevenue();
$message .= "";
if ($daily_revenue[$today]['revenue'] > $daily_revenue[$yesterday]['revenue']) {
    $message .= "Up from";
} elseif ($daily_revenue[$today]['revenue'] < $daily_revenue[$yesterday]['revenue']) {
    $message .= "Down from";
} else {
    $message .= "Same as";
}
$message .= " $". number_format($daily_revenue[$yesterday]['revenue']). " yesterday. Day before was $".
    number_format($daily_revenue[$day_before]['revenue']) . ".";
$subject = "$" . number_format($daily_revenue[$today]['revenue']) . " in revenue today";

$text = $subject.'\n'. $message;

$result = UpstartHelper::postToSlack($channel, $text);
