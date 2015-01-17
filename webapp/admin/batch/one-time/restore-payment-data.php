<?php
/**
 * On January 14th 2015 we deployed a bug which prevented Upstart from capturing new subscriptions and Amazon IPN
 * notifications. On January 16th, we gathered this data from the error log and re-inserted it manually in this script.
 */
chdir('..');
chdir('..');
chdir('..');
require_once 'init.php';

$d = array();

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BQ2RUO94JBV76OR4EEKU9BLRSILLQGB5D",
    "status"=>"PS",
    "nextTransactionDate"=>"1424114260",
    "buyerEmail"=>"amandarose@gmail.com",
    "referenceId"=>"13570_1421435979",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=>"1421435861",
    "buyerName"=>"amanda wallwin",
    "subscriptionId"=>"31b67cb0-34b1-4b36-9712-4d386fb690e9",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BQ2RUO94JBV76OR4EEKU9BLRSILLQGB5D",
    "status"=>"PI",
    "buyerEmail"=>"amandarose@gmail.com",
    "referenceId"=>"13570_1421435979",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=>"1421435860",
    "buyerName"=>"amanda wallwin",
    "subscriptionId"=>"31b67cb0-34b1-4b36-9712-4d386fb690e9",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "startValidityDate"=>"1421435860",
    "status"=>"SubscriptionSuccessful",
    "buyerEmail"=>"amandarose@gmail.com",
    "referenceId"=>"13570_1421435979",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "buyerName"=>"amanda wallwin",
    "subscriptionId"=>"31b67cb0-34b1-4b36-9712-4d386fb690e9",
    "recurringFrequency"=>"1 MONTH",
    "recipientName"=>"ThinkUp, LLC",
    "noOfPromotionTransactions"=>"0",
    "paymentMethod"=>"CC",
    "transactionDate"=>"1421435860"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BNHJ6QB7GLE6KL4PHR94ZS9VT57DO2KNV",
    "status"=>"PS",
    "nextTransactionDate"=>"1424029039",
    "buyerEmail"=>"jamesjshields@comcast.net",
    "referenceId"=>"17776_1421350749",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421350640",
    "buyerName"=>"James Shields",
    "subscriptionId"=>"5fa5ec19-6316-4f99-b8a1-bac895806521",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BNHJ6QB7GLE6KL4PHR94ZS9VT57DO2KNV",
    "status"=>"PI",
    "buyerEmail"=>"jamesjshields@comcast.net",
    "referenceId"=>"17776_1421350749",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421350639",
    "buyerName"=>"James Shields",
    "subscriptionId"=>"5fa5ec19-6316-4f99-b8a1-bac895806521",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "startValidityDate"=>"1421350639",
    "status"=>"SubscriptionSuccessful",
    "buyerEmail"=>"jamesjshields@comcast.net",
    "referenceId"=>"17776_1421350749",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "buyerName"=>"James Shields",
    "subscriptionId"=>"5fa5ec19-6316-4f99-b8a1-bac895806521",
    "recurringFrequency"=>"1 MONTH",
    "recipientName"=>"ThinkUp, LLC",
    "noOfPromotionTransactions"=>"0",
    "paymentMethod"=>"CC",
    "transactionDate"=> "1421350639",
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BNCS9543MVTM3JQHZ3VG47E6NEVZCGASA",
    "status"=>"PS",
    "nextTransactionDate"=>"1424024093",
    "buyerEmail"=>"benjamin.spall@gmail.com",
    "referenceId"=>"17898_1421342625",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421345694",
    "buyerName"=>"Benjamin",
    "subscriptionId"=>"5b80f80d-89a6-45d5-a460-1b2487c1be95",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "startValidityDate"=>"1421345693",
    "status"=>"SubscriptionSuccessful",
    "buyerEmail"=>"benjamin.spall@gmail.com",
    "referenceId"=>"17898_1421342625",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "buyerName"=>"Benjamin",
    "subscriptionId"=>"5b80f80d-89a6-45d5-a460-1b2487c1be95",
    "recurringFrequency"=>"1 MONTH",
    "recipientName"=>"ThinkUp, LLC",
    "noOfPromotionTransactions"=>"0",
    "paymentMethod"=>"CC",
    "transactionDate"=> "1421345693"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BNCS9543MVTM3JQHZ3VG47E6NEVZCGASA",
    "status"=>"PI",
    "buyerEmail"=>"benjamin.spall@gmail.com",
    "referenceId"=>"17898_1421342625",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421345693",
    "buyerName"=>"Benjamin",
    "subscriptionId"=>"5b80f80d-89a6-45d5-a460-1b2487c1be95",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BMUNCAB2BQNLULHENK7UJCDJ6NZJMNJBZ",
    "status"=>"PS",
    "nextTransactionDate"=>"1424009253",
    "buyerEmail"=>"rststoermer@gmail.com",
    "referenceId"=>"17190_1421330993",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421330854",
    "buyerName"=>"Taylor Stoermer",
    "subscriptionId"=>"808fd4ad-cb70-489a-a2c9-313b474219c0",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "startValidityDate"=>"1421330853",
    "status"=>"SubscriptionSuccessful",
    "buyerEmail"=>"rststoermer@gmail.com",
    "referenceId"=>"17190_1421330993",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "buyerName"=>"Taylor Stoermer",
    "subscriptionId"=>"808fd4ad-cb70-489a-a2c9-313b474219c0",
    "recurringFrequency"=>"1 MONTH",
    "recipientName"=>"ThinkUp, LLC",
    "noOfPromotionTransactions"=>"0",
    "paymentMethod"=>"CC",
    "transactionDate"=> "1421330853"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BMUNCAB2BQNLULHENK7UJCDJ6NZJMNJBZ",
    "status"=>"PI",
    "buyerEmail"=>"rststoermer@gmail.com",
    "referenceId"=>"17190_1421330993",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421330853",
    "buyerName"=>"Taylor Stoermer",
    "subscriptionId"=>"808fd4ad-cb70-489a-a2c9-313b474219c0",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC",
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BLK9P6U4OVOK81UHH9BHK9V7MJT1VQR9H",
    "status"=>"PS",
    "nextTransactionDate"=>"1423964767",
    "buyerEmail"=>"palmer.j.r@gmail.com",
    "referenceId"=>"19204_1421286521",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421286368",
    "buyerName"=>"Joseph Palmer",
    "subscriptionId"=>"02de4cc2-66c3-47f4-b5a1-5b801faead80",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "startValidityDate"=>"1421286367",
    "status"=>"SubscriptionSuccessful",
    "buyerEmail"=>"palmer.j.r@gmail.com",
    "referenceId"=>"19204_1421286521",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "buyerName"=>"Joseph Palmer",
    "subscriptionId"=>"02de4cc2-66c3-47f4-b5a1-5b801faead80",
    "recurringFrequency"=>"1 MONTH",
    "recipientName"=>"ThinkUp, LLC",
    "noOfPromotionTransactions"=>"0",
    "paymentMethod"=>"CC",
    "transactionDate"=> "1421286367"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BLK9P6U4OVOK81UHH9BHK9V7MJT1VQR9H",
    "status"=>"PI",
    "buyerEmail"=>"palmer.j.r@gmail.com",
    "referenceId"=>"19204_1421286521",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421286367",
    "buyerName"=>"Joseph Palmer",
    "subscriptionId"=>"02de4cc2-66c3-47f4-b5a1-5b801faead80",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BLJPSCL24U8GV18KJ6LURC4IHBDL6MJ4R",
    "status"=>"PS",
    "nextTransactionDate"=>"1423964246",
    "buyerEmail"=>"kevinchavis@gmail.com",
    "referenceId"=>"17460_1421285991",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421285847",
    "buyerName"=>"Kevin Chavis",
    "subscriptionId"=>"dfe10a72-911e-4b5a-9848-f7b2cb6dd43f",
    "operation"=>"pay",
    "paymentMethod"=>"CC"
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "startValidityDate"=>"1421285846",
    "status"=>"SubscriptionSuccessful",
    "buyerEmail"=>"kevinchavis@gmail.com",
    "referenceId"=>"17460_1421285991",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "buyerName"=>"Kevin Chavis",
    "subscriptionId"=>"dfe10a72-911e-4b5a-9848-f7b2cb6dd43f",
    "recurringFrequency"=>"1 MONTH",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC",
    "transactionDate"=> "1421285846",
);

$d[] = array(
    "paymentReason"=>"ThinkUp.com monthly membership",
    "transactionAmount"=>"USD 5.00",
    "signatureMethod"=>"RSA-SHA1",
    "transactionId"=>"19BLJPSCL24U8GV18KJ6LURC4IHBDL6MJ4R",
    "status"=>"PI",
    "buyerEmail"=>"kevinchavis@gmail.com",
    "referenceId"=>"17460_1421285991",
    "recipientEmail"=>"hostmaster@thinkup.com",
    "transactionDate"=> "1421285846",
    "buyerName"=>"Kevin Chavis",
    "subscriptionId"=>"dfe10a72-911e-4b5a-9848-f7b2cb6dd43f",
    "operation"=>"pay",
    "recipientName"=>"ThinkUp, LLC",
    "paymentMethod"=>"CC"
);

$subscription_operation_dao = new SubscriptionOperationMySQLDAO();
$subscriber_dao = new SubscriberMySQLDAO();
$subscription_helper = new SubscriptionHelper();

$rd = array_reverse($d);
foreach ($rd as $post) {
    try {
        $past_op = $subscription_operation_dao->getByAmazonSubscriptionID($post['subscriptionId']);
        if (isset($past_op)) {
            $subscriber = $subscriber_dao->getByID($past_op->subscriber_id);
            $op = new SubscriptionOperation();
            $op->subscriber_id = $subscriber->id;
            $op->recurring_frequency = $past_op->recurring_frequency;
            $op->payment_reason = $post['paymentReason'];
            $op->transaction_amount = $post['transactionAmount'];
            $op->status_code = $post['status'];
            $op->buyer_email = $post['buyerEmail'];
            $op->reference_id = $post['referenceId'];
            $op->amazon_subscription_id = $post['subscriptionId'];
            $op->buyer_name = $post['buyerName'];
            $op->payment_method = $post['paymentMethod'];
            $op->operation = (isset($post['operation']))?$post['operation']:'unspecified';
            $op->transaction_date = (isset($post['transactionDate']))?
                $post['transactionDate']:"NOW()";
            $op->timestamp = (isset($post['transactionDate']))?
                $post['transactionDate']:"NOW()";

            $subscription_operation_dao->insertWithTimestamp($op);

            //Set new paid_through date and update status
            //Inefficient workaround alert:
            //For inexplicable reasons, we have to retrieve the operation from the database here
            //instead of just passing it to SubscriptionHelper
            //because once it's inserted into the database, the transaction_date gets formatted
            //correctly, in a way that PHP strtotime and date() just won't.
            $op = $subscription_operation_dao->getByAmazonSubscriptionID($post['subscriptionId']);
            $subscription_helper->updateSubscriptionStatusAndPaidThrough($subscriber, $op);

            echo $op->status_code." for ".$subscriber->thinkup_username
                .'\nhttps://www.thinkup.com/join/admin/subscriber.php?id='. $subscriber->id."
";
        } else {
            $splitted = split("_",$post['referenceId']);
            $subscriber_id = $splitted[0];
            //handle new ops!
            $op = new SubscriptionOperation();
            $op->subscriber_id = $subscriber_id;
            $op->payment_reason = $post['paymentReason'];
            $op->transaction_amount = $post['transactionAmount'];
            $op->status_code = $post['status'];
            $op->buyer_email = $post['buyerEmail'];
            //@TODO Verify the reference_id starts with the subscriber ID
            $op->reference_id = $post['referenceId'];
            $op->amazon_subscription_id = $post['subscriptionId'];
            $op->transaction_date = (isset($post['transactionDate']))?
                $post['transactionDate']:"NOW()";
            $op->buyer_name = $post['buyerName'];
            $op->operation = (isset($post['operation']))?$post['operation']:'unspecified';
            $op->recurring_frequency = '1 month';
            $op->payment_method = $post['paymentMethod'];
            $op->timestamp = (isset($post['transactionDate']))?
                $post['transactionDate']:"NOW()";

            $subscription_operation_dao->insertWithTimestamp($op);

            //Set new paid_through date and update status
            //Inefficient workaround alert:
            //For inexplicable reasons, we have to retrieve the operation from the database here
            //instead of just passing it to SubscriptionHelper
            //because once it's inserted into the database, the transaction_date gets formatted
            //correctly, in a way that PHP strtotime and date() just won't.
            $op = $subscription_operation_dao->getByAmazonSubscriptionID($post['subscriptionId']);
            $subscriber = $subscriber_dao->getByID($subscriber_id);
            $subscription_helper->updateSubscriptionStatusAndPaidThrough($subscriber, $op);

            echo $op->status_code." for ".$subscriber->thinkup_username
              .'\nhttps://www.thinkup.com/join/admin/subscriber.php?id='. $subscriber_id."
";
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    $past_op = null;
    $op = null;
    $subscriber = null;
}
