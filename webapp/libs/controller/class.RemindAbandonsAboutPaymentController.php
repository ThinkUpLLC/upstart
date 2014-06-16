<?php

/**
 * This controller sends email reminders to ThinkUp members who abandoned payment.
 * Eventually, it will be deprecated by free trial.
 *
 * For first reminder, select all subscribers who are not on the waitlist, whose subscription_status is 'Payment due','
 * 'whose total_payment_reminders_sent = 0, and whose creation_time is more than 24 hours earlier than now.
 * Cycle through them and send first reminder, setting total_payment_reminders_sent = 1 along the way.
 *
 * For the second reminder, select all subscribers who are not on the waitlist, whose subscription_status is
 * 'Payment due', whose total_payment_reminders_sent = 1, and whose last_payment_reminder_sent is more than 48 hours
 * earlier than now. Cycle through them and send second reminder, setting total_payment_reminders_sent = 2 along
 * the way.
 *
 * For the final reminder, select all subscribers who are not on the waitlist, whose subscription_status is
 * 'Payment due', whose total_payment_reminders_sent = 2, and whose last_payment_reminder_sent is more than 96 hours
 * earlier than now. Cycle through them and send third reminder, setting total_payment_reminders_sent = 3 along the way.
 */
class RemindAbandonsAboutPaymentController extends Controller {
    public function control() {
        $subscriber_dao = new SubscriberMySQLDAO();
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $template_name = "Upstart System Messages";
        $api_key = Config::getInstance()->getValue('mandrill_api_key_for_payment_reminders');

        //Send first payment reminder 24 hours after signup time
        $subscribers = $subscriber_dao->getSubscribersDueReminder(0, 24);
        $subject_line = "Hey, did you forget something?";
        $headline = "Lock in your ThinkUp membership";
        foreach ($subscribers as $subscriber) {
            $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
            $body_html = $email_view_mgr->fetch('_email.payment-reminder-abandon-1.tpl');
            $message = Mailer::getSystemMessageHTML($body_html, $headline);
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name, 
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 1);
        }

        //Send second payment reminder 48 hours after first reminder
        $subscribers = $subscriber_dao->getSubscribersDueReminder(1, 48);
        $subject_line = "Don’t forget to finalize your ThinkUp membership";
        $headline = "One quick step needed to keep your ThinkUp account";
        foreach ($subscribers as $subscriber) {
            $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
            $body_html = $email_view_mgr->fetch('_email.payment-reminder-abandon-2.tpl');
            $message = Mailer::getSystemMessageHTML($body_html, $headline);
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name, 
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 2);
        }

        //Send final payment reminder 96 hours after second reminder
        $subscribers = $subscriber_dao->getSubscribersDueReminder(2, 96);
        $headline = "We don’t want to say goodbye so soon!";
        foreach ($subscribers as $subscriber) {
            $subject_line = "Last chance to keep ".$subscriber->thinkup_username.".thinkup.com!";
            $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
            $body_html = $email_view_mgr->fetch('_email.payment-reminder-abandon-3.tpl');
            $message = Mailer::getSystemMessageHTML($body_html, $headline);
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name, 
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 3);
        }
    }
}