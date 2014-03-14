<?php

class RegisterNewUserController extends SignUpHelperController {
    public function control() {
        $this->setViewTemplate('register.tpl');
        //Avoid "unable to write file [really long file]" errors
        $this->disableCaching();
        if ( !self::isLevelValid() ) {
            return $this->tryAgain($this->generic_error_msg, "Invalid level", __FILE__, __METHOD__, __LINE__);
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
                            //If Twitter user exists in subscribers table, tryAgain with error
                            $subscriber_dao = new SubscriberMySQLDAO();
                            if ($subscriber_dao->doesSubscriberConnectionExist($authed_twitter_user['user_id'],
                                'twitter')) {
                                $user_error = "Whoa! We love your enthusiasm, but @".
                                    $authed_twitter_user['user_name'] . " on Twitter has already joined ThinkUp. ".
                                    "Please connect another Facebook or Twitter account.";
                                $tech_error = "Twitter user exists ". Utils::varDumpToString($authed_twitter_user);
                                return $this->tryAgain( $user_error, $tech_error, __FILE__, __METHOD__, __LINE__ );
                            }

                            $potential_username = strtolower($authed_twitter_user['user_name']);
                            if (UpstartHelper::isUsernameValid($potential_username)
                                && !$subscriber_dao->isUsernameTaken($potential_username)) {
                                $this->addToView('username', $potential_username);
                            }

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
                            $user_error = "Hmm, we got a technical error from Twitter: ".
                                Utils::varDumpToString($authed_twitter_user).
                                " Please try again, or contact us at help@thinkup.com if you're stuck.";
                            return $this->tryAgain($user_error, $user_error, __FILE__, __METHOD__, __LINE__);

                        }
                    } catch (APIErrorException $e) {
                        $user_error = "Hmm, we got a technical error from Twitter: ".
                            get_class($e)." - ".$e->getMessage().
                            " Please try again, or contact us at help@thinkup.com if you're stuck.";
                        return $this->tryAgain($user_error, $user_error, __FILE__, __METHOD__, __LINE__);
                    }
                } else {
                    $user_error = "Hmm, we got a technical error from Twitter: ".
                        'the access tokens were not set. '.
                        (isset($tok)?Utils::varDumpToString($tok):'').
                        " Please try again, or contact us at help@thinkup.com if you're stuck.";
                    $tech_error = 'Access tokens not set. '. (isset($tok)?Utils::varDumpToString($tok):'');
                    return $this->tryAgain($user_error, $tech_error, __FILE__, __METHOD__, __LINE__);
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

                        //If Facebook user exists in subscribers table, tryAgain with error
                        if (!isset($subscriber_dao)) {
                            $subscriber_dao = new SubscriberMySQLDAO();
                        }
                        if ($subscriber_dao->doesSubscriberConnectionExist($fb_user_profile['id'], 'facebook')) {
                            $user_error =  "Whoa! We love your enthusiasm, but ".
                                $fb_user_profile['name'] . " on Facebook has already joined ThinkUp. ".
                                "If you have an account, try logging in.";
                            $tech_error = "Facebook user exists ". Utils::varDumpToString($fb_user_profile);
                            return $this->tryAgain($user_error, $tech_error, __FILE__, __METHOD__, __LINE__);
                        }

                        if (!$subscriber_dao->doesSubscriberEmailExist($fb_user_profile['email'])) {
                            $this->addToView('email', $fb_user_profile['email']);
                        }

                        if (isset($fb_user_profile['username'])) {
                            $potential_username = strtolower($fb_user_profile['username']);
                            if (UpstartHelper::isUsernameValid($potential_username)
                                && !$subscriber_dao->isUsernameTaken($potential_username)) {
                                $this->addToView('username', $potential_username);
                            }
                        }

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
                        $error_msg = "Uh oh, we got a technical error from Facebook when connecting to your account.  ";
                        $error_object = JSONDecoder::decode($access_token_response);
                        if (isset($error_object) && isset($error_object->error->type)
                            && isset($error_object->error->message)) {
                            $error_msg = $error_msg .$error_object->error->type.": " .$error_object->error->message." ";
                        } else {
                            $error_msg = $error_msg . " access token ".$access_token_response. " ";
                        }
                        $error_msg = $error_msg ."Please try again, or contact us at help@thinkup.com if you're stuck.";
                        $tech_error_msg = "Access token response parse_str failed: ". $access_token_response;
                        return $this->tryAgain($error_msg, $tech_error_msg, __FILE__, __METHOD__, __LINE__);
                    }
                } else {
                    $user_error = "Uh oh! We got a technical error from Facebook about an invalid CSRF ".
                        "token. That's not your fault, so contact us at help@thinkup.com if you're stuck.";
                    $tech_error = "Invalid Facebook CSRF token, GET['state'] != Session facebook_auth_csrf";
                    return $this->tryAgain($user_error, $tech_error, __FILE__, __METHOD__, __LINE__);
                }
            }
        }

       if (self::hasFormBeenPosted()) {
            // Validate email, password, and username
            if (self::isEmailInputValid() & self::isPasswordInputValid() & self::isUsernameValid()
                & self::isTimeZoneValid() & self::hasAgreedToTerms()) {
                // Make sure SessionCache has 'network_auth_details', if not tryAgain()
                $network_auth_details = $this->getCachedAuthedUserDetails();
                if (isset($network_auth_details) ) {
                    $has_user_been_created = false;
                    //Build subscriber object
                    $subscriber = new Subscriber();
                    $subscriber->email = $_POST['email'];
                    $subscriber->pwd = $_POST['password'];
                    $subscriber->network_user_name = $network_auth_details['network_user_name'];
                    $subscriber->network_user_id = $network_auth_details['network_user_id'];
                    $subscriber->network = $network_auth_details['network'];
                    $subscriber->full_name = $network_auth_details['full_name'];
                    $subscriber->oauth_access_token = $network_auth_details['oauth_access_token'];
                    $subscriber->oauth_access_token_secret = $network_auth_details['oauth_access_token_secret'];
                    $subscriber->is_verified = $network_auth_details['is_verified'];
                    $subscriber->membership_level = ucfirst($_GET['level']);
                    $subscriber->timezone = $_POST['timezone'];
                    $subscriber->thinkup_username = $_POST['username'];
                    $subscriber->follower_count = $network_auth_details['follower_count'];

                    //Insert subscriber
                    if (!isset($subscriber_dao)) {
                        $subscriber_dao = new SubscriberMySQLDAO();
                    }
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
                        $this->addToView('tz_list', UpstartHelper::getTimeZoneList());
                        return $this->generateView();
                    } catch (DuplicateSubscriberUsernameException $e) {
                        $this->addErrorMessage('That username is already in use. Please try again.');
                        $this->addToView('email', $_POST['email']);
                        $this->addToView('current_tz', $_POST['timezone']);
                        $this->addToView('password', $_POST['password']);
                        $this->addToView('tz_list', UpstartHelper::getTimeZoneList());
                        return $this->generateView();
                    } catch (DuplicateSubscriberConnectionException $e) {
                        $user_error =  "Whoa! We love your enthusiasm, but ".
                            $subscriber->network_user_name . " on " . $subscriber->network .
                            " has already joined ThinkUp.  Please connect another Facebook or Twitter account.";
                        $tech_error = "DuplicateSubscriberConnectionException ". Utils::varDumpToString($subscriber);
                        return $this->tryAgain($user_error, $tech_error, __FILE__, __METHOD__, __LINE__);
                    }

                    if ($has_user_been_created) {
                        $installer = new AppInstaller();
                        $install_results = $installer->install($new_subscriber_id);
                        //Begin Amazon redirect
                        $caller_reference = $new_subscriber_id.'_'.time();

                        $selected_level = htmlspecialchars($_GET['level']);
                        //Get Amazon URL
                        $callback_url = UpstartHelper::getApplicationURL().'welcome.php?level='.$_GET['level'];
                        $amount = SignUpHelperController::$subscription_levels[$selected_level];
                        $pay_with_amazon_url = AmazonFPSAPIAccessor::getAmazonFPSURL($caller_reference,
                            $callback_url, $amount);
                        header('Location: '.$pay_with_amazon_url);
                    } else {
                        return $this->tryAgain($this->generic_error_msg);
                    }
                } else {
                    return $this->tryAgain($this->generic_error_msg, "SubscriberDAO insertion failed", __FILE__, 
                        __METHOD__, __LINE__);
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

        $authed_user = $this->getCachedAuthedUserDetails();
        if (isset($authed_user) && isset($authed_user['network_user_name']) && isset($authed_user['network'])) {
            if ($authed_user['network'] == 'twitter') {
                $this->addToView('network_username', '@'.$authed_user['network_user_name']);
            } else {
                $this->addToView('network_username', $authed_user['full_name']);
            }
            $this->addToView('network', $authed_user['network']);
        }

        $this->addToView('tz_list', UpstartHelper::getTimeZoneList());
        return $this->generateView();
    }


    /**
     * Get the cached social network user details from Session, and unserialize them.
     * @return array Details array
     */
    private function getCachedAuthedUserDetails() {
        $authed_user = SessionCache::get('network_auth_details');
        try {
            $authed_user_array = Serializer::unserializeString($authed_user);
            return $authed_user_array;
        } catch (SerializerException $e) {
            return null;
        }
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
     * @param str $user_error_message User-facing error message
     * @param str $technical_error_message Technical internal error message
     * @param str $file Filename of calling script
     * @param str $method Method name where error occurred
     * @param str $line Line where error occurred
     * @return str
     */
    private function tryAgain($user_error_message, $technical_error_message, $file, $method, $line) {
        $this->logError($technical_error_message, $file, $line, $method);
        SessionCache::put('auth_error_message', $user_error_message);
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