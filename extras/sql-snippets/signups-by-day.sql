--
-- New members by day.
--

SELECT COUNT(id) as total_subscribers, DATE(creation_time) as signup_date
FROM subscribers
WHERE  membership_level !=  'Waitlist'
GROUP BY DATE(subscribers.creation_time) order by subscribers.creation_time desc
LIMIT 0, 3;

--
-- Revenue by day.
--
SELECT count(id) as successful_payments, CONCAT('$', FORMAT(SUM(amount), 0)) as revenue, DATE(timestamp) AS date 
FROM payments WHERE transaction_status = "Success" GROUP BY DATE(timestamp) ORDER BY timestamp DESC LIMIT 4;