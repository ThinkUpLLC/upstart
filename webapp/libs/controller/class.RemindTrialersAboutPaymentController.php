<?php

/**
 * This controller sends payment reminders via email to ThinkUp members who are currently in free trial mode.
 *
 * Four payment reminders:
 * 1. Day 1 of trial
 * 2. Day 7 of trial
 * 3. Day 13 of trial
 * 4. Day 14 of trial
 */
class RemindTrialersAboutPaymentController extends Controller {
    public function control() {
        $subscriber_dao = new SubscriberMySQLDAO();
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $template_name = "Upstart System Messages";
        $api_key = Config::getInstance()->getValue('mandrill_api_key_for_payment_reminders');

        //Send first payment reminder 24 hours after signup time (day 1)
        $subscribers = $subscriber_dao->getSubscribersFreeTrialPaymentReminder(0, 24);
        $subject_line = "Ready to join ThinkUp and get your FREE gift?";
        $headline = "Loving ThinkUp? Time to join!";
        foreach ($subscribers as $subscriber) {
            $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
            $body_html = $email_view_mgr->fetch('_email.payment-reminder-trial-1.tpl');
            $message = Mailer::getSystemMessageHTML($body_html, $headline);
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 1);
        }

        //Send second payment reminder 144 hours (6 days) after 1st reminder (day 7)
        $subscribers = $subscriber_dao->getSubscribersFreeTrialPaymentReminder(1, 144);
        $subject_line = "Enjoying ThinkUp? Join and get a FREE book...";
        $headline = "You've tried ThinkUp for a week...";
        foreach ($subscribers as $subscriber) {
            $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
            $body_html = $email_view_mgr->fetch('_email.payment-reminder-trial-2.tpl');
            $message = Mailer::getSystemMessageHTML($body_html, $headline);
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 2);
        }

        //Send third payment reminder 144 hours (6 days) after 2nd reminder (day 13)
        $subscribers = $subscriber_dao->getSubscribersFreeTrialPaymentReminder(2, 144);
        $subject_line = "Your ThinkUp trial has almost expired!";
        $headline = "Only one day left to join ThinkUp!";
        foreach ($subscribers as $subscriber) {
            $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
            $body_html = $email_view_mgr->fetch('_email.payment-reminder-trial-3.tpl');
            $message = Mailer::getSystemMessageHTML($body_html, $headline);
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 3);
        }

        //Send fourth and final payment reminder 24 hours (1 day) after 3rd reminder (day 14)
        $subscribers = $subscriber_dao->getSubscribersFreeTrialPaymentReminder(3, 24);
        $subject_line = "FINAL REMINDER: Don't lose your ThinkUp membership!";
        $headline = "Action Required: Your ThinkUp trial is ending";
        foreach ($subscribers as $subscriber) {
            $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
            $body_html = $email_view_mgr->fetch('_email.payment-reminder-trial-4.tpl');
            $message = Mailer::getSystemMessageHTML($body_html, $headline);
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 4);
        }
    }
}