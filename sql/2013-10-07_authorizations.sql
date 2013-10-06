DROP TABLE transactions;

CREATE TABLE authorizations (
  id int(11) NOT NULL AUTO_INCREMENT COMMENT 'Internal unique ID.',
  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time of transaction.',
  token_id varchar(100) NOT NULL COMMENT 'Token ID of transaction.',
  amount int(11) NOT NULL COMMENT 'Monetary amount of transaction in US Dollars.',
  status_code varchar(2) NOT NULL COMMENT 'The status of the transaction request.',
  error_message varchar(255) DEFAULT NULL COMMENT 'Human readable message that specifies the reason for a request failure (optional).',
  payment_method_expiry varchar(10) DEFAULT NULL COMMENT 'Payment method expiration date (optional).',
  PRIMARY KEY (id),
  UNIQUE KEY token_id (token_id),
  KEY status_code (status_code)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Amazon FPS recurring-use payment authorizations.';

ALTER TABLE  authorizations ADD  caller_reference VARCHAR( 20 ) NOT NULL COMMENT  'Caller reference used for authorization request.',
ADD  recurrence_period VARCHAR( 12 ) NOT NULL DEFAULT  '12 Months' COMMENT  'Recurrence period of payment authorization.',
ADD  token_validity_start_date DATE NOT NULL COMMENT  'Date the token becomes valid.';

DROP TABLE transaction_status_codes;

CREATE TABLE  authorization_status_codes (
code VARCHAR( 2 ) NOT NULL COMMENT  'Status code.',
description VARCHAR( 125 ) NOT NULL COMMENT  'Description.',
PRIMARY KEY (  code )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Amazon FPS authorization request status codes.';


INSERT INTO  authorization_status_codes ( code , description )
VALUES (
'SA',  'Success status for the ABT payment method.'
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

DROP TABLE subscriber_transactions;

CREATE TABLE  subscriber_authorizations (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT  'Internal unique ID.',
timestamp TIMESTAMP NOT NULL COMMENT  'Time transaction was recorded.',
subscriber_id INT NOT NULL COMMENT  'Subscriber ID keyed to subscribers.',
authorization_id INT NOT NULL COMMENT  'Authorization ID keyed to authorizations.'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Payment authorizations by known subscribers.';


ALTER TABLE  authorizations CHANGE  token_validity_start_date  token_validity_start_date TIMESTAMP NOT NULL COMMENT  'Date the token becomes valid.';