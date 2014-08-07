<?php
/**
 * Uninstall ThinkUp for all members who:
 *
 * * Signed up for the free trial and never paid 15 days since signup and 30 hours since last dispatch_time
 *
 * This controller doesn't uninstall closed accounts because those will get uninstalled when the trial expires.
 * Since closed accounts don't get crawled or reminded about payment, the user isn't getting any communication from
 * ThinkUp even though the installation still exists. The only downside is that the username will be claimed for the
 * course of the trial, even if the user closes the account on day 2.
 *
 * This controller does not delete these installation's databases. It moves them to thinkupstop_username.
 */
class UninstallOverduePaymentsController extends Controller {
    public function control() {
        $subscriber_dao = new SubscriberMySQLDAO();
        $app_installer = new AppInstaller();

        $subscribers_to_uninstall = $subscriber_dao->getSubscribersToUninstallDueToExpiredTrial();

        while (sizeof($subscribers_to_uninstall) > 0) {
            foreach ($subscribers_to_uninstall as $subscriber) {
                echo "Uninstalling ".$subscriber->thinkup_username."...
";
                try {
                    $app_installer->uninstall($subscriber->id);
                } catch (InactiveInstallationException $e) {
                    //this shouldn't happen but when/if it does, ignore and move on
                } catch (NonExistentInstallationException $e) {
                    //this shouldn't happen but when/if it does, ignore and move on
                }
                $subscriber_dao->archiveSubscriber($subscriber->id);
                $subscriber_dao->deleteBySubscriberID($subscriber->id);
            }
            $subscribers_to_uninstall = $subscriber_dao->getSubscribersToUninstallDueToExpiredTrial();
        }
     }
}