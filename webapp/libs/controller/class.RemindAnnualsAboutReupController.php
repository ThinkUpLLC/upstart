<?php

/**
 * This controller sends payment reminders via email to annual ThinkUp members who have a payment due in a week and
 * in 2 weeks.
 *
 * Two reup reminders:
 * 1. 2 weeks before payment is due
 * 2. 1 week before payment is due
 */
class RemindAnnualsAboutReupController extends Controller {
    public function control() {
        $subscriber_dao = new SubscriberMySQLDAO();
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $template_name = "Upstart System Messages";
        $cfg = Config::getInstance();
        $api_key = $cfg->getValue('mandrill_api_key_for_payment_reminders');
        $email_view_mgr->assign('site_url', UpstartHelper::getApplicationURL(false, false, false) );

        //Send first reup reminder 2 weeks before paid_through date
        $subscribers = $subscriber_dao->getAnnualSubscribersDueReupReminder(14, 1);
        $subject_line = "Your first year of ThinkUp";
        foreach ($subscribers as $subscriber) {
            $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                $cfg->getValue('user_installation_url'));
            $email_view_mgr->assign('thinkup_url', $user_installation_url);
            $body_html = $email_view_mgr->fetch('_email.annual-reup-notification-1.tpl');
            $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalReupRemindersSent( $subscriber->id, 1);
        }

        //Send second reup reminder 1 week before paid_through date
        $subscribers = $subscriber_dao->getAnnualSubscribersDueReupReminder(7, 2);
        $subject_line = "Your ThinkUp membership is about to renew";
        foreach ($subscribers as $subscriber) {
            $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                $cfg->getValue('user_installation_url'));
            $email_view_mgr->assign('thinkup_url', $user_installation_url);
            $body_html = $email_view_mgr->fetch('_email.annual-reup-notification-2.tpl');
            $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            $subscriber_dao->setTotalReupRemindersSent( $subscriber->id, 2);
        }
    }
}