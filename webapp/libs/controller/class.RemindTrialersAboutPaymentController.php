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
        $cfg = Config::getInstance();
        $api_key = $cfg->getValue('mandrill_api_key_for_payment_reminders');
        $email_view_mgr->assign('site_url', UpstartHelper::getApplicationURL(false, false, false) );

        //Send first payment reminder 48 hours after signup time (day 2)
        $subscribers = $subscriber_dao->getSubscribersFreeTrialPaymentReminder(0, 48);
        $subject_line = "Join ThinkUp and get your FREE gift!";
        foreach ($subscribers as $subscriber) {
            $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                $cfg->getValue('user_installation_url'));
            $email_view_mgr->assign('thinkup_url', $user_installation_url);
            $email_view_mgr->assign('membership_level', $subscriber->membership_level);
            $message = $email_view_mgr->fetch('_email.payment-reminder-trial-1.tpl');
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 1);
        }

        //Send second payment reminder 120 hours (5 days) after 1st reminder (day 7)
        $subscribers = $subscriber_dao->getSubscribersFreeTrialPaymentReminder(1, 120);
        $subject_line = "Enjoying ThinkUp? Join and get even more...";
        foreach ($subscribers as $subscriber) {
            $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                $cfg->getValue('user_installation_url'));
            $email_view_mgr->assign('thinkup_url', $user_installation_url);
            $email_view_mgr->assign('membership_level', $subscriber->membership_level);
            $message = $email_view_mgr->fetch('_email.payment-reminder-trial-2.tpl');
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 2);
        }

        //Send third payment reminder 144 hours (6 days) after 2nd reminder (day 13)
        $subscribers = $subscriber_dao->getSubscribersFreeTrialPaymentReminder(2, 144);
        $subject_line = "One day left: Ready to join ThinkUp?";
        foreach ($subscribers as $subscriber) {
            $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                $cfg->getValue('user_installation_url'));
            $email_view_mgr->assign('thinkup_url', $user_installation_url);
            $email_view_mgr->assign('membership_level', $subscriber->membership_level);
            $message = $email_view_mgr->fetch('_email.payment-reminder-trial-3.tpl');
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 3);
        }

        //Send fourth and final payment reminder 24 hours (1 day) after 3rd reminder (day 14)
        $subscribers = $subscriber_dao->getSubscribersFreeTrialPaymentReminder(3, 24);
        $subject_line = "Your ThinkUp free trial ends TODAY. Join now!";
        foreach ($subscribers as $subscriber) {
            $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                $cfg->getValue('user_installation_url'));
            $email_view_mgr->assign('thinkup_url', $user_installation_url);
            $email_view_mgr->assign('membership_level', $subscriber->membership_level);
            $message = $email_view_mgr->fetch('_email.payment-reminder-trial-4.tpl');
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalPaymentRemindersSent( $subscriber->id, 4);
        }
    }
}