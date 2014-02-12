<?php
chdir('..');
chdir('..');
require_once 'init.php';

/*
Get subscribers who are not on waitlist and whose subscription_status is null
For each subscriber, getAccountStatus and save it in subscription_status
*/
$subscriber_dao = new SubscriberMySQLDAO();
$subscribers_to_update = $subscriber_dao->getSubscribersWithoutSubscriptionStatus();
$updated_subscribers = 0;
while (sizeof($subscribers_to_update) > 0 ) {
	foreach ($subscribers_to_update as $subscriber) {
		$subscription_status = $subscriber->getAccountStatus();
		$updated_subscribers += $subscriber_dao->updateSubscriptionStatus($subscriber->id, $subscription_status);
		$subscription_status = null;
	}
	echo "Updated ".$updated_subscribers." subscription_status<br>";
	$subscribers_to_update = $subscriber_dao->getSubscribersWithoutSubscriptionStatus();
}