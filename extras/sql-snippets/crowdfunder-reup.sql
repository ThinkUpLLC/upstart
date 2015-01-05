--
-- Crowdfunders: Annuals due for renewal on Jan 17th
--
SELECT count(*) as total FROM subscribers s
WHERE subscription_status = 'Paid'
AND date(paid_through) = '2015-01-17'
AND subscription_recurrence = '12 months'
AND is_account_closed != 1


--
-- Crowdfunders who have closed their account
-- On Jan 5, 2015: Total is 67
--
SELECT count(*) as total FROM subscribers s
WHERE subscription_status = 'Paid'
AND date(paid_through) = '2015-01-17'
AND subscription_recurrence = '12 months'
AND is_account_closed = 1


--
-- Crowdfunder re-ups by membership_level and with revenue totals
--
SELECT total, membership_level, price, total * price AS revenue FROM
(
SELECT count(*) as total, membership_level,
IF(membership_level = 'Member', 60,
    IF(membership_level = 'Early Bird', 50,
        IF(membership_level = 'Late Bird', 50,
            IF(membership_level = 'Pro', 120,
                IF(membership_level = 'Exec', 996, 0))))) AS price FROM subscribers
WHERE subscription_recurrence = '12 months' AND is_account_closed != 1 AND subscription_status = 'Paid' AND
 date(paid_through) = '2015-01-17'
GROUP BY membership_level
) AS crowdfunders


--
-- Crowdfunders with expired credit cards
--
SELECT thinkup_username, a.payment_method_expiry FROM subscribers s
INNER JOIN subscriber_authorizations sa ON sa.subscriber_id = s.id
INNER JOIN authorizations a ON a.id = sa.authorization_id
WHERE s.subscription_status = 'Paid'
AND date(s.paid_through) = '2015-01-17'
AND s.subscription_recurrence = '12 months'
AND s.is_account_closed != 1
AND a.payment_method_expiry like '%2014'


--
-- Crowdfunder expired credit cards by membership_level and with revenue totals
--
SELECT membership_level,
IF(membership_level = 'Member', 60,
    IF(membership_level = 'Early Bird', 50,
        IF(membership_level = 'Late Bird', 50,
            IF(membership_level = 'Pro', 120,
                IF(membership_level = 'Exec', 996, 0))))) AS price, count(*) as total
FROM subscribers s
INNER JOIN subscriber_authorizations sa ON sa.subscriber_id = s.id
INNER JOIN authorizations a ON a.id = sa.authorization_id
WHERE s.subscription_status = 'Paid'
AND date(s.paid_through) = '2015-01-17'
AND s.subscription_recurrence = '12 months'
AND s.is_account_closed != 1
AND a.payment_method_expiry like '%2014'
GROUP BY membership_level
