ALTER TABLE subscriber_archive ADD subscription_status VARCHAR(50) NULL COMMENT 'Status of subscription payment.';

ALTER TABLE subscriber_archive ADD total_payment_reminders_sent INT NOT NULL DEFAULT '0' COMMENT 'The number of payment reminder emails sent to this subscriber.' ,
ADD payment_reminder_last_sent TIMESTAMP NULL COMMENT 'Last time a payment reminder was sent to this subscriber.';