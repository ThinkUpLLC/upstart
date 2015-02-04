ALTER TABLE subscribers ADD total_reup_reminders_sent INT(11) NOT NULL DEFAULT 0
COMMENT 'The number of annual reup reminder emails sent to this subscriber.'
AFTER payment_reminder_last_sent,
ADD reup_reminder_last_sent TIMESTAMP NULL DEFAULT NULL
COMMENT 'Last time a reup reminder was sent to this subscriber.' AFTER total_reup_reminders_sent;


--
-- Update fields to reflect what we sent via MailChimp
-- https://trello.com/c/Y54R4dOV/492-mailchimp-feb-1st-4th-5th-6th-annual-renewals-2nd-notification-up-to-21st-1st-notification-rolling-re-ups-for-annual-subscribers
--
-- First/Year in review sent to members through 2015-02-20
--

UPDATE subscribers SET total_reup_reminders_sent = 1, reup_reminder_last_sent = '2015-02-03 15:02:00'
WHERE subscription_status = 'Paid' AND is_account_closed = 0
AND subscription_recurrence = '12 months'
AND date(paid_through) <= '2015-02-20';

--
-- Second/Final payment reminder sent to members through 2015-02-07
--
UPDATE subscribers SET total_reup_reminders_sent = 2, reup_reminder_last_sent = '2015-02-03 15:02:00'
WHERE subscription_status = 'Paid' AND is_account_closed = 0
AND subscription_recurrence = '12 months'
AND date(paid_through) <= '2015-02-07';


-- Feb 4th: 1st reminder 2/18, 2nd reminder 2/11*
-- Feb 5th: 1st reminder 2/19, 2nd reminder 2/12*
-- Feb 6th: 1st reminder 2/20, 2nd reminder 2/13*
-- Feb 7th: 1st reminder 2/21* 2nd reminder 2/14*