ALTER TABLE subscription_operations CHANGE status_code status_code VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Transaction status code.';

DROP INDEX amazon_subscription_id on subscription_operations;

CREATE UNIQUE INDEX amazon_subscription_id ON subscription_operations (amazon_subscription_id,reference_id,status_code);