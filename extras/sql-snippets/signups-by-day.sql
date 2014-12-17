--
-- New members by day.
--

SELECT COUNT(id) as total_subscribers, DATE(creation_time) as signup_date
FROM subscribers
GROUP BY DATE(subscribers.creation_time) order by subscribers.creation_time desc
LIMIT 0, 3;

--
-- New monthly subs by day.
--
SELECT count(id) as successful_payments, DATE(timestamp) AS date
FROM subscription_operations WHERE operation = "pay" GROUP BY DATE(timestamp) ORDER BY timestamp DESC LIMIT 30;

--
-- Refunds (cancellations by day)
--

SELECT COUNT( id ) AS refunds, DATE( TIMESTAMP ) AS DATE
FROM subscription_operations WHERE operation =  "refund" GROUP BY DATE( TIMESTAMP )  ORDER BY TIMESTAMP DESC LIMIT 30;

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

--
-- Revenue by day. [Old annual subscriptions, pre-monthly]
--
SELECT count(id) as successful_payments, CONCAT('$', FORMAT(SUM(amount), 0)) as revenue, DATE(timestamp) AS date
FROM payments WHERE transaction_status = "Success" GROUP BY DATE(timestamp) ORDER BY timestamp DESC LIMIT 4;

--
-- New subscriptions per month
--
SELECT YEAR(timestamp) AS year, MONTH(timestamp) AS month, count(*) AS new_subs
FROM subscription_operations where operation='pay' AND status_code = 'SS'
GROUP BY MONTH(timestamp), YEAR(timestamp) ORDER BY timestamp DESC

--
-- Revenue per month
--
SELECT SUM(successful_payments), month_of_year AS month, year FROM (
SELECT *, (transaction_amount * total_subs) as successful_payments FROM (
SELECT DATE( TIMESTAMP ) AS DATE, MONTH( TIMESTAMP ) AS month_of_year, YEAR( TIMESTAMP) AS year, COUNT( * ) AS total_subs, status_code, replace(transaction_amount, 'USD ','') as transaction_amount
FROM subscription_operations
WHERE status_code =  'PS'
GROUP BY MONTH( TIMESTAMP ) , transaction_amount
) subtable
) subtable
GROUP BY month_of_year, year

--
-- Refunds per month
--
SELECT round(SUM(refund_amount)), month_of_year AS month, year FROM (
SELECT *, (transaction_amount * total_subs) AS refund_amount FROM (
SELECT DATE( TIMESTAMP ) AS DATE, MONTH( TIMESTAMP ) AS month_of_year, YEAR( TIMESTAMP) AS year, COUNT( * ) AS total_subs, status_code, replace(transaction_amount, 'USD ','') as transaction_amount
FROM subscription_operations
WHERE operation =  'refund'
GROUP BY MONTH( TIMESTAMP ) , transaction_amount
) subtable
) subtable
GROUP BY month_of_year, year

--
-- Monthly revenue + refunds by month
--
SELECT * FROM
((SELECT SUM(successful_payments) AS total, 'payments' AS type, month_of_year AS month, year FROM (
SELECT *, (transaction_amount * total_subs) as successful_payments FROM (
SELECT DATE( TIMESTAMP ) AS DATE, MONTH( TIMESTAMP ) AS month_of_year, YEAR( TIMESTAMP) AS year, COUNT( * ) AS total_subs, status_code, replace(transaction_amount, 'USD ','') as transaction_amount
FROM subscription_operations
WHERE status_code =  'PS'
GROUP BY MONTH( TIMESTAMP ) , transaction_amount
) subtable
) subtable
GROUP BY month_of_year, year)

UNION

(SELECT round(SUM(refund_amount)) AS total, 'refunds' AS type, month_of_year AS month, year FROM (
SELECT *, (transaction_amount * total_subs) AS refund_amount FROM (
SELECT DATE( TIMESTAMP ) AS DATE, MONTH( TIMESTAMP ) AS month_of_year, YEAR( TIMESTAMP) AS year, COUNT( * ) AS total_subs, status_code, replace(transaction_amount, 'USD ','') as transaction_amount
FROM subscription_operations
WHERE operation =  'refund'
GROUP BY MONTH( TIMESTAMP ) , transaction_amount
) subtable
) subtable
GROUP BY month_of_year, year)) subtable
ORDER BY year DESC, month DESC, type

--
-- Trial signups by week
--
SELECT DATE(creation_time) AS date, COUNT(email) AS signups
FROM
( SELECT email, creation_time, thinkup_username FROM subscriber_archive WHERE creation_time > '2014-07-08'
UNION
SELECT email, creation_time, thinkup_username FROM subscribers WHERE creation_time > '2014-07-08') all_subscribers
GROUP BY WEEKOFYEAR(creation_time), YEAR(creation_time) ORDER BY creation_time ASC

--
-- Conversions by week
--
SELECT DATE(timestamp) AS date, count(*) AS subscriptions FROM subscription_operations
WHERE operation='pay' AND status_code = 'SS'
GROUP BY WEEKOFYEAR(timestamp), YEAR(timestamp) ORDER BY timestamp ASC

--
-- Revenue by week
--
SELECT SUM(successful_payments) AS revenue, week_of_year FROM (
SELECT *, (transaction_amount * total_subs) as successful_payments FROM (
SELECT CONCAT( YEAR(timestamp), '-', WEEKOFYEAR(timestamp)) AS week_of_year, COUNT( * ) AS total_subs, status_code, replace(transaction_amount, 'USD ','') AS transaction_amount
FROM subscription_operations
WHERE status_code =  'PS'
GROUP BY WEEKOFYEAR( timestamp ), YEAR(timestamp), transaction_amount
ORDER BY timestamp ASC
) subtable
) subtable
GROUP BY week_of_year


