<?php
/**
 * Uninstall ThinkUp for all members who:
 *
 * * abandoned payment
 * * received 3 payment reminder emails over the course of 14 days
 * * never paid, 14 days from the 3rd and final payment reminder
 *
 * This controller does not delete these installation's databases. It moves them to thinkupstop_username.
 */
class UninstallOverduePaymentsController extends Controller {
    public function control() {
        $subscriber_dao = new SubscriberMySQLDAO();
        $app_installer = new AppInstaller();

        $subscribers_to_uninstall = $subscriber_dao->getSubscribersToUninstallDueToNonPayment();

        if (sizeof($subscribers_to_uninstall) == 0) {
            echo "No overdue memberships to uninstall.
";
        }
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
            $subscribers_to_uninstall = $subscriber_dao->getSubscribersToUninstallDueToNonPayment();
        }
    }
}