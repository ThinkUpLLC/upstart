<?php
chdir('..');
chdir('..');
require_once 'init.php';

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
    number_format($daily_revenue[2]['revenue']) . ".
";
$subject = "$" . number_format($daily_revenue[0]['revenue']) . " in revenue today";

Mailer::mailViaPHP( 'scppHwfCNV3jC4H2Aio3RvJ73H9voj+p-1@api.pushover.net', $subject, $message);
Mailer::mailViaPHP( 'k55QGmp2Gi1nMskLgukjitdWEjy5AG@api.pushover.net', $subject, $message);