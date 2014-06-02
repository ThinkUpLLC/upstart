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

        while (sizeof($subscribers_to_uninstall) > 0) {
            foreach ($subscribers_to_uninstall as $subscriber) {
                $app_installer->uninstall($subscriber->id);
                $subscriber_dao->archiveSubscriber($subscriber->id);
            }
            $subscribers_to_uninstall = $subscriber_dao->getSubscribersToUninstallDueToNonPayment();
        }
    }
}