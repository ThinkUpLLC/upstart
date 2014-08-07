#
# Fake-test free trial reminder emails on staging by fast-forwarding signup/reminder times and running email job.
#

#
# First reminder on day 2
#
UPDATE subscribers SET creation_time = DATE_SUB(creation_time, INTERVAL 3 DAY);

#
# Second reminder on day 7
#
UPDATE subscribers SET payment_reminder_last_sent = DATE_SUB(payment_reminder_last_sent, INTERVAL 145 HOUR);

#
# Third reminder on day 13
#
UPDATE subscribers SET payment_reminder_last_sent = DATE_SUB(payment_reminder_last_sent, INTERVAL 145 HOUR);

#
# Fourth and final reminder on day 14
#
UPDATE subscribers SET payment_reminder_last_sent = DATE_SUB(payment_reminder_last_sent, INTERVAL 25 HOUR);