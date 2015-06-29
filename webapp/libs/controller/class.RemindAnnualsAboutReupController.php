<?php

/**
 * This controller sends transition-to-Recurly marketing emails to annual FPS ThinkUp members
 * who have a payment due in 2 days, 7 days past, and 21 days past.
 *
 * Two reminders:
 * 1. 2 days before payment is due
 * 2. 7 days after payment is due
 * 3. 21 days after payment is due
 *
 */
class RemindAnnualsAboutReupController extends Controller {
    public function control() {
        $subscriber_dao = new SubscriberMySQLDAO();
        $payment_dao = new PaymentMySQLDAO();
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $template_name = "Upstart System Messages";
        $cfg = Config::getInstance();
        $api_key = $cfg->getValue('mandrill_api_key_for_payment_reminders');
        $email_view_mgr->assign('site_url', UpstartHelper::getApplicationURL(false, false, false) );

        //Send first reup reminder 2 days before paid_through date
        $subscribers = $subscriber_dao->getAnnualSubscribersDueReupReminder(2, 1);
        foreach ($subscribers as $subscriber) {
            //Determine membership level
            $membership_level = $subscriber->membership_level;
            if ($subscriber->membership_level == 'Early Bird' || $subscriber->membership_level == 'Late Bird') {
                $is_getting_discount =  false;
                $subject_line = "Time to renew your ThinkUp membership";
                $membership_level = 'Member';
            } else {
                $is_getting_discount =  true;
                $subject_line = "Renew your ThinkUp subscription and get 2 months free";
            }
            //Only notify Member and Pro levels who have an installation
            $notify_this_subscriber = false;
            if (isset($subscriber->thinkup_username) && ($membership_level == 'Member' || $membership_level == 'Pro')){
                $notify_this_subscriber = true;
            }

            if ($notify_this_subscriber) {
                $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                    $cfg->getValue('user_installation_url'));
                $email_view_mgr->assign('thinkup_url', $user_installation_url);
                $email_view_mgr->assign('member_level', $membership_level );
                $email_view_mgr->assign('is_getting_discount', $is_getting_discount );

                try {
                    $earliest_payment = $payment_dao->getSubscribersEarliestPayment($subscriber->id);
                    $original_subscription_date = date('F jS, Y', strtotime( $earliest_payment->timestamp ) );
                    $email_view_mgr->assign('original_subscription_date', $original_subscription_date );

                    $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-1.tpl');
                    $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
                    Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                        array('html_body'=>$message), $api_key);
                } catch (PaymentDoesNotExistException $e) {
                    //Do nothing
                }

                //Set reminders sent so this isn't sent again
                $subscriber_dao->setTotalReupRemindersSent( $subscriber->id, 1);
                //Set subscription status to Payment due
                $subscriber->subscription_status = 'Payment due';
                $subscriber_dao->setSubscriptionDetails( $subscriber );
            }
        }

        //Send second reup reminder 7 days after paid_through date
        $subscribers = $subscriber_dao->getAnnualSubscribersDueReupReminder(-7, 2);

        $subject_line = "Action required: Update your ThinkUp payment info";
        foreach ($subscribers as $subscriber) {
            //Determine membership level
            $membership_level = $subscriber->membership_level;
            if ($subscriber->membership_level == 'Early Bird' || $subscriber->membership_level == 'Late Bird') {
                $is_getting_discount =  false;
                $membership_level = 'Member';
            } else {
                $is_getting_discount =  true;
            }
            //Only notify Member and Pro levels who have an installation
            $notify_this_subscriber = false;
            if (isset($subscriber->thinkup_username) && ($membership_level == 'Member' || $membership_level == 'Pro')){
                $notify_this_subscriber = true;
            }

            if ($notify_this_subscriber) {
                $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                    $cfg->getValue('user_installation_url'));
                $email_view_mgr->assign('thinkup_url', $user_installation_url);
                $email_view_mgr->assign('member_level', $membership_level );
                $email_view_mgr->assign('is_getting_discount', $is_getting_discount );

                $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-2.tpl');
                $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
                Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                    array('html_body'=>$message), $api_key);

                //Set reminders sent so this isn't sent again
                $subscriber_dao->setTotalReupRemindersSent( $subscriber->id, 2);
                //Set subscription status to Payment due
                $subscriber->subscription_status = 'Payment due';
                $subscriber_dao->setSubscriptionDetails( $subscriber );
            }
        }

        //Send third reup reminder 21 days after paid_through date
        $subscribers = $subscriber_dao->getAnnualSubscribersDueReupReminder(-21, 3);
        $subject_line = "LAST CHANCE to save your ThinkUp account";
        foreach ($subscribers as $subscriber) {
            //Determine membership level
            $membership_level = $subscriber->membership_level;
            if ($subscriber->membership_level == 'Early Bird' || $subscriber->membership_level == 'Late Bird') {
                $is_getting_discount =  false;
                $membership_level = 'Member';
            } else {
                $is_getting_discount =  true;
            }
            //Only notify Member and Pro levels who have an installation
            $notify_this_subscriber = false;
            if (isset($subscriber->thinkup_username) && ($membership_level == 'Member' || $membership_level == 'Pro')){
                $notify_this_subscriber = true;
            }

            if ($notify_this_subscriber) {
                $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                    $cfg->getValue('user_installation_url'));
                $email_view_mgr->assign('thinkup_url', $user_installation_url);
                $email_view_mgr->assign('member_level', $membership_level );
                $email_view_mgr->assign('is_getting_discount', $is_getting_discount );

                $body_html = $email_view_mgr->fetch('_email.fps-transition-reminder-3.tpl');
                $message = Mailer::getSystemMessageHTML($body_html, $subject_line);
                Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                    array('html_body'=>$message), $api_key);

                //Set reminders sent so this isn't sent again
                $subscriber_dao->setTotalReupRemindersSent( $subscriber->id, 3);
                //Set subscription status to Payment due
                $subscriber->subscription_status = 'Payment due';
                $subscriber_dao->setSubscriptionDetails( $subscriber );
            }
        }
    }
}