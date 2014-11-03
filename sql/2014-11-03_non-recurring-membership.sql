ALTER TABLE subscribers ADD claim_code VARCHAR(24) NULL COMMENT 'Redeemed claim code.' ;

ALTER TABLE subscribers ADD paid_through TIMESTAMP NULL COMMENT 'Membership is paid for through this date.' AFTER subscription_status;

ALTER TABLE subscribers COMMENT = 'Subscribers who have added a social network account.';

ALTER TABLE subscribers CHANGE subscription_recurrence subscription_recurrence VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1 month' COMMENT 'How often membership renews, 1 month, 12 months or None.';