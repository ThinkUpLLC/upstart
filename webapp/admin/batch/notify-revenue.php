<?php
chdir('..');
chdir('..');
require_once 'init.php';

/**
 * Post signups and revenue numbers to ThinkUp's Slack #signups room.
 */

$url = 'https://thinkup.slack.com/services/hooks/incoming-webhook?token=mPEOeIpng7h2EIskwtNd9hNF';
$channel = "#signups";
//$channel = "#testbot";

// New signups
$subscriber_dao = new SubscriberMySQLDAO();
$daily_signups = $subscriber_dao->getDailySignups();
$message .= "";
if ($daily_signups[0]['new_members'] > $daily_signups[1]['new_members']) {
    $message .= "up from";
} elseif ($daily_signups[0]['new_members'] < $daily_signups[1]['new_members']) {
    $message .= "down from";
} else {
    $message .= "the same as";
}
$subject = number_format($daily_signups[0]['new_members']) . " new member". 
	(($daily_signups[0]['new_members'] == 1)?'':'s') ." joined ThinkUp today.";

$message = "That's ". $message. " ".number_format($daily_signups[1]['new_members']). 
	" signup". (($daily_signups[1]['new_members'] == 1)?'':'s') ." yesterday. Day before was ".
    number_format($daily_signups[2]['new_members']) . " signup" .(($daily_signups[1]['new_members'] == 1)?'':'s').
    ".";

$payload = '{"channel": "'.$channel.'", "username": "upstartbot", "text": "'. $subject.'\n'.
	$message. '", "icon_emoji": ":cubimal_chick:"}';

$fields = array('payload'=>$payload);

$result = postToURL($url, $fields);
$message = null;
$subject = null;


// Revenue
$payment_dao = new PaymentMySQLDAO();
$daily_revenue = $payment_dao->getDailyRevenue();
$message .= "";
if ($daily_revenue[0]['revenue'] > $daily_revenue[1]['revenue']) {
    $message .= "Up from";
} elseif ($daily_revenue[0]['revenue'] < $daily_revenue[1]['revenue']) {
    $message .= "Down from";
} else {
    $message .= "Same as";
}
$message .= " $". number_format($daily_revenue[1]['revenue']). " yesterday. Day before was $".
    number_format($daily_revenue[2]['revenue']) . ".";
$subject = "$" . number_format($daily_revenue[0]['revenue']) . " in revenue today";


$payload = '{"channel": "'.$channel.'", "username": "upstartbot", "text": "'. $subject.'\n'.
	$message. '", "icon_emoji": ":cubimal_chick:"}';

$fields = array('payload'=>$payload);

$result = postToURL($url, $fields);

/**
 * Post fields to a URL.
 * @param str $URL
 * @param array $fields
 * @return str contents
 */
function postToURL($URL, array $fields) {
    //open connection
    $ch = curl_init();

    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //execute post
    $contents = curl_exec($ch);

    //close connection
    curl_close($ch);
    if (isset($contents)) {
        return $contents;
    } else {
        return null;
    }
}
