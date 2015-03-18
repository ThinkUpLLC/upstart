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

// Set up DAO
$subscriber_dao = new SubscriberMySQLDAO();

// Record paid subscriber count
$subscriber_dao->captureCurrentPaidCount();

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

