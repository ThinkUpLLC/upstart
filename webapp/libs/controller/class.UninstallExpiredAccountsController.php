<?php
/**
 * Uninstall ThinkUp for expired free trials and closed accounts.
 *
 * This controller does not delete these installation's databases. It moves them to thinkupstop_username.
 */
class UninstallExpiredAccountsController extends Controller {
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

        $this->uninstallExpiredFreeTrials();
        $this->uninstallClosedAccounts();
        $this->uninstallFPSAccounts();
    }

    public function uninstallExpiredFreeTrials() {
        $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToExpiredTrial();

        while (sizeof($subscribers_to_uninstall) > 0) {
            foreach ($subscribers_to_uninstall as $subscriber) {
                $this->uninstallSubscriber($subscriber, 'Trial expired');
            }
            $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToExpiredTrial();
        }
    }

    public function uninstallClosedAccounts() {
        $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToAccountClosure();

        while (sizeof($subscribers_to_uninstall) > 0) {
            foreach ($subscribers_to_uninstall as $subscriber) {
                $this->uninstallSubscriber($subscriber, 'Closed');
            }
            $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToAccountClosure();
        }
    }

    public function uninstallFPSAccounts() {
        $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToFPSDeprecation();

        while (sizeof($subscribers_to_uninstall) > 0) {
            foreach ($subscribers_to_uninstall as $subscriber) {
                $this->uninstallSubscriber($subscriber, 'FPS');
            }
            $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToFPSDeprecation();
        }
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
}