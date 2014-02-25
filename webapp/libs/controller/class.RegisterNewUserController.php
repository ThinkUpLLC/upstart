<?php

class RegisterNewUserController extends SignUpController {
    public function control() {
        $this->setViewTemplate('register-new-user.tpl');
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
                            SessionCache::put('auth_error_message', $this->generic_error_msg);
                            $this->logError("Invalid Twitter user returned: ".
                            Utils::varDumpToString($authed_twitter_user),__FILE__,__LINE__,__METHOD__);
                            return $this->tryAgain();
                        }
                    } catch (APIErrorException $e) {
                        SessionCache::put('auth_error_message', $this->generic_error_msg);
                        $this->logError(get_class($e).":".$e->getMessage(),__FILE__,__LINE__,__METHOD__);
                        return $this->tryAgain();
                    }
                } else {
                    SessionCache::put('auth_error_message', $this->generic_error_msg);
                    $this->logError('Twitter access tokens not set '. (isset($tok)?Utils::varDumpToString($tok):''),
                    __FILE__,__LINE__,__METHOD__);
                    return $this->tryAgain();
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
                        SessionCache::put('auth_error_message', $error_msg);
                        $this->logError( $error_msg, __FILE__,__LINE__,__METHOD__);
                        return $this->tryAgain();
                    }
                } else {
                    SessionCache::put('auth_error_message', $this->generic_error_msg);
                    $this->logError( "Facebook auth error: Invalid CSRF token", __FILE__,__LINE__,__METHOD__);
                    return $this->tryAgain();
                }
            }
       }
       if (self::hasFormBeenPosted()) {
            //@TODO Validate email
            //@TODO Validate timezone
            //@TODO Validate ThinkUp username
            //@TODO Make sure SessionCache has 'network_auth_details', if not tryAgain()

            //@TODO Insert subscriber details here
            //@TODO Catch duplicate subscriber exception
            //@TODO Catch duplicate username exception

            //Begin Amazon redirect
            $click_dao = new ClickMySQLDAO();
            $caller_reference = $click_dao->insert();
            SessionCache::put('caller_reference', $caller_reference);

            $selected_level = null;
            if (isset($_GET['level']) && ($_GET['level'] == "member" || $_GET['level'] == "pro")) {
                $selected_level = htmlspecialchars($_GET['level']);
                //Get Amazon URL
                $callback_url = UpstartHelper::getApplicationURL().'welcome.php?';
                $amount = SignUpController::$subscription_levels[$selected_level];
                $pay_with_amazon_url = AmazonFPSAPIAccessor::getAmazonFPSURL($caller_reference, $callback_url, $amount);
                header('Location: '.$pay_with_amazon_url);
            } else {
                SessionCache::put('auth_error_message', 'Oops! Something went wrong. Please try again.');
                return $this->tryAgain();
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
     * @return str
     */
    private function tryAgain() {
        $controller = new SubscribeController(true);
        return $controller->go();
    }
}