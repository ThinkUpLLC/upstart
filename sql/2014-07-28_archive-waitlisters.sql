#
# Move all waitlist subscribers to subscriber_archive
#

INSERT INTO subscriber_archive SELECT s.email, s.pwd, s.pwd_salt, s.creation_time, s.network_user_id,
s.network_user_name, s.network, s.full_name, s.follower_count, s.is_verified, s.oauth_access_token,
s.oauth_access_token_secret, s.verification_code, s.is_email_verified, s.is_from_waitlist,
s.membership_level, s.thinkup_username, s.date_installed, s.api_key_private, s.last_dispatched,
s.commit_hash, s.is_installation_active, a.token_id, a.amount,
a.status_code, a.error_message, a.payment_method_expiry, a.caller_reference, a.recurrence_period,
a.token_validity_start_date, s.subscription_status, s.total_payment_reminders_sent,
s.payment_reminder_last_sent, s.is_account_closed
FROM subscribers s LEFT JOIN subscriber_authorizations sa
ON s.id = sa.subscriber_id LEFT JOIN authorizations a ON a.id = sa.authorization_id
WHERE s.membership_level = 'Waitlist';


DELETE from subscribers WHERE membership_level = 'Waitlist';