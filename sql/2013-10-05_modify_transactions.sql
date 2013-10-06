ALTER TABLE  transactions ADD UNIQUE (token_id);

ALTER TABLE  transactions CHANGE  expiry  payment_method_expiry VARCHAR( 10 ) NULL COMMENT  'Payment method expiration date (optional).';

ALTER TABLE  transactions ADD  status_code VARCHAR( 2 ) NOT NULL COMMENT  'The status of the transaction request.' AFTER  amount ,
ADD  error_message VARCHAR( 255 ) NULL COMMENT  'Human readable message that specifies the reason for a request failure (optional).' AFTER  status_code ,
ADD INDEX (  status_code )


CREATE TABLE  transaction_status_codes (
code VARCHAR( 2 ) NOT NULL COMMENT  'Status code.',
description VARCHAR( 125 ) NOT NULL COMMENT  'Description.',
PRIMARY KEY (  code )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT =  'Amazon Flexible Payment System transaction status codes.';


INSERT INTO  transaction_status_codes ( code , description )
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
