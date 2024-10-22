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
        $this->uninstallDelinquentAccounts();
        $this->uninstallExpiredComplimentaryAccounts();
        $this->subscriber_dao->setPaymentDue();
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

    public function uninstallExpiredComplimentaryAccounts() {
        $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToComplimentaryAccount();

        while (sizeof($subscribers_to_uninstall) > 0) {
            foreach ($subscribers_to_uninstall as $subscriber) {
                $this->uninstallSubscriber($subscriber, 'Complimentary account');
            }
            $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToComplimentaryAccount();
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

    public function uninstallDelinquentAccounts() {
        //Failed payments
        $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToFailedPayment();

        while (sizeof($subscribers_to_uninstall) > 0) {
            foreach ($subscribers_to_uninstall as $subscriber) {
                $this->uninstallSubscriber($subscriber, 'Failed payment for over 120 days');
            }
            $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToFailedPayment();
        }

        //Overdue annual reups (from FPS transition)
        $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToOverdueReup();

        while (sizeof($subscribers_to_uninstall) > 0) {
            foreach ($subscribers_to_uninstall as $subscriber) {
                $this->uninstallSubscriber($subscriber, 'Last reup reminder over 30 days ago');
            }
            $subscribers_to_uninstall = $this->subscriber_dao->getSubscribersToUninstallDueToOverdueReup();
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