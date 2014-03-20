SELECT COUNT(`id`), DATE(`creation_time`)
FROM `subscribers`
WHERE  `membership_level` NOT LIKE  'Waitlist'
GROUP BY DATE(subscribers.creation_time)
LIMIT 0, 300
