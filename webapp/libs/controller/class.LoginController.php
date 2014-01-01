<?php
class LoginController extends UpstartController {

    public function control() {
        $this->setPageTitle('Log in');
        $this->setViewTemplate('user.login.tpl');
        $this->disableCaching();

        if (isset($_GET['msg'])) {
            $this->addSuccessMessage($_GET['msg']);
        }
        // if already logged in, show settings screen
        if ( Session::isLoggedIn()) {
            $settings_controller = new SettingsController(true);
            return $settings_controller->go();
        } else  {
            $subscriber_dao = new SubscriberMySQLDAO();

            if (isset($_POST['Submit']) && $_POST['Submit']=='Log In' && isset($_POST['email']) &&
            isset($_POST['pwd']) ) {
                if ( $_POST['email']=='' || $_POST['pwd']=='') {
                    if ( $_POST['email']=='') {
                        $this->addErrorMessage("Email must not be empty");
                        return $this->generateView();
                    } else {
                        $this->addErrorMessage("Password must not be empty");
                        return $this->generateView();
                    }
                } else {
                    $user_email = $_POST['email'];
                    if (get_magic_quotes_gpc()) {
                        $user_email = stripslashes($user_email);
                    }
                    $this->addToView('email', $user_email);
                    $subscriber = $subscriber_dao->getByEmail($user_email);
                    if (!$subscriber) {
                        $this->addErrorMessage("Incorrect email");
                        return $this->generateView();
                    } elseif (!$subscriber->is_email_verified) {
                        $error_msg = 'Inactive account. ';
                        if ($subscriber->failed_logins == 0) {
                            $error_msg .= 'You must activate your account.';
                        } elseif ($subscriber->failed_logins == 10) {
                            $error_msg .= $subscriber->account_status .
                            '. <a href="forgot.php">Reset your password.</a>';
                        }
                        $disable_xss = true;
                        $this->addErrorMessage($error_msg, null, $disable_xss);
                        return $this->generateView();
                        // If the credentials supplied by the user are incorrect
                    } elseif (!$subscriber_dao->isAuthorized($user_email, $_POST['pwd']) ) {
                        $error_msg = 'Incorrect password';
                        if ($subscriber->failed_logins >= 9) { // where 9 represents the 10th attempt!
                            $subscriber_dao->deactivateSubscriber($user_email);
                            $status = 'Account deactivated due to too many failed logins';
                            $subscriber_dao->setAccountStatus($user_email, $status);
                            $error_msg = 'Inactive account. ' . $status .
                            '. <a href="forgot.php">Reset your password.</a>';
                        }

                        $subscriber_dao->incrementFailedLogins($user_email);
                        $disable_xss = true;
                        $this->addErrorMessage($error_msg, null, $disable_xss);
                        return $this->generateView();
                    } else {
                        // user has logged in sucessfully this sets variables in the session
                        $session = new Session();
                        $session->completeLogin($subscriber);

                        $subscriber_dao->updateLastLogin($subscriber->email);
                        $subscriber_dao->resetFailedLogins($subscriber->email);
                        $subscriber_dao->clearAccountStatus($subscriber->email);

                        // Get the username and private API key
                        $logged_in_user = Session::getLoggedInUser();
                        $this->addToView('logged_in_user', $logged_in_user);
                        $subscriber = $subscriber_dao->getByEmail($logged_in_user);

                        if (isset($subscriber->thinkup_username) && isset($subscriber->date_installed)
                            && $subscriber->is_installation_active) {
                            $config = Config::getInstance();
                            $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                            $config->getValue('user_installation_url'));
                            $upstart_url = UpstartHelper::getApplicationURL() . $config->getValue('site_root_path');

                            $params = array("u"=>$logged_in_user, "k"=>$subscriber->api_key_private,
                            'success_redir'=> $user_installation_url,
                            'failure_redir'=> $upstart_url . '');

                            $url = $user_installation_url.'api/v1/session/login.php?';
                            end($params);
                            $last_param = key($params);
                            foreach ($params as $key=>$value) {
                                $url .= $key ."=" . urlencode($value);
                                if ($key != $last_param) {
                                    $url .= "&";
                                }
                            }
                            // Redirect to installation to log in
                            if (!$this->redirect($url)) {
                                $this->generateView(); //for testing
                            }
                        } else {
                            // No installation, show settings screen
                            $settings_controller = new SettingsController(true);
                            return $settings_controller->go();
                        }
                    }
                }
            } else  {
                return $this->generateView();
            }
        }
    }
}
