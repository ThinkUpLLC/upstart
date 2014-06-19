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

            //debug
            //print_r($pending_payments);

            $total_processed = 0;

            $subscriber_dao = new SubscriberMySQLDAO();

            while ($total_processed < self::UPDATE_CAP && sizeof($pending_payments) > 0 ) {
                $updated_payment = null;
                $status = null;
                foreach ($pending_payments as $pending_payment) {
                    $status = $api_accessor->getTransactionStatus($pending_payment['transaction_id']);
                    // Verify transaction ID and caller reference match a payment in the DB
                    $payment = $payment_dao->getPayment($status['transaction_id'], $status['caller_reference']);

                    // Update payment status using the PaymentDAO
                    $updated_payment = $payment_dao->updateStatus($payment->id, $status['status'],
                        $status['status_message']);
                    $subscriber_dao->updateSubscriptionStatus($pending_payment['subscriber_id']);
                    $subscriber = $subscriber_dao->getByID($pending_payment['subscriber_id']);

                    // Send an email to the member re: payment status
                    $email_view_mgr = new ViewManager();
                    $email_view_mgr->caching=false;
                    $template_name = "Upstart System Messages";
                    $cfg = Config::getInstance();
                    $api_key = $cfg->getValue('mandrill_api_key_for_payment_reminders');

                    if ($status['status'] == 'Success') {
                        $subject_line = "Thanks for joining ThinkUp!";
                        $email_view_mgr->assign('member_level', $subscriber->membership_level);
                        $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
                        $email_view_mgr->assign('amount', $payment->amount);
                        $user_installation_url = $cfg->getValue('user_installation_url');
                        $subscriber->installation_url = str_replace ("{user}", $subscriber->thinkup_username,
                            $user_installation_url);

                        $paid_through_year = intval(date('Y', strtotime($payment->timestamp))) + 1;
                        $paid_through_date = date('M j, ', strtotime($payment->timestamp));
                        $email_view_mgr->assign('renewal_date', $paid_through_date.$paid_through_year );
                        $email_view_mgr->assign('installation_url', $subscriber->installation_url );
                        $body_html = $email_view_mgr->fetch('_email.payment-charge-successful.tpl');
                        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
                        Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                            array('html_body'=>$message), $api_key);
                    } else {
                        $subject_line = "Uh oh! Problem with your ThinkUp payment";
                        $email_view_mgr->assign('amount', $payment->amount);

                        if (isset($status['status_message'])
                            && (strpos($status['status_message'], '<?xml version=') === false)) {
                            $email_view_mgr->assign('amazon_error_message', $status['status_message'] );
                        } else {
                            $email_view_mgr->assign('amazon_error_message', null );
                        }
                        $body_html = $email_view_mgr->fetch('_email.payment-charge-failure.tpl');

                        $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
                        Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                            array('html_body'=>$message), $api_key);
                    }
                    $total_processed += $updated_payment;
                }
                $pending_payments = $payment_dao->getPendingPayments(self::UPDATE_CAP);
            }
        } catch (Exception $e) {
            echo get_class($e).': '. $e->getMessage()."
";
        }
    }
}