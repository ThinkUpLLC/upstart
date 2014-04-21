<?php
chdir('..');
chdir('..');
require_once 'init.php';

$subscriber_dao = new SubscriberMySQLDAO();
/**
 * Clear subscription_status for all subscribers.
 * TODO: Do this more gracefully; rather than clear everyone's status and repopulate, only update the ones who need
 * an update.
 */
$total_subscribers_cleared = $subscriber_dao->clearSubscriptionStatus();
echo "Cleared ".$total_subscribers_cleared." subscribers' subscription_status.
";
/**
 * Get subscribers who are not on waitlist and whose subscription_status is null
 * For each subscriber, getSubscriptionStatus and save it in subscription_status.
 */
$subscribers_to_update = $subscriber_dao->getSubscribersWithoutSubscriptionStatus();
$updated_subscribers = 0;
while (sizeof($subscribers_to_update) > 0 ) {
	foreach ($subscribers_to_update as $subscriber) {
		$subscription_status = $subscriber->getSubscriptionStatus();
		$updated_subscribers += $subscriber_dao->updateSubscriptionStatus($subscriber->id, $subscription_status);
		$subscription_status = null;
	}
	$subscribers_to_update = $subscriber_dao->getSubscribersWithoutSubscriptionStatus();
}
echo "Updated ".$updated_subscribers." subscribers' subscription_status.";
