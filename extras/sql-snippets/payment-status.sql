--
-- Run this after the batch update to the subscriber_status field.
--
SELECT count(id) as total, subscription_status FROM subscribers
where subscription_status not like 'Paid through%' group by subscription_status
UNION
SELECT count(id) as total, subscription_status FROM subscribers
where subscription_status like 'Paid through%'