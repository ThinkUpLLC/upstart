<?php
/**
 * Update status for all pending payments.
 */
class UpdatePendingPaymentStatusController extends Controller {

    public function control() {
        $payment_dao = new PaymentMySQLDAO();
        $subscriber_dao = new SubscriberMySQLDAO();
        $processed_payment_ids = array();

        try {
            $api_accessor = new AmazonFPSAPIAccessor();
            // Retrieve pending payments
            $pending_payments = $payment_dao->getPendingPayments();

            while (sizeof($pending_payments) > 0 ) {
                //debug
                // print_r($pending_payments);
                // print_r($processed_payment_ids);

                $updated_payment = null;
                $status = null;
                foreach ($pending_payments as $payment) {
                    // Check the Amazon Payments API for new status
                    $status = $api_accessor->getTransactionStatus($payment->transaction_id);

                    //debug
                    // print_r($status);

                    // Add this id to the list of processed IDs, so we don't keep checking in a continuous loop
                    $processed_payment_ids[] = $payment->id;

                    // Only update and send email notification if the status is no longer Pending
                    if ($status['status'] !== 'Pending') {
                        // Update payment status using the PaymentDAO
                        $updated_payment_total = $payment_dao->updateStatus($payment->id, $status['status'],
                            $status['status_message']);
                        $subscriber_dao->updateSubscriptionStatus($payment->subscriber_id);
                        $subscriber = $subscriber_dao->getByID($payment->subscriber_id);

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
                        } elseif ($status['status'] == 'Failure') {
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
                    }
                }
                // Clear pending payments queue
                $pending_payments = array();
                $next_set_of_pending_payments = $payment_dao->getPendingPayments();

                // Only add new pending payments to the queue, not ones already checked in earlier loop
                // TODO: Instead of this madness, store last_status_check time in tu_payments table and select from that
                foreach ($next_set_of_pending_payments as $next_pending_payment) {
                    if (!in_array($next_pending_payment->id, $processed_payment_ids))
                        $pending_payments[] = $next_pending_payment;
                }
            }
        } catch (Exception $e) {
            echo get_class($e).': '. $e->getMessage()."
";
        }
    }
}