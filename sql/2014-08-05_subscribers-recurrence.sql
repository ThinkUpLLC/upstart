--
-- Default to 1 month for subscription recurrence for new subscribers
--
ALTER TABLE subscribers ADD subscription_recurrence VARCHAR(10) NOT NULL DEFAULT '1 month' COMMENT 'How often subscription renews, 1 month or 12 months.' AFTER subscription_status;

--
-- Set all existing subscribers are 12 months
--
UPDATE subscribers SET subscription_recurrence = '12 months';