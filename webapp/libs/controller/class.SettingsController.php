<?php
class SettingsController extends UpstartAuthController {
    /*
     * @var array Options for notification frequency
     */
    var $notification_frequencies = array('daily'=>'Daily','weekly'=>'Weekly', 'never'=>'Never');

    public function authControl() {
        $this->disableCaching();
        $this->enableCSRFToken();
        $this->setPageTitle('Settings');
        $this->setViewTemplate('user.settings.tpl');

        $logged_in_user = Session::getLoggedInUser();
        $this->addToView('logged_in_user', $logged_in_user);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail($logged_in_user);
        $this->addToView('subscriber', $subscriber);

        // get owner object
        $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
        $owner_dao = new OwnerMySQLDAO($subscriber->thinkup_username);

        //process submitted edits
        if (isset($_POST['Done'])) {
            $this->validateCSRFToken();
            $new_tz = isset($_POST['timezone']) ? $_POST['timezone'] : null;
            $updates = 0;
            if (isset($new_tz)) {
                $possible_timezones = timezone_identifiers_list();
                if (in_array($new_tz, $possible_timezones)) {
                    $updates += $owner_dao->setTimezone($logged_in_user, $new_tz);
                }
            }

            $new_email_frequency = isset($_POST['control-notification-frequency'])
                ? $_POST['control-notification-frequency'] : null;
            if (isset($new_email_frequency)) {
                if (in_array($new_email_frequency, array_keys($this->notification_frequencies))) {
                    $updates += $owner_dao->setEmailNotificationFrequency( $logged_in_user, $new_email_frequency);
                }
            }

            // Only update if they supply the old password
            if (!empty($_POST['current_password'])) {
                $current_pwd = $_POST['current_password'];
                $new_pwd1 = $_POST['new_password1'];
                $new_pwd2 = $_POST['new_password2'];
                if (!$subscriber_dao->isAuthorized($logged_in_user, $current_pwd)) {
                    $this->addErrorMessage("Oops! Your current password doesn't look right.");
                } else if ($new_pwd1 !== $new_pwd2) {
                    $this->addErrorMessage('Oops! Your new passwords did not match.');
                } else if (!UpstartHelper::validatePassword($new_pwd1)) {
                    $this->addErrorMessage('Oops! Your new password must be at least 8 characters '.
                        'and contain both numbers and letters.');
                } else {
                    // We are good to update.  Update both the Upstart and ThinkUp password
                    $subscriber_dao->updatePassword($logged_in_user, $new_pwd1);
                    $updates += $owner_dao->updatePassword($logged_in_user, $new_pwd1);
                }
            }

            if ($updates > 0) {
                $this->addSuccessMessage('Saved your changes.');
            }
        }
        $owner = $owner_dao->getByEmail($logged_in_user);
        $this->addToView('owner', $owner);
        $this->addToView('current_tz', $owner->timezone);
        $this->addToView('notification_options', $this->notification_frequencies);

        $instances = $tu_tables_dao->getInstancesWithStatus($subscriber->thinkup_username, 2);

        //Start off assuming connection doesn't exist
        $connection_status = array('facebook'=>'inactive', 'twitter'=>'inactive', 'instagram'=>'inactive');
        foreach ($instances as $instance) {
            if ($instance['auth_error'] != '') {
                $connection_status[$instance['network']] = 'error';
            } else { //connection exists, so it's active
                $connection_status[$instance['network']] = 'active';
            }
        }
        $this->addToView('facebook_connection_status', $connection_status['facebook']);
        $this->addToView('twitter_connection_status', $connection_status['twitter']);
        $this->addToView('instagram_connection_status', $connection_status['instagram']);

        $config = Config::getInstance();
        $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
        $config->getValue('user_installation_url'));
        $this->addToView('thinkup_url', $user_installation_url);

        $this->addToView('tz_list', UpstartHelper::getTimeZoneList());
        return $this->generateView();
	}
}
