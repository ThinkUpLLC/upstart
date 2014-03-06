<?php

class RegisterNewUserController extends SignUpController {
    public function control() {
        $this->setViewTemplate('register-new-user.tpl');
        if ( !self::isLevelValid() ) {
            return $this->tryAgain('Oops! Something went wrong. No level set. TODO: Rewrite Please try again.');
        }

        if ($this->hasUserReturnedFromTwitter() || $this->hasUserReturnedFromFacebook()) {
            if ($this->hasUserReturnedFromTwitter()) {
                $cfg = Config::getInstance();
                $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
                $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

                $request_token = $_GET['oauth_token'];
                $request_token_secret = SessionCache::get('oauth_request_token_secret');
                $to = new TwitterOAuth($oauth_consumer_key, $oauth_consumer_secret, $request_token,
                $request_token_secret);

                if (isset($_GET['oauth_verifier'])) {
                    $tok = $to->getAccessToken($_GET['oauth_verifier']);
                } else {
                    $tok = null;
                }

                if (isset($tok['oauth_token']) && isset($tok['oauth_token_secret'])) {
                    $api = new TwitterAPIAccessorOAuth($tok['oauth_token'], $tok['oauth_token_secret'],
                    $oauth_consumer_key, $oauth_consumer_secret, 5,  false);

                    try {
                        $authed_twitter_user = $api->verifyCredentials();
                        if (isset($authed_twitter_user['user_name'])) {
                            //@TODO If Twitter user exists in subscribers table, tryAgain with error
                            $this->addToView('network_username', '@'.$authed_twitter_user['user_name']);

                            $network_auth_details = array(
                                'network_user_name'=>$authed_twitter_user['user_name'],
                                'network_user_id'=>$authed_twitter_user['user_id'],
                                'network'=>'twitter',
                                'full_name'=>$authed_twitter_user['full_name'],
                                'oauth_access_token'=>$tok['oauth_token'],
                                'oauth_access_token_secret'=>$tok['oauth_token_secret'],
                                'is_verified'=>$authed_twitter_user['is_verified'],
                                'follower_count'=>$authed_twitter_user['follower_count']
                            );
                            SessionCache::put('network_auth_details', serialize($network_auth_details));
                        } else {
                            return $this->tryAgain("Invalid Twitter user returned: ".
                            Utils::varDumpToString($authed_twitter_user),__FILE__,__LINE__,__METHOD__);
                        }
                    } catch (APIErrorException $e) {
                        return $this->tryAgain(get_class($e).":".$e->getMessage(),__FILE__,__LINE__,__METHOD__);
                    }
                } else {
                    return $this->tryAgain('Twitter access tokens not set '.
                        (isset($tok)?Utils::varDumpToString($tok):''), __FILE__,__LINE__,__METHOD__);
                }
            } elseif ($this->hasUserReturnedFromFacebook()) {
                if ($_GET["state"] == SessionCache::get('facebook_auth_csrf')) {
                    //Prepare API request
                    //First, prep redirect URI
                    $redirect_uri = urlencode(UpstartHelper::getApplicationURL().'register.php?n=facebook&level='
                        .$_GET['level']);

                    $cfg = Config::getInstance();
                    $facebook_app_id = $cfg->getValue('facebook_app_id');
                    $facebook_api_secret = $cfg->getValue('facebook_api_secret');

                    $facebook_app = new Facebook(array('appId'=>$facebook_app_id, 'secret' => $facebook_api_secret ));

                    //Build API request URL
                    $api_req = 'https://graph.facebook.com/oauth/access_token?client_id='. $facebook_app_id.
                    '&client_secret='. $facebook_api_secret. '&redirect_uri='.$redirect_uri.'&state='.
                    SessionCache::get('facebook_auth_csrf').'&code='.$_GET["code"];

                    $access_token_response = FacebookGraphAPIAccessor::rawApiRequest($api_req, false);
                    parse_str($access_token_response);
                    if (isset($access_token)) {
                        $facebook_app->setAccessToken($access_token);
                        $fb_user_profile = $facebook_app->api('/me');

                        // echo "<pre>";
                        // print_r($fb_user_profile);
                        // echo "</pre>";

                        //@TODO If Facebook user exists in subscribers table, tryAgain with error
                        $this->addToView('email', $fb_user_profile['email']);
                        $this->addToView('network_username', $fb_user_profile['name']);

                        $network_auth_details = array(
                            'network_user_name'=>$fb_user_profile['username'],
                            'network_user_id'=>$fb_user_profile['id'],
                            'network'=>'facebook',
                            'full_name'=>$fb_user_profile['name'],
                            'oauth_access_token'=>$access_token,
                            'oauth_access_token_secret'=>'',
                            'is_verified'=>0,
                            'follower_count'=>0
                        );
                        SessionCache::put('network_auth_details', serialize($network_auth_details));
                    } else {
                        $error_msg = "Problem authorizing your Facebook account. ";
                        $error_object = JSONDecoder::decode($access_token_response);
                        if (isset($error_object) && isset($error_object->error->type)
                        && isset($error_object->error->message)) {
                            $error_msg = $error_msg." Facebook says: \"".$error_object->error->type.": "
                            .$error_object->error->message. "\"";
                        } else {
                            $error_msg = $error_msg." Facebook's response: \"".$access_token_response. "\"";
                        }
                        return $this->tryAgain( $error_msg ." ". __FILE__. " ".__LINE__. " ".__METHOD__);
                    }
                } else {
                    return $this->tryAgain("Facebook auth error: Invalid CSRF token ". __FILE__. " ".__LINE__
                        . " ".__METHOD__);
                }
            }
        }

       if (self::hasFormBeenPosted()) {
            // Validate email, password, and username
            if (self::isEmailInputValid() & self::isPasswordInputValid() & self::isUsernameValid()
                & self::isTimeZoneValid() & self::hasAgreedToTerms()) {
                // Make sure SessionCache has 'network_auth_details', if not tryAgain()
                $network_auth_details = SessionCache::get('network_auth_details');
                if (isset($network_auth_details) ) {
                    $unserialized_network_auth_details = unserialize($network_auth_details);
                    if ($unserialized_network_auth_details !== false) {
                        print_r($unserialized_network_auth_details);
                        $has_user_been_created = false;
                        //Build subscriber object
                        $subscriber = new Subscriber();
                        $subscriber->email = $_POST['email'];
                        $subscriber->pwd = $_POST['password'];
                        $subscriber->network_user_name = $unserialized_network_auth_details['network_user_name'];
                        $subscriber->full_name = $unserialized_network_auth_details['full_name'];
                        $subscriber->oauth_access_token = $unserialized_network_auth_details['oauth_access_token'];
                        $subscriber->oauth_access_token_secret = $unserialized_network_auth_details['oauth_access_token_secret'];
                        $subscriber->is_verified = $unserialized_network_auth_details['is_verified'];
                        $subscriber->membership_level = ucfirst($_GET['level']);
                        $subscriber->timezone = $_POST['timezone'];
                        $subscriber->thinkup_username = $_POST['username'];

                        //Insert subscriber
                        $subscriber_dao = new SubscriberMySQLDAO();
                        try {
                            $new_subscriber_id = $subscriber_dao->insertCompleteSubscriber($subscriber);
                            $has_user_been_created = true;
                            SessionCache::put('new_subscriber_id', $new_subscriber_id);
                        } catch (DuplicateSubscriberEmailException $e) {
                            $this->addErrorMessage('That email address is already subscribed to ThinkUp. '.
                                'Please try again.');
                            $this->addToView('username', $_POST['username']);
                            $this->addToView('current_tz', $_POST['timezone']);
                            $this->addToView('password', $_POST['password']);
                            return $this->generateView();
                        } catch (DuplicateSubscriberUsernameException $e) {
                            $this->addErrorMessage('That username is already in use. Please try again.');
                            $this->addToView('email', $_POST['email']);
                            $this->addToView('current_tz', $_POST['timezone']);
                            $this->addToView('password', $_POST['password']);
                            return $this->generateView();
                        }

                        if ($has_user_been_created) {
                            //Begin Amazon redirect
                            $click_dao = new ClickMySQLDAO();
                            $caller_reference = $click_dao->insert();
                            SessionCache::put('caller_reference', $caller_reference);

                            $selected_level = null;
                            if (isset($_GET['level']) && ($_GET['level'] == "member" || $_GET['level'] == "pro")) {
                                $selected_level = htmlspecialchars($_GET['level']);
                                //Get Amazon URL
                                $callback_url = UpstartHelper::getApplicationURL().'welcome.php?level='.$_GET['level'];
                                $amount = SignUpController::$subscription_levels[$selected_level];
                                $pay_with_amazon_url = AmazonFPSAPIAccessor::getAmazonFPSURL($caller_reference,
                                    $callback_url, $amount);
                                header('Location: '.$pay_with_amazon_url);
                            } else {
                                return $this->tryAgain('Oops! Something went wrong. Please try again.');
                            }
                        }
                    } else {
                        return $this->tryAgain('Oops! Something went wrong. Please try again.');
                    }
                } else {
                    return $this->tryAgain("Network auth details not set; please try again TODO: Rewrite this");
                }
            } else { //Populate form with the submitted values
                $this->addToView('email', $_POST['email']);
                $this->addToView('username', $_POST['username']);
                $this->addToView('current_tz', $_POST['timezone']);
                $this->addToView('password', $_POST['password']);
                if (isset($_POST['terms'])) {
                    $this->addToView('terms', $_POST['terms']);
                }
            }
        }
        $this->addToView('tz_list', UpstartHelper::getTimeZoneList());
        return $this->generateView();
    }

    /**
     * Check whether or not all registration form values have been posted.
     * @return bool
     */
    protected function hasFormBeenPosted() {
        return (isset($_POST['timezone']) && isset($_POST['username']) && isset($_POST['password'])
            && isset($_POST['email']));
    }

    /**
     * Send user back to subcribe page with error message in session.
     * @param str Error message
     * @return str
     */
    private function tryAgain($error_message = '') {
        //$this->addErrorMessage($error_message);
        SessionCache::put('auth_error_message', $error_message);
        $controller = new SubscribeController(true);
        return $controller->go();
    }

    /**
     * Check if valid level is set in URL.
     * @return bool
     */
    private function isLevelValid() {
        if (isset($_GET["level"]) && ($_GET['level'] == 'member' || $_GET['level'] == 'pro' )) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verify time zone and add appropriate error message if not
     * return bool
     */
    private function isTimeZoneValid() {
        if (isset($_POST['timezone']) && empty($_POST['timezone'])) {
            $this->addErrorMessage('Please enter a timezone.', 'timezone');
        }
        // Validate timezone
        $is_timezone_valid = false;
        if (isset($_POST['timezone']) && !empty($_POST['timezone'])) {
            $possible_timezones = timezone_identifiers_list();
            if (in_array($_POST['timezone'], $possible_timezones)) {
                $is_timezone_valid = true;
            }
            if (!$is_timezone_valid) {
                $this->addErrorMessage('Time zone must be chosen from this list.', 'timezone');
            }
        }
        return (isset($_POST['timezone']) && $is_timezone_valid);
    }

    /**
     * Verify user has agreed to TOS and add appropriate error message if not
     * return bool
     */
    private function hasAgreedToTerms() {
        if (isset($_POST['terms']) && $_POST['terms'] === 'agreed') {
            return true;
        } else {
            $this->addErrorMessage("Please agree to ThinkUp's terms of service.", 'terms');
            return false;
        }
    }
}