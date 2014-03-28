<?php
chdir('..');
chdir('..');
require_once 'init.php';

/* BEGIN CONFIGURATION */

$UPDATE_CAP = 50;

/* END CONFIGURATION */

$payment_dao = new PaymentMySQLDAO();
$total_payments_to_update = $payment_dao->getTotalPaymentsToUpdate();
echo $total_payments_to_update." payments are in Pending status
";

try {
    $api_accessor = new AmazonFPSAPIAccessor();
    // Retrieve subscribers (with authorization info) who have authorizations but who do NOT have payments
    $transactions_to_update = $payment_dao->getPaymentsToUpdate($UPDATE_CAP);
    $total_updated = 0;
    echo 'Updating '.
    (($UPDATE_CAP > $total_payments_to_update)?$total_payments_to_update:$UPDATE_CAP).' transactions...
';

    while ($total_updated < $UPDATE_CAP && sizeof($transactions_to_update) > 0 ) {
        $updated_payment = null;
        $status = null;
        foreach ($transactions_to_update as $transaction_to_update) {
            try {
                $status = $api_accessor->getTransactionStatus($transaction_to_update['transaction_id']);
                // Verify transaction ID and caller reference match a payment in the DB
                $payment = $payment_dao->getPayment($status['transaction_id'], $status['caller_reference']);
                if (isset($payment)) {
                    // Update payment status using the PaymentDAO
                    $updated_payment = $payment_dao->updateStatus($payment->id, $status['status'],
                    $status['status_message']);
                    if ($updated_payment > 0) {
                        echo 'Success updating '.$transaction_to_update['transaction_id'];
                    } else {
                        echo 'Failure updating '.$transaction_to_update['transaction_id'];
                    }
                } else {
                    echo('No such payment. Transaction ID: '.$status['transaction_id'].
                    '  Caller Reference: '.$status['caller_reference']);
                }
            } catch (Exception $e) {
                echo 'Caught exception: '.$e->getMessage();
            }
            echo "
";
        }
        $total_updated += sizeof($transactions_to_update);
        $transactions_to_update = $payment_dao->getPaymentsToUpdate($UPDATE_CAP);
    }

    echo "Updated ".$total_updated." transactions.
";
    $daily_revenue = $payment_dao->getDailyRevenue();
    echo $daily_revenue[0]['successful_payments']. " successful payments today totaling ".$daily_revenue[0]['revenue'];
    if ($daily_revenue[0]['revenue'] > $daily_revenue[1]['revenue']) {
        echo " up from ";
    } else {
        echo " down from ";
    }
    echo $daily_revenue[1]['revenue']. " yesterday. Day before was ".$daily_revenue[2]['revenue'] . ".
";

} catch (Exception $e) {
    echo $e->getMessage();
}
