ALTER TABLE subscriber_archive ADD id INT(11) NULL COMMENT 'Subscriber ID.' FIRST;

ALTER TABLE subscriber_archive ADD claim_code VARCHAR(24) NULL COMMENT 'Redeemed claim code.' ;

ALTER TABLE subscriber_archive ADD paid_through TIMESTAMP NULL COMMENT 'Membership is paid for through this date.' AFTER subscription_status;
