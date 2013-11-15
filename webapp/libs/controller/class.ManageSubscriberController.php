<?php
/**
 * Show subscriber information and offer actions to modify/manage.
 * @author gina
 */
class ManageSubscriberController extends Controller {
    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('admin-subscriber.tpl');

        $subscriber_id = (isset($_GET['id']))?(integer)$_GET['id']:false;
        if ($subscriber_id !== false ) {
            //Get subscriber and assign to view
            $subscriber_dao = new SubscriberMySQLDAO();
            $subscriber = $subscriber_dao->getByID($subscriber_id);
            $this->addToView('application_url', UpstartHelper::getApplicationURL());

            if (isset($subscriber)) {
                //Get authorizations and assign to view
                $subscriber_auth_dao = new SubscriberAuthorizationMySQLDAO();
                $authorizations = $subscriber_auth_dao->getBySubscriberID($subscriber_id);
                $this->addToView('authorizations', $authorizations);

                //If action specified, perform it
                if (isset($_GET['action'])) {
                    if ($_GET['action'] == 'archive') {
                        $result = $this->archiveSubscriber($subscriber_id);
                        if ($result) {
                            $this->addSuccessMessage("Subscriber archived.");
                            $subscriber = null;
                        } else {
                            $this->addErrorMessage("Subscriber does not exist.");
                        }
                    } elseif ($_GET['action'] == 'setusername') {
                        if (isset($_GET['username'])) {
                            $username = self::subdomainify($_GET['username']);
                            if ( $subscriber_dao->setUsername($subscriber_id, $username) ) {
                                $this->addSuccessMessage("Saved username $username.");
                                $subscriber = $subscriber_dao->getByID($subscriber_id);
                            }
                        } else {
                            $this->addErrorMessage("No username specified");
                        }
                    } elseif ($_GET['action'] == 'install') {
                        $installer = new AppInstaller();
                        try {
                            $install_results = $installer->install($subscriber_id, true);
                            $this->addSuccessMessage("Installation complete!");
                            $this->addToView('install_results', $install_results);
                            $subscriber = $subscriber_dao->getByID($subscriber_id);
                        } catch (Exception $e) {
                            $this->addErrorMessage("Could not install ThinkUp. ".$e->getMessage());
                        }
                    }
                }

                //Set installation URL
                if (isset($subscriber->thinkup_username) && isset($subscriber->date_installed) ) {
                    $cfg = Config::getInstance();
                    $user_installation_url = $cfg->getValue('user_installation_url');
                    $subscriber->installation_url = str_replace ("{user}", $subscriber->thinkup_username,
                    $user_installation_url);
                }
                if (isset($subscriber) && !isset($subscriber->thinkup_username)) {
                    $subscriber->subdomainified_username = self::subdomainify($subscriber->network_user_name);
                }
                $this->addToView('subscriber', $subscriber);
            } else {
                $this->addErrorMessage("Subscriber does not exist.");
            }
        } else {
            $this->addErrorMessage("No subscriber specified.");
        }
        return $this->generateView();
    }

    /**
     * Archive subscriber and auth to subscribers_archived table, then delete auth, sub_auth, and subscriber.
     * @param int $subscriber_id
     * @return boolean Whether or not subscriber was archived
     */
    private function archiveSubscriber($subscriber_id) {
        $result = 0;
        $subscriber_dao = new SubscriberMySQLDAO();
        $result += $subscriber_dao->archiveSubscriber($subscriber_id);

        //Delete auth
        $auth_dao = new AuthorizationMySQLDAO();
        $result += $auth_dao->deleteBySubscriberID($subscriber_id);

        //Delete sub_auth
        $subscriber_auth_dao = new SubscriberAuthorizationMySQLDAO();
        $result += $subscriber_auth_dao->deleteBySubscriberID($subscriber_id);

        //Delete subscriber
        $result += $subscriber_dao->deleteBySubscriberID($subscriber_id);
        return ($result > 0);
    }

    /**
     * Convert username to valid characters for subdomains, ie, remove capital letters and special characters.
     * @param str $username
     * @return str $username
     */
    protected function subdomainify($username) {
        $username = strtolower($username);
        $username = preg_replace("/[^a-zA-Z0-9\s]/", "", $username);
        if ($username == '') {
            $unique = uniqid();
            $username .= substr($unique, strlen($unique)-4, strlen($unique));
        }
        return $username;
    }
}