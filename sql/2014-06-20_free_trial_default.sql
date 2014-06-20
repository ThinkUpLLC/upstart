ALTER TABLE subscribers CHANGE subscription_status subscription_status VARCHAR(50)
CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'Free trial' COMMENT 'Status of subscription payment.';