--
-- New members by day.
--

SELECT COUNT(id) as total_subscribers, DATE(creation_time) as signup_date
FROM subscribers
GROUP BY DATE(subscribers.creation_time) order by subscribers.creation_time desc
LIMIT 0, 3;

--
-- Revenue by day.
--
SELECT count(id) as successful_payments, CONCAT('$', FORMAT(SUM(amount), 0)) as revenue, DATE(timestamp) AS date
FROM payments WHERE transaction_status = "Success" GROUP BY DATE(timestamp) ORDER BY timestamp DESC LIMIT 4;

--
-- New monthly subs by day.
--
SELECT count(id) as successful_payments, DATE(timestamp) AS date
FROM subscription_operations WHERE operation = "pay" GROUP BY DATE(timestamp) ORDER BY timestamp DESC LIMIT 30;

--
-- Average subs per day grouped by month
--
SELECT year, month, ROUND(total_subs / days_in_month, 1) AS subs_per_day FROM
(
SELECT timestamp, YEAR(timestamp) AS year, MONTH(timestamp) AS month, count(*) AS total_subs,
IF( (MONTH(timestamp)= 8 AND YEAR(timestamp)=2014), 7,
    IF ((MONTH(NOW())=MONTH(timestamp) AND YEAR(NOW())=YEAR(timestamp)), DAY(NOW()),
    DAY(LAST_DAY(timestamp)))
    ) AS days_in_month

FROM subscription_operations where operation='pay'
GROUP BY YEAR(timestamp), MONTH(timestamp) ORDER BY year, month DESC
) AS subscriptions

--
-- Subscriptions per week
--
SELECT date(timestamp) as date, YEARWEEK(timestamp) as week_of_year, count(*) AS total_subs
FROM subscription_operations where operation='pay' AND status_code = 'SS'
GROUP BY YEARWEEK(timestamp) ORDER BY timestamp DESC
