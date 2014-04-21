--
-- Run this after the batch update to the subscriber_status field.
--
SELECT count(id) as total, subscription_status FROM subscribers
WHERE subscription_status not like 'Paid through%' group by subscription_status
UNION
SELECT count(id) as total, subscription_status FROM subscribers
WHERE subscription_status like 'Paid through%'

--
-- Get membership_level of payment abandons
--
SELECT subscriber_count, membership_level, subscription_status, (subscriber_count*price) as total_payments_owed FROM
(
SELECT COUNT(id) AS subscriber_count, subscription_status, membership_level, 
IF(membership_level = 'Member', 60, 
	IF(membership_level = 'Early Bird', 50, 
		IF(membership_level = 'Late Bird', 50, 
			IF(membership_level = 'Pro', 120, 
				IF(membership_level = 'Exec', 996, 0))))) AS price FROM subscribers
WHERE subscription_status = 'Payment due' GROUP BY membership_level
) AS failed_payments ORDER BY total_payments_owed DESC;

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
