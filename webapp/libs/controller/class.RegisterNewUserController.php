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
                            $this->addToView('network_username', '@'.$authed_twitter_user['user_name']);
                        } else {
                            $this->addErrorMessage($this->generic_error_msg);
                            $this->logError("Invalid Twitter user returned: ".
                            Utils::varDumpToString($authed_twitter_user),__FILE__,__LINE__,__METHOD__);
                        }
                    } catch (APIErrorException $e) {
                        $this->addErrorMessage($this->generic_error_msg);
                        $this->logError(get_class($e).":".$e->getMessage(),__FILE__,__LINE__,__METHOD__);
                    }
                } else {
                    $this->addErrorMessage($this->generic_error_msg);
                    $this->logError('Twitter access tokens not set '. (isset($tok)?Utils::varDumpToString($tok):''),
                    __FILE__,__LINE__,__METHOD__);
                }
            } elseif ($this->hasUserReturnedFromFacebook()) {
                if ($_GET["state"] == SessionCache::get('facebook_auth_csrf')) {
                    //Prepare API request
                    //First, prep redirect URI
                    $redirect_uri = UpstartHelper::getApplicationURL().'register.php?n=facebook';

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

                        echo "<pre>";
                        print_r($fb_user_profile);
                        echo "</pre>";
                    } else {
                        $error_msg = "Problem authorizing your Facebook account.";
                        $error_object = JSONDecoder::decode($access_token_response);
                        if (isset($error_object) && isset($error_object->error->type)
                        && isset($error_object->error->message)) {
                            $error_msg = $error_msg."<br>Facebook says: \"".$error_object->error->type.": "
                            .$error_object->error->message. "\"";
                        } else {
                            $error_msg = $error_msg."<br>Facebook's response: \"".$access_token_response. "\"";
                        }
                        $this->addErrorMessage($error_msg);
                        $this->logError( $error_msg, __FILE__,__LINE__,__METHOD__);
                    }
                } else {
                    $this->addErrorMessage($this->generic_error_msg);
                    $this->logError( "Facebook auth error: Invalid CSRF token", __FILE__,__LINE__,__METHOD__);
                }
            }
       }
       if (self::hasFormBeenPosted()) {
            $click_dao = new ClickMySQLDAO();
            $caller_reference = $click_dao->insert();
            $this->addToView('caller_reference', $caller_reference);
            SessionCache::put('caller_reference', $caller_reference);

            $selected_level = null;
            if (isset($_GET['level']) && ($_GET['level'] == "member" || $_GET['level'] == "pro")) {
                $selected_level = htmlspecialchars($_GET['level']);
                //Get Amazon URL
                $callback_url = UpstartHelper::getApplicationURL().'firstrun.php?';
                $amount = SignUpController::$subscription_levels[$selected_level];
                print_r(SignUpController::$subscription_levels);
                $subscribe_url = AmazonFPSAPIAccessor::getAmazonFPSURL($caller_reference, $callback_url, $amount);
                header('Location: '.$subscribe_url);

            } else {
                $this->addErrorMessage('TODO REWRITE THIS ERROR Error: no level chosen');
            }
       }

        $this->addToView('tz_list', UpstartHelper::getTimeZoneList());
        return $this->generateView();
    }

    /*
    TODO: Complete writing this function
     */
    protected function hasFormBeenPosted() {
        return (isset($_POST['timezone']));
    }
}