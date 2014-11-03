<?php
chdir('..');
chdir('..');
chdir('..');
require_once 'init.php';

/**
 * Populate subscribers.paid_through and subscribers.subscription_status fields based on payment info
 * in subscription_operations and payments.
 */

//Get 50 subscribers, foreach, getSubStatusAndRecurrence, and update table with those fields
$subscription_helper = new SubscriptionHelper();
$subscriber_dao = new SubscriberMySQLDAO();
$page = 1;
$total_subscribers = 50;
$subscribers = $subscriber_dao->getSubscriberList($page, $total_subscribers);

while (sizeof($subscribers) > 0) {
    foreach ($subscribers as $subscriber) {
        $new_values = $subscription_helper->getSubscriptionStatusAndPaidThrough($subscriber);
        if ($new_values['subscription_status'] != $subscriber->subscription_status ) {
            $subscriber_dao->setSubscriptionStatus($subscriber->id, $new_values['subscription_status']);
        }
        if (isset($new_values['paid_through'])) {
            $subscriber_dao->setPaidThrough($subscriber->id, $new_values['paid_through']);
        }
    }
    $page++;
    $subscribers = $subscriber_dao->getSubscriberList($page, $total_subscribers);
}
