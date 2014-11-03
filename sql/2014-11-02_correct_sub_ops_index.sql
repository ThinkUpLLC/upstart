ALTER TABLE  subscription_operations DROP INDEX  amazon_subscription_id ,
ADD UNIQUE  amazon_subscription_id (  amazon_subscription_id ,  reference_id ,  status_code ,  transaction_date );