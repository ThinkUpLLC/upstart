--
-- Run this after the batch update to the subscriber_status field.
--
SELECT count(id) as total, subscription_status FROM subscribers GROUP BY subscription_status

--
-- Get membership_level of failed payments
--
SELECT subscriber_count, membership_level, subscription_status, (subscriber_count*price) as total_payments_owed FROM
(
SELECT COUNT(id) AS subscriber_count, subscription_status, membership_level,
IF(membership_level = 'Member', 60,
	IF(membership_level = 'Early Bird', 50,
		IF(membership_level = 'Late Bird', 50,
			IF(membership_level = 'Pro', 120,
				IF(membership_level = 'Exec', 996, 0))))) AS price FROM subscribers
WHERE subscription_status = 'Payment failed' GROUP BY membership_level
) AS failed_payments ORDER BY total_payments_owed DESC;

--
-- How many annual crowdfunders will renew on January 17, 2015?
--
SELECT total, membership_level, price, total * price AS revenue FROM
(
SELECT count(*) as total, membership_level,
IF(membership_level = 'Member', 60,
    IF(membership_level = 'Early Bird', 50,
        IF(membership_level = 'Late Bird', 50,
            IF(membership_level = 'Pro', 120,
                IF(membership_level = 'Exec', 996, 0))))) AS price FROM `subscribers`
WHERE subscription_recurrence = '12 months' AND is_account_closed = 0 AND subscription_status = 'Paid' AND
 paid_through IS NOT NULL AND DATE_FORMAT(paid_through, '%b %e %Y') = 'Jan 17 2015'
GROUP BY membership_level
) AS crowdfunders