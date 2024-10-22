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
            try {
                $subscriber = $subscriber_dao->getByID($subscriber_id);
                $subscriber->creation_time_friendly = date('M jS Y', strtotime($subscriber->creation_time));
                $this->addToView('application_url', UpstartHelper::getApplicationURL());

                $subscriber_payment_dao = new SubscriberPaymentMySQLDAO();

                //Get authorizations and assign to view
                $subscriber_auth_dao = new SubscriberAuthorizationMySQLDAO();
                $authorizations = $subscriber_auth_dao->getBySubscriberID($subscriber_id);
                $this->addToView('authorizations', $authorizations);

                //Get subscription operations and assign to view
                $sub_op_dao = new SubscriptionOperationMySQLDAO();
                $subscription_operations = $sub_op_dao->getBySubscriberID($subscriber_id);
                $this->addToView('subscription_operations', $subscription_operations);

                if (isset($subscriber->claim_code)) {
                    $claim_code_dao = new ClaimCodeMySQLDAO();
                    $claim_code = $claim_code_dao->getWithOperationDetails($subscriber->claim_code);
                    $this->addToView('claim_code', $claim_code);
                }

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
                    } elseif ($_GET['action'] == 'syncrecurly') {
                        if (isset($_GET['recurly_sub_id'])) {
                            $cfg = Config::getInstance();
                            Recurly_Client::$subdomain = $cfg->getValue('recurly_subdomain');
                            Recurly_Client::$apiKey = $cfg->getValue('recurly_api_key');

                            try {
                                $subscription = Recurly_Subscription::get($_GET['recurly_sub_id']);

                                if (strpos($subscription->plan->plan_code, 'monthly') !== false) {
                                    $subscriber->subscription_recurrence = '1 month';
                                } elseif (strpos($subscription->plan->plan_code, 'yearly') !== false) {
                                    $subscriber->subscription_recurrence = '12 months';
                                }
                                $subscriber->paid_through =
                                    $subscription->current_period_ends_at->format('Y-m-d H:i:s');

                                //@TODO Handle other subscription states here
                                $subscriber->subscription_status = 'Paid';

                                $subscriber_dao->setSubscriptionDetails($subscriber);

                                $this->addSuccessMessage("Updated to ".$subscriber->subscription_recurrence
                                    .' recurrence, paid through '. $subscriber->paid_through);
                            } catch (Exception $e) {
                                $this->addErrorMessage($e->getMessage());
                            }
                        } else {
                            $this->addErrorMessage("No subscription Id specified");
                        }
                    } elseif ($_GET['action'] == 'setrecurlysubid') {
                        if (isset($_GET['recurly_subscription_id']) && $_GET['recurly_subscription_id'] != '') {
                            $recurly_sub_id = $_GET['recurly_subscription_id'];
                            $subscriber_dao->setRecurlySubscriptionID($subscriber_id, $recurly_sub_id);
                            $this->addSuccessMessage("Saved Recurly subscription.");
                            $subscriber = $subscriber_dao->getByID($subscriber_id);
                        } else {
                            $this->addErrorMessage("No Recurly subscription ID specified");
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
                    } elseif ($_GET['action'] == 'setmembershiplevel') {
                        if (isset($_GET['level']) && $_GET['level'] != '' &&
                            ($_GET['level'] == 'Member' || $_GET['level'] == 'Pro')) {
                            $level = $_GET['level'];

                            $subscriber_dao->setMembershipLevel($subscriber_id, $level);
                            //Change level in TU installation
                            $tu_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
                            if ($tu_dao->updateOwnerMembershipLevel( $subscriber->email, $level)) {
                                $this->addSuccessMessage("Saved new membership level $level.");
                            } else {
                                $this->addErrorMessage("Changed membership level in Upstart but not in ThinkUp. ".
                                "To resolve, check $subscriber->thinkup_username's database manually.");
                            }
                            $subscriber = $subscriber_dao->getByID($subscriber_id);
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
                            $subscriber->is_membership_complimentary = true;
                            //Update is_free_trial field in ThinkUp installation
                            $tu_tables_dao = new ThinkUpTablesMySQLDAO($subscriber->thinkup_username);
                            $trial_ended = $tu_tables_dao->endFreeTrial($subscriber->email);
                            $subscriber_dao->updateSubscriberSubscriptionDetails($subscriber);
                            $this->addSuccessMessage("Comped membership for ".$subscriber->email);
                            $subscriber->is_membership_complimentary = true;
                        }
                    } elseif ($_GET['action'] == 'decomp') {
                        $decomped = $subscriber_dao->decompSubscription($subscriber_id, $username);
                        if ( $decomped > 0 ) {
                            $subscriber_dao->updateSubscriberSubscriptionDetails($subscriber);
                            $this->addSuccessMessage("Decomped membership for ".$subscriber->email);
                            $subscriber->is_membership_complimentary = false;
                        }
                    } elseif ($_GET['action'] == 'due') {
                        $is_payment_due = $subscriber_dao->setSubscriptionStatus($subscriber_id, 'Payment due');
                        if ( $is_payment_due > 0 ) {
                            $this->addSuccessMessage("Set status to Payment due for ".$subscriber->email);
                            $subscriber->subscription_status = 'Payment due';
                        }
                    } elseif ($_GET['action'] == 'dispatch') {
                        $cfg = Config::getInstance();
                        $jobs_array = array();
                        // json_encode them
                        $jobs_array[] = array(
                            'installation_name'=>$subscriber->thinkup_username,
                            'timezone'=>$cfg->getValue('dispatch_timezone'),
                            'db_host'=>$cfg->getValue('tu_db_host'),
                            'db_name'=>$cfg->getValue('user_installation_db_prefix').$subscriber->thinkup_username,
                            'db_socket'=>$cfg->getValue('tu_db_socket'),
                            'db_port'=>$cfg->getValue('tu_db_port')
                        );
                        // call Dispatcher
                        $result_decoded = Dispatcher::dispatch($jobs_array);
                        if (!isset($result_decoded->success)) {
                            $this->addErrorMessage("There was a problem with API call ".$api_call.". The result was ".
                                $result);
                        } else {
                            $subscriber_dao->updateLastDispatchedTime($subscriber->id);
                            $this->addSuccessMessage('Successfully dispatched crawl.');
                            $subscriber = $subscriber_dao->getByID($subscriber_id);
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
                if (!isset($subscriber->thinkup_username)) {
                    $subscriber->subdomainified_username = self::subdomainify($subscriber->network_user_name);
                }
                $subscriber->paid_through_friendly = date('M jS Y', strtotime($subscriber->paid_through));
                $this->addToView('subscriber', $subscriber);

                //Set annual payer paid flag
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

                //Set next charge amount if this is a manual FPS annual payer
                if (isset($payments)) {
                    $subscription_helper = new SubscriptionHelper();
                    $next_annual_charge_amount =
                        $subscription_helper->getNextAnnualChargeAmount($subscriber->membership_level);
                    $this->addToView('next_annual_charge_amount', $next_annual_charge_amount);
                }

                $install_log_dao = new InstallLogMySQLDAO();
                $install_log_entries = $install_log_dao->getLogEntriesBySubscriber($subscriber_id);
                $this->addToView('install_log_entries', $install_log_entries);

                $cfg = Config::getInstance();
                $is_in_sandbox = $cfg->getValue('amazon_sandbox');
                $this->addToView('is_in_sandbox', $is_in_sandbox);
            } catch (SubscriberDoesNotExistException $e) {
                $this->addErrorMessage($e->getMessage());
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
