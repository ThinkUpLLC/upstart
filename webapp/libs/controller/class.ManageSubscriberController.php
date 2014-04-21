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

            $subscriber_payment_dao = new SubscriberPaymentMySQLDAO();

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
                    } elseif ($_GET['action'] == 'setemail') {
                        if (isset($_GET['email']) && $_GET['email'] != '') {
                            $email = $_GET['email'];
                            //Validate email address
                            if (UpstartHelper::validateEmail( $email ) ) {
                                try {
                                    $subscriber_dao->setEmail($subscriber_id, $email);
                                    //Change email in TU installation
                                    $tu_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
                                    if ($tu_dao->updateOwnerEmail( $subscriber->email, $email)) {
                                        $this->addSuccessMessage("Saved email $email.");
                                    } else {
                                        $this->addErrorMessage("Changed email in Upstart but not in ThinkUp. ".
                                        "To resolve, check $subscriber->thinkup_username's database manually.");
                                    }
                                    $subscriber = $subscriber_dao->getByID($subscriber_id);
                                } catch (DuplicateSubscriberEmailException $e) {
                                    $this->addErrorMessage("$email is already in use by another subscriber.");
                                }
                            } else {
                                $this->addErrorMessage("$email is not a valid email address");
                            }
                        } else {
                            $this->addErrorMessage("No email specified");
                        }
                    } elseif ($_GET['action'] == 'install') {
                        $installer = new AppInstaller();
                        try {
                            $install_results = $installer->install($subscriber_id);
                            $this->addSuccessMessage("Installation complete!");
                            $this->addToView('install_results', $install_results);
                            $subscriber = $subscriber_dao->getByID($subscriber_id);
                        } catch (Exception $e) {
                            $this->addErrorMessage("Could not install ThinkUp. ".$e->getMessage());
                        }
                    } elseif ($_GET['action'] == 'uninstall') {
                        $installer = new AppInstaller();
                        try {
                            $uninstall_results = $installer->uninstall($subscriber_id);
                            $this->addSuccessMessage("Uninstallation complete!");
                            $this->addToView('install_results', $uninstall_results);
                            $subscriber = $subscriber_dao->getByID($subscriber_id);
                        } catch (Exception $e) {
                            $this->addErrorMessage("Could not uninstall ThinkUp. ".$e->getMessage());
                        }
                    } elseif ($_GET['action'] == 'comp') {
                        $comped = $subscriber_dao->compSubscription($subscriber_id, $username);
                        if ( $comped > 0 ) {
                            $subscriber_dao->updateSubscriptionStatus($subscriber_id);
                            $this->addSuccessMessage("Comped membership for ".$subscriber->email);
                            $subscriber->is_membership_complimentary = true;
                        }
                    } elseif ($_GET['action'] == 'decomp') {
                        $decomped = $subscriber_dao->decompSubscription($subscriber_id, $username);
                        if ( $decomped > 0 ) {
                            $subscriber_dao->updateSubscriptionStatus($subscriber_id);
                            $this->addSuccessMessage("Decomped membership for ".$subscriber->email);
                            $subscriber->is_membership_complimentary = false;
                        }
                    } elseif ($_GET['action'] == 'charge') {
                        if (isset($_GET['token_id']) && isset($_GET['amount'])) {
                            $fps_api_accessor = new AmazonFPSAPIAccessor();
                            $ok = $fps_api_accessor->invokeAmazonPayAction($subscriber_id, $_GET['token_id'],
                            $_GET['amount']);
                            if ($ok) {
                                $this->addSuccessMessage("Payment successful!");
                            } else {
                                $this->addErrorMessage("Payment failed!");
                            }
                        }  else {
                            $this->addErrorMessage("No token and/or amount specified");
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

                $payments = $subscriber_payment_dao->getBySubscriber($subscriber_id);
                $this->addToView('payments', $payments);
                $paid = false;
                foreach ($payments as $p) {
                    if (empty($p['error_message']) && strtotime($p['timestamp']) > (time() - (60*60*24*365))) {
                        $paid = true;
                        break;
                    }
                }
                $this->addToView('paid', $paid);

                $install_log_dao = new InstallLogMySQLDAO();
                $install_log_entries = $install_log_dao->getLogEntriesBySubscriber($subscriber_id);
                $this->addToView('install_log_entries', $install_log_entries);

                $cfg = Config::getInstance();
                $is_in_sandbox = $cfg->getValue('amazon_sandbox');
                $this->addToView('is_in_sandbox', $is_in_sandbox);
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
