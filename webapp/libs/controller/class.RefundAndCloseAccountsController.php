<?php
/**
 * Refund members and close their ThinkUp accounts.
 */
class RefundAndCloseAccountsController extends Controller {
    /**
     * Subscriber DAO
     * @var SubscriberMySQLDAO
     */
    var $subscriber_dao;
    /**
     * App Installer
     * @var AppInstaller
     */
    var $app_installer;

    public function control() {
        $this->subscriber_dao = new SubscriberMySQLDAO();
        $this->app_installer = new AppInstaller();

        $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstall();

        //while (sizeof($subscribers_to_uninstall) > 0) {
            foreach ($subscribers_to_uninstall as $subscriber) {
                if ($subscriber->is_via_recurly) {
                    if ($this->cancelRecurlySubscription($subscriber)) {
                        //Print success message
                        echo "Successfully canceled Recurly sub for ".$subscriber->thinkup_username."...
";
                        $this->uninstallSubscriber($subscriber, 'Shutdown');
                    } else {
                        //Print failure message
                        echo "Failed to cancel Recurly sub for ".$subscriber->thinkup_username."...
";
                    }
                } else {
                    //Print "not via Recurly" message
                    echo "Not via Recurly error ".$subscriber->thinkup_username."...
";
                }
            }
            $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstall();
        //}
    }


    public function uninstallSubscriber(Subscriber $subscriber, $reason = '') {
        echo $reason.": Uninstalling ".$subscriber->thinkup_username."...
";
        try {
            $this->app_installer->uninstall($subscriber->id);
        } catch (InactiveInstallationException $e) {
            //this shouldn't happen but when/if it does, ignore and move on
        } catch (NonExistentInstallationException $e) {
            //this shouldn't happen but when/if it does, ignore and move on
        }
        $this->subscriber_dao->archiveSubscriber($subscriber->id);
        $this->subscriber_dao->deleteBySubscriberID($subscriber->id);
    }

    /**
     * Cancel and issue refund for a subscription paid via Recurly.
     * @param  Subscriber $subscriber
     * @return bool
     */
    private function cancelRecurlySubscription(Subscriber $subscriber) {
        // Required for the Recurly API
        $cfg = Config::getInstance();
        Recurly_Client::$subdomain = $cfg->getValue('recurly_subdomain');
        Recurly_Client::$apiKey = $cfg->getValue('recurly_api_key');
        try {
            try {
                $subscriptions = Recurly_SubscriptionList::getForAccount($subscriber->id);
                foreach ($subscriptions as $subscription) {
                    if ($subscription->state == 'active') {
                        $subscription->terminateAndPartialRefund();
                        // Close account
                        $subscriber_dao = new SubscriberMySQLDAO();
                        $result = $subscriber_dao->setSubscriptionStatus($subscriber->id, "Refunded");
                        $result = $subscriber_dao->closeAccount($subscriber->id);
                        // Send account closure email
                        if ($this->sendAccountClosureEmail($subscriber, null, 'Recurly')) {
                            return true;
                        }
                    }
                }
            } catch (Recurly_NotFoundError $e) {
                $subscription = Recurly_Subscription::get($subscriber->recurly_subscription_id);
                $subscription->terminateAndPartialRefund();
                // Close account
                $subscriber_dao = new SubscriberMySQLDAO();
                $result = $subscriber_dao->setSubscriptionStatus($subscriber->id, "Refunded");
                $result = $subscriber_dao->closeAccount($subscriber->id);
                // Send account closure email
                if ($this->sendAccountClosureEmail($subscriber, null, 'Recurly')) {
                    return true;
                }
            }
        } catch (Recurly_NotFoundError $e) {
            //there's no valid Recurly account or subscription
            echo ("Recurly_NotFoundError: ".$e->getMessage()."
");
            return false;
        } catch (Exception $e) {
            echo ("Exception: ".$e->getMessage()."
");
            return false;
        }
        return false;
    }
    /**
     * Send account closure email and notify Slack of account closure.
     * @param  Subscriber $subscriber
     * @param  int $refund_amount
     * @param  str $account_type
     * @return bool
     */
    private function sendAccountClosureEmail(Subscriber $subscriber, $refund_amount, $account_type = '') {
        // Send account closure email
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $template_name = "Upstart System Messages";
        $api_key = Config::getInstance()->getValue('mandrill_api_key_for_payment_reminders');

        $subject_line = "Your ThinkUp account has been closed";
        $email_view_mgr->assign('thinkup_username', $subscriber->thinkup_username );
        $refund_amount = number_format($refund_amount, 2);
        $email_view_mgr->assign('refund_amount', $refund_amount);
        $body_html = $email_view_mgr->fetch('_email.account-closed.tpl');
        $headline = "Thanks for trying ThinkUp.";
        $message = Mailer::getSystemMessageHTML($body_html, $headline);
        try {
            Mailer::mailHTMLViaMandrillTemplate($subscriber->email, $subject_line, $template_name,
                array('html_body'=>$message), $api_key);
            UpstartHelper::postToSlack('#thinkup-signups',
                $subscriber->thinkup_username.' account is closed and user has been refunded ('.$account_type.')."
                    .'\nhttps://www.thinkup.com/join/admin/subscriber.php?id='. $subscriber->id);
        } catch (Exception $e) {
            $this->addErrorMessage($e->getMessage());
            return false;
        }
        return true;
    }
}