
CREATE TABLE subscription_operations (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of insertion.',
  subscriber_id int(11) NOT NULL COMMENT 'Subscriber ID.',
  operation varchar(25) NOT NULL COMMENT 'Operation performed on Amazon.',
  payment_reason varchar(100) NOT NULL COMMENT 'Reason for payment.',
  transaction_amount varchar(100) NOT NULL COMMENT 'Amount of transaction.',
  recurring_frequency varchar(25) NOT NULL COMMENT 'How often subscription recurs, 1 month or 12 months.',
  status_code varchar(2) NOT NULL COMMENT 'Transaction status code.',
  buyer_email varchar(255) NOT NULL COMMENT 'Amazon''s buyer email address.',
  reference_id varchar(20) NOT NULL COMMENT 'Caller reference for transaction.',
  amazon_subscription_id varchar(100) NOT NULL COMMENT 'Amazon''s subscription ID.',
  transaction_date timestamp NOT NULL COMMENT 'Amazon''s transaction date.',
  buyer_name varchar(255) NOT NULL COMMENT 'Amazon''s buyer name.',
  payment_method varchar(25) NOT NULL COMMENT 'Payment method.',
  PRIMARY KEY (id),
  KEY subscriber_id (subscriber_id),
  UNIQUE KEY amazon_subscription_id (amazon_subscription_id,reference_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Amazon subscription operations.';

-- CREATE TABLE authorization_status_codes (
-- `code` varchar(2) NOT NULL COMMENT 'Status code.',
--  description varchar(125) NOT NULL COMMENT 'Description.',
--  PRIMARY KEY (`code`)
-- ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Amazon FPS authorization request status codes.';

CREATE TABLE subscription_status_codes LIKE authorization_status_codes;

-- Populate subscription status codes

INSERT INTO  subscription_status_codes ( code , description )
VALUES (
'SA',  'Success status for the ABT payment method.'
), (
'SS',  'The subscription was completed.'
), (
'SF',  'The subscription failed.'
), (
'SI',  'The subscription has initiated.'
), (
'SB',  'Success status for the ACH (bank account) payment method.'
), (
'SC',  'Success status for the credit card payment method.'
), (
'SE',  'System error.'
), (
'A',  'Buyer abandoned the pipeline.'
), (
'CE',  'Specifies a caller exception.'
), (
'PE',  'Payment Method Mismatch Error: Specifies that the buyer does not have payment method that you have requested.'
), (
'NP',  'This account type does not support the specified payment method.'
), (
'NM',  'You are not registered as a third-party caller to make this transaction. Contact Amazon Payments for more information.'
);

DROP TABLE authorization_status_codes;