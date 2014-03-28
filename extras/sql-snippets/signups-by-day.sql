SELECT COUNT(id) as total_subscribers, DATE(creation_time) as signup_date
FROM subscribers
WHERE  membership_level !=  'Waitlist'
GROUP BY DATE(subscribers.creation_time) order by subscribers.creation_time desc
LIMIT 0, 30