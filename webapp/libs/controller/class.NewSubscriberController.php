<?php
/**
 * Create ThinkUp account for new subscriber
 */
class NewSubscriberController extends SignUpController {
    public function control() {
        $this->setViewTemplate('pledge-new.tpl');
        $do_show_form = false;

        if ($this->hasUserPostedSignUpForm()) {
            if (self::isEmailInputValid() & self::isPasswordInputValid()) {
                $redirect_to_network = false;
                //Create new subscriber record
                $subscriber_dao = new SubscriberMySQLDAO();
                try {
                    $subscriber_id = $subscriber_dao->insert($_POST['email'], $_POST['password']);
                } catch (DuplicateSubscriberException $e) {
                    //@TODO Figure out what to do in the case of a repeat subscriber

                    //Stopgap for testing: get the subscriber ID from the DB
                    $subscriber = $subscriber_dao->getByEmail($_POST['email']);
                    $subscriber_id = $subscriber->id;
                }
                //Store subscriber ID in Session Cache
                SessionCache::put('subscriber_id', $subscriber_id);

                //Verify that authorization.token_id is in SessionCache and exists in database, show error if not
                //@TODO Handle case where session item isn't set
                $token_id = SessionCache::get('token_id');
                $authorization_dao = new AuthorizationMySQLDAO();
                $authorization = $authorization_dao->getByTokenID($token_id);
                if ($authorization == null) {
                    $this->addErrorMessage("Oops! Looks like you haven't subscribed yet. Please try again." );
                } else {
                    //Save authorization ID and subscriber ID in subscriber_authorizations table.
                    $subscriber_authorization_dao = new SubscriberAuthorizationMySQLDAO();
                    try {
                        $subscriber_authorization_dao->insert($subscriber_id, $authorization->id);
                        $redirect_to_network = true;
                    } catch (DuplicateSubscriberAuthorizationException $e) {
                        //@TODO Figure out what to do in case of repeat subscriber authorization
                        //stopgap for testing
                        $redirect_to_network = true;
                    }
                }

                if ($redirect_to_network) {
                    if ($_POST['n'] == 'twitter') {
                        //Go to Twitter
                        $cfg = Config::getInstance();
                        $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
                        $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

                        $to = new TwitterOAuth($oauth_consumer_key, $oauth_consumer_secret);
                        //Add unique waitlisted user ID from previous DB operation to callback
                        $tok = $to->getRequestToken(UpstartHelper::getApplicationURL().'new.php?n=twitter');

                        if (isset($tok['oauth_token'])) {
                            $token = $tok['oauth_token'];
                            SessionCache::put('oauth_request_token_secret', $tok['oauth_token_secret']);
                            // Build Twitter authorization URL
                            $oauthorize_link = $to->getAuthorizeURL($token);
                            //Redirect to oauthorize link
                            header('Location: '.$oauthorize_link);
                        } else {
                            $this->addErrorMessage("Oops! Something went wrong. ".
                            (isset($tok))?Utils::varDumpToString($tok):'' );
                        }
                    } elseif ($_POST['n'] == 'facebook') {
                        //Go to Facebook
                        $cfg = Config::getInstance();
                        $facebook_app_id = $cfg->getValue('facebook_app_id');
                        $facebook_api_secret = $cfg->getValue('facebook_api_secret');

                        // Create Facebook Application instance
                        $facebook_app = new Facebook(array('appId'  => $facebook_app_id,
                        'secret' => $facebook_api_secret ));

                        $fb_user = $facebook_app->getUser();
                        if ($fb_user) {
                            try {
                                $fb_user_profile = $facebook_app->api('/me');
                            } catch (FacebookApiException $e) {
                                error_log($e);
                                $fb_user = null;
                                $fb_user_profile = null;
                            }
                        }
                        //Plant unique token for CSRF protection during auth per https://developers.facebook.com/docs/authentication/
                        if (SessionCache::get('facebook_auth_csrf') == null) {
                            SessionCache::put('facebook_auth_csrf', md5(uniqid(rand(), true)));
                        }

                        $params = array('scope'=>'read_stream,user_likes,user_location,user_website,'.
                        'read_friendlists,friends_location,manage_pages,read_insights,manage_pages',
                        'state'=>SessionCache::get('facebook_auth_csrf'),
                        'redirect_uri'=>UpstartHelper::getApplicationURL().'new.php?n=facebook');

                        $fbconnect_link = $facebook_app->getLoginUrl($params);
                        //Redirect to Facebook connect link
                        header('Location: '.$fbconnect_link);
                    }
                }
            } else { // form inputs were invalid, display it again with errors
                $this->addToView('prefill_email', $_POST['email']);
                $do_show_form = true;
            }
        } elseif ($this->hasUserReturnedFromAmazon()) {
            $internal_caller_reference = SessionCache::get('caller_reference');
            if (isset($internal_caller_reference) && $this->isAmazonResponseValid($internal_caller_reference)) {
                $amazon_caller_reference = $_GET['callerReference'];
                $this->addToView('amazon_caller_reference', $amazon_caller_reference);

                $error_message = isset($_GET["errorMessage"])?$_GET["errorMessage"]:null;
                if ($error_message === null ) {
                    $this->addSuccessMessage("W00t! Thanks for subscribing to ThinkUp, you glorious member, you.");
                    $do_show_form = true;
                } else {
                    $this->addErrorMessage("Oops! Something went wrong. Amazon says: ".$error_message);
                }

                //Record authorization
                $authorization_dao = new AuthorizationMySQLDAO();
                $amount = SignUpController::$subscription_levels[$_GET['l']];
                $payment_expiry_date = (isset($_GET['expiry']))?$_GET['expiry']:null;

                try {
                    $authorization_id = $authorization_dao->insert($_GET['tokenID'], $amount, $_GET["status"],
                    $internal_caller_reference, $error_message, $payment_expiry_date);
                } catch (DuplicateAuthorizationException $e) {
                    $this->addSuccessMessage("Looks like you already paid for your ThinkUp subscription. ".
                    "   Did you refresh the page?");
                }
                //Add tokenID to cache
                SessionCache::put('token_id', $_GET['tokenID']);
            } else {
                //@TODO Link Please try again to the subscribe page
                $this->addErrorMessage("Oops! This URL is invalid. Please try again.");
            }
        } elseif ($this->hasUserReturnedFromTwitter() || $this->hasUserReturnedFromFacebook()) {
            $update_count = 0;
            $subscriber_dao = new SubscriberMySQLDAO();
            //@TODO Handle case where session item isn't set
            $subscriber_id = SessionCache::get('subscriber_id');

            if ($this->hasUserReturnedFromTwitter()) {
                $cfg = Config::getInstance();
                $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
                $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

                $request_token = $_GET['oauth_token'];
                //@TODO Handle case where session item isn't set
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
                            //                            echo "<pre>";
                            //                            print_r($authed_twitter_user);
                            //                            echo "</pre>";

                            //Update subscriber record with Twitter auth information
                            $update_count = $subscriber_dao->update($subscriber_id, $authed_twitter_user['user_name'],
                            $authed_twitter_user['user_id'], 'twitter', $authed_twitter_user['full_name'],
                            $tok['oauth_token'], $tok['oauth_token_secret'], $authed_twitter_user['is_verified'],
                            $authed_twitter_user['follower_count']);
                        } else {
                            $this->addErrorMessage("Oops! Something went wrong. Twitter didn't return a valid user.");
                        }
                    } catch (APIErrorException $e) {
                        $this->addErrorMessage("Oops! Something went wrong. Twitter says: ".$e->getMessage());
                    }
                } else {
                    $this->addErrorMessage("Oops! Something went wrong. ".
                    (isset($tok))?Utils::varDumpToString($tok):'' );
                }
            } elseif ($this->hasUserReturnedFromFacebook()) {
                if ($_GET["state"] == SessionCache::get('facebook_auth_csrf')) {
                    //Prepare API request
                    //First, prep redirect URI
                    $redirect_uri = UpstartHelper::getApplicationURL().'new.php?n=facebook';

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
                        $fb_username = $fb_user_profile['name'];
                        $fb_user_id = $fb_user_profile['id'];

                        //Update subscriber record with Facebook auth information
                        $update_count = $subscriber_dao->update($subscriber_id, $fb_user_profile['username'],
                        $fb_user_profile['id'], 'facebook', $fb_user_profile['name'], $access_token, '',
                        $fb_user_profile['verified']);
                        //                        echo "<pre>";
                        //                        print_r($fb_user_profile);
                        //                        echo "</pre>";
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
                        $this->addErrorMessage($error_msg, null, true);
                    }
                } else {
                    $this->addErrorMessage("Could not authenticate Facebook account due to invalid CSRF token.");
                }
            }

            if ($update_count == 1) {
                $this->addSuccessMessage("Alrighty then! You're subscribed to ThinkUp. ".
                "One last step: Check your email for a special link to verify your address.");

                $subscriber = $subscriber_dao->getByID($subscriber_id);

                //Send confirmation email with URL that includes verification code & address
                MandrillMailer::sendConfirmationEmail($subscriber->email, $subscriber->full_name,
                UpstartHelper::getApplicationURL().'confirm.php?usr='.urlencode($subscriber->email)."&code=".
                $subscriber->verification_code);

                //Clear SessionCache values, we're done
                SessionCache::unsetKey('subscriber_id');
                SessionCache::unsetKey('token_id');
                SessionCache::unsetKey('oauth_request_token_secret');
            } else {
                $this->addErrorMessage("Oops! Something went wrong. Couldn't save ".ucfirst($_GET['n']). " details. ".
                " Please try again.");
                $do_show_form = true;
            }

        } else { //No recognizable POST or GET vars set
            //@TODO Link Please try again to the subscribe page
            $this->addErrorMessage("Oops! Something went wrong. Please try again.");
        }

        $this->addToView('do_show_form', $do_show_form);

        return $this->generateView();
    }

    private function hasUserPostedSignUpForm() {
        return (sizeof($_POST) > 0);
    }

    private function hasUserReturnedFromFacebook() {
        return (isset($_GET['n']) && isset($_GET['code']) && isset($_GET['state']) && $_GET["n"] == 'facebook');
    }

    private function hasUserReturnedFromTwitter() {
        return (isset($_GET['n']) && isset($_GET['oauth_token']) && $_GET["n"] == 'twitter');
    }

    private function hasUserReturnedFromAmazon() {
        return (isset($_GET['callerReference'])  && isset($_GET['tokenID']) && isset($_GET["l"])
        && isset($_GET['status']) && isset($_GET['certificateUrl']) && isset($_GET['signatureMethod'])
        && isset($_GET['signature']) );
    }

    private function isAmazonResponseValid($internal_caller_reference) {
        //Check inputs match internal rules
        return ($internal_caller_reference == $_GET['callerReference']
        && (array_key_exists($_GET["l"], SignUpController::$subscription_levels))
        && $this->isAmazonSignatureValid());
    }

    private function isAmazonSignatureValid() {
        $cfg = Config::getInstance();
        $AWS_ACCESS_KEY_ID = $cfg->getValue('AWS_ACCESS_KEY_ID');
        $AWS_SECRET_ACCESS_KEY = $cfg->getValue('AWS_SECRET_ACCESS_KEY');

        $service = new Amazon_FPS_Client($AWS_ACCESS_KEY_ID, $AWS_SECRET_ACCESS_KEY);

        try {
            $endpoint_url = UpstartHelper::getApplicationURL().'new.php?l='.$_GET['l'];
            $request_params_str = '';
            foreach ($_GET as $key => $value) {
                if ($key !== "l") {
                    $request_params_str .= urlencode($key)."=".urlencode($value)."&";
                }
            }
            $request_array = array('UrlEndPoint'=>$endpoint_url, 'HttpParameters'=>$request_params_str);
            //print_r($request_array);
            $request_object = new Amazon_FPS_Model_VerifySignatureRequest($request_array);
            //            echo "<pre>";
            //            print_r($request_object);
            //            echo "</pre>";
            $response = $service->verifySignature($request_object);

            $verifySignatureResult = $response->getVerifySignatureResult();
            $result = $verifySignatureResult->getVerificationStatus();
            if ($result == 'Success') {
                return true;
            }
        } catch (Amazon_FPS_Exception $ex) {
            /*
             echo("Caught Exception: " . $ex->getMessage() . "\n");
             echo("Response Status Code: " . $ex->getStatusCode() . "\n");
             echo("Error Code: " . $ex->getErrorCode() . "\n");
             echo("Error Type: " . $ex->getErrorType() . "\n");
             echo("Request ID: " . $ex->getRequestId() . "\n");
             echo("XML: " . $ex->getXML() . "\n");
             */
        }
        return false;
    }
}