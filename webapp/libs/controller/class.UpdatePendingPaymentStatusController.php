<?php
/**
 * Update status for all pending payments.
 */
class UpdatePendingPaymentStatusController extends Controller {
    /**
     * How many to update at a time
     */
    const UPDATE_CAP = 50;

    public function control() {
        $payment_dao = new PaymentMySQLDAO();
        $total_pending_payments = $payment_dao->getTotalPendingPayments();

        try {
            $api_accessor = new AmazonFPSAPIAccessor();
            // Retrieve pending payments
            $pending_payments = $payment_dao->getPendingPayments(self::UPDATE_CAP);
            $total_processed = 0;

            $subscriber_dao = new SubscriberMySQLDAO();

            while ($total_processed < self::UPDATE_CAP && sizeof($pending_payments) > 0 ) {
                $updated_payment = null;
                $status = null;
                foreach ($pending_payments as $pending_payment) {
                    try {
                        $status = $api_accessor->getTransactionStatus($pending_payment['transaction_id']);
                        // Verify transaction ID and caller reference match a payment in the DB
                        $payment = $payment_dao->getPayment($status['transaction_id'], $status['caller_reference']);
                        if (isset($payment)) {
                            // Update payment status using the PaymentDAO
                            $updated_payment = $payment_dao->updateStatus($payment->id, $status['status'],
                                $status['status_message']);
                            $subscriber_dao->updateSubscriptionStatus($pending_payment['subscriber_id']);

                            $total_processed += $updated_payment;
                        } else {
                            echo('No such payment. Transaction ID: '.$status['transaction_id']. '  Caller Reference: '
                                .$status['caller_reference']);
                        }
                    } catch (Exception $e) {
                        echo get_class($e).': '. $e->getMessage();
                    }
                }
                $pending_payments = $payment_dao->getPendingPayments(self::UPDATE_CAP);
            }
        } catch (Exception $e) {
            echo get_class($e).': '. $e->getMessage();
        }
    }
}