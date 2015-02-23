<?php
class LoginController extends Controller {

    public function control() {
        $this->setPageTitle('Log in');
        $this->setViewTemplate('user.login.tpl');
        $this->disableCaching();

        if (isset($_GET['msg'])) {
            $this->addSuccessMessage($_GET['msg']);
        }
        // if already logged in, show settings or username picker screen
        $subscriber_dao = new SubscriberMySQLDAO();
        if ( Session::isLoggedIn()) {
            $subscriber = $subscriber_dao->getByEmail(Session::getLoggedInUser() );
            if (isset($subscriber->thinkup_username) && isset($subscriber->date_installed)
                && $subscriber->is_installation_active) {
                $controller = new SettingsController(true);
            } else {
                $controller = new ChooseUsernameController(true);
            }
            return $controller->go();
        } else  {
            // If usr verification parameter is on the query string, add it to the view
            if (isset($_GET['usr'])) {
                $this->addToView('usr', rawurlencode($_GET['usr']));
            }
            // Set successful login redirect destination
            if (isset($_GET['redirect'])) {
                $this->addToView('redirect', $_GET['redirect']);
            }
            // If form has been submitted
            if (isset($_POST['redirect'])) {
                $this->addToView('redirect', $_POST['redirect']);
            }

            if (isset($_POST['Submit']) && $_POST['Submit']=='Log In' && isset($_POST['email']) &&
            isset($_POST['pwd']) ) {
                if ( $_POST['email']=='' || $_POST['pwd']=='') {
                    if ( $_POST['email']=='') {
                        $this->addErrorMessage("You'll need to enter an email address.");
                        return $this->generateView();
                    } else {
                        $this->addErrorMessage("You'll need a password.");
                        return $this->generateView();
                    }
                } else {
                    $user_email = $_POST['email'];
                    if (get_magic_quotes_gpc()) {
                        $user_email = stripslashes($user_email);
                    }
                    $this->addToView('email', $user_email);
                    try {
                        $subscriber = $subscriber_dao->getByEmail($user_email);
                    } catch (SubscriberDoesNotExistException $e) {
                        $this->addErrorMessage("Sorry, can't find that email.");
                        return $this->generateView();
                    }

                    // Attempt to quietly verify the email address if it's not already
                    if (isset($subscriber) && !$subscriber->is_email_verified) {
                        if (isset($_GET['usr'])) {
                            // account for annoying +/space problem
                            $email_to_verify =  str_replace(' ', '+', $_GET['usr']);
                            if (isset($_GET['code'])  && $email_to_verify == $_POST['email']) {
                                $verification_code = $subscriber_dao->getVerificationCode($email_to_verify);
                                if ($_GET['code'] == $verification_code['verification_code']) {
                                    $verified = $subscriber_dao->verifyEmailAddress($email_to_verify);
                                    if ($verified > 0) {
                                        $subscriber->is_email_verified = true;
                                    }
                                }
                            }
                        }
                    }
                    //@TODO Properly implement is_activated check here with failed login cap
                    // } elseif (!$subscriber->is_activated) {
                    //     $error_msg = 'Inactive account. ';
                    //     if ($subscriber->failed_logins == 0) {
                    //         $error_msg .= 'Please confirm your email address before logging into ThinkUp.';
                    //     } elseif ($subscriber->failed_logins == 10) {
                    //         $error_msg .= $subscriber->account_status .
                    //         '. <a href="forgot.php">Reset your password.</a>';
                    //     }
                    //     $disable_xss = true;
                    //     $this->addErrorMessage($error_msg, null, $disable_xss);
                    //     return $this->generateView();
                    // If the credentials supplied by the user are incorrect
                    if (!$subscriber_dao->isAuthorized($user_email, $_POST['pwd']) ) {
                        $error_msg = 'That password doesn\'t seem right.';
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
                        Session::completeLogin($subscriber->email);

                        $subscriber_dao->updateLastLogin($subscriber->email);
                        $subscriber_dao->resetFailedLogins($subscriber->email);
                        $subscriber_dao->clearAccountStatus($subscriber->email);

                        // Get the username and private API key
                        $logged_in_user = Session::getLoggedInUser();
                        $this->addToView('logged_in_user', $logged_in_user);
                        $subscriber = $subscriber_dao->getByEmail($logged_in_user);

                        if (isset($subscriber->thinkup_username) && isset($subscriber->date_installed)
                            && $subscriber->is_installation_active ) {
                            $config = Config::getInstance();
                            $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
                            $config->getValue('user_installation_url'));
                            $upstart_url = UpstartHelper::getApplicationURL() . $config->getValue('site_root_path');

                            if (isset($_POST['redirect']) && $_POST['redirect'] != '') {
                                $success_redir = $_POST['redirect'];
                            } else {
                                $success_redir = $user_installation_url;
                            }

                            $params = array("u"=>$logged_in_user, "k"=>$subscriber->api_key_private,
                            'success_redir'=> $success_redir,
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
                            // Installation doesn't exist yet, can't show Settings
                            $controller = new ChooseUsernameController(true);
                            return $controller->go();
                        }
                    }
                }
            } else  {
                return $this->generateView();
            }
        }
    }
}
