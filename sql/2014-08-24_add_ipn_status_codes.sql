INSERT INTO  subscription_status_codes ( code , description )
VALUES
(
'RS',  'The refund transaction was successful.'
), (
'RF',  'The refund transaction failed.'
), (
'PI',  'Payment has been initiated.'
), (
'PS',  'The payment transaction was successful.'
), (
'PF',  'Payment failed. Direct customer to the Amazon Payments Authorization page to select a different payment method.'
);