<?php
/**
 * Create ThinkUp account for new subscriber
 */
class NewSubscriberController extends SignUpController {
    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('new.tpl');

        $do_show_form = false;

        if ($this->hasUserPostedSignUpForm()) {
            if (self::isEmailInputValid() & self::isPasswordInputValid()) {
                $redirect_to_network = false;
                //Create new subscriber record
                $subscriber_dao = new SubscriberMySQLDAO();
                try {
                    $subscriber_id = $subscriber_dao->insert($_POST['email'], $_POST['password']);
                    //Store subscriber ID in Session Cache
                    SessionCache::put('subscriber_id', $subscriber_id);

                    //Verify that authorization.token_id is in SessionCache and exists in database, show error if not
                    $token_id = SessionCache::get('token_id');
                    $authorization_dao = new AuthorizationMySQLDAO();
                    $authorization = $authorization_dao->getByTokenID($token_id);
                    if ($authorization == null) {
                        $this->addErrorMessage($this->generic_error_msg);
                        $this->logError('Authorization not found. Token ID: "'.$token_id.'"', __FILE__,__LINE__,
                        __METHOD__);
                        $redirect_to_network = false;
                        $do_show_form = false;
                    } else {
                        //Save authorization ID and subscriber ID in subscriber_authorizations table.
                        $subscriber_authorization_dao = new SubscriberAuthorizationMySQLDAO();
                        $subscriber_authorization_dao->insert($subscriber_id, $authorization->id);
                        //Update subscribers record with level
                        if (isset(SignUpController::$membership_levels[(string) $authorization->amount])) {
                            $membership_level = SignUpController::$membership_levels[(string) $authorization->amount];
                            $subscriber_dao->setMembershipLevel($subscriber_id, $membership_level);
                        } else {
                            $this->logError('No membership level found for '.$authorization->amount, __FILE__,__LINE__,
                            __METHOD__);
                        }
                        $redirect_to_network = true;
                    }
                } catch (DuplicateSubscriberEmailException $e) {
                    $this->addErrorMessage("Whoa! We love the enthusiasm, but someone already joined ThinkUp ".
                    "with that email address. Please enter another address, or ".
                    "<a href=\"mailto:help@thinkup.com\">contact us</a> with questions.");

                    $redirect_to_network = false;
                    $do_show_form = true;
                }

                if ($redirect_to_network) {
                    if ($_POST['n'] == 'twitter') {
                        $twitter_auth_link = self::getTwitterAuthLink('new.php?n=twitter');
                        //Go to Twitter
                        header('Location: '.$twitter_auth_link);
                    } elseif ($_POST['n'] == 'facebook') {
                        //Go to Facebook
                        $fbconnect_link = self::getFacebookConnectLink('new.php?n=facebook');
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
                    $this->addSuccessMessage("Thanks so much for subscribing to ThinkUp!");
                    $do_show_form = true;
                } else {
                    $this->addErrorMessage($generic_error_msg);
                    $this->logError("Amazon returned error: ".$error_message, __FILE__,__LINE__,__METHOD__);
                }

                //Record authorization
                $authorization_dao = new AuthorizationMySQLDAO();
                $amount = SignUpController::$subscription_levels[$_GET['l']];
                $payment_expiry_date = (isset($_GET['expiry']))?$_GET['expiry']:null;

                try {
                    $authorization_id = $authorization_dao->insert($_GET['tokenID'], $amount, $_GET["status"],
                    $internal_caller_reference, $error_message, $payment_expiry_date);
                } catch (DuplicateAuthorizationException $e) {
                    $this->addSuccessMessage("Whoa there! It looks like you already paid for your ThinkUp ".
                    "subscription.  Did you refresh the page?");
                }
                //Add tokenID to cache
                SessionCache::put('token_id', $_GET['tokenID']);
            } else {
                $this->addErrorMessage($generic_error_msg);
                $this->logError('Internal caller reference not set or Amazon response invalid', __FILE__,__LINE__,
                __METHOD__);
            }
        } elseif ($this->hasUserReturnedFromTwitter() || $this->hasUserReturnedFromFacebook()) {
            $do_show_form = false;
            $update_count = 0;
            $subscriber_dao = new SubscriberMySQLDAO();
            $subscriber_id = SessionCache::get('subscriber_id');

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
                            //                            echo "<pre>";
                            //                            print_r($authed_twitter_user);
                            //                            echo "</pre>";

                            //Update subscriber record with Twitter auth information
                            $update_count = $subscriber_dao->update($subscriber_id, $authed_twitter_user['user_name'],
                            $authed_twitter_user['user_id'], 'twitter', $authed_twitter_user['full_name'],
                            $tok['oauth_token'], $tok['oauth_token_secret'], $authed_twitter_user['is_verified'],
                            $authed_twitter_user['follower_count']);
                            $do_show_form = true;
                        } else {
                            $this->addErrorMessage($generic_error_msg);
                            $this->logError("Invalid Twitter user returned: ".
                            Utils::varDumpToString($authed_twitter_user),__FILE__,__LINE__,__METHOD__);
                        }
                    } catch (DuplicateSubscriberConnectionException $e) {
                        $this->addErrorMessage("Whoa! We're love your enthusiasm, but @".
                        $authed_twitter_user['user_name']." has already joined ThinkUp. Connect another Twitter or ".
                        "Facebook account to ThinkUp.");

                        $this->addToView('do_show_just_auth_buttons', true);
                        $twitter_auth_link = self::getTwitterAuthLink();
                        $this->addToView('twitter_auth_link', $twitter_auth_link);
                        $fb_connect_link = self::getFacebookConnectLink();
                        $this->addToView('fb_connect_link', $fb_connect_link);
                    } catch (APIErrorException $e) {
                        $this->addErrorMessage($generic_error_msg);
                        $this->logError(get_class($e).":".$e->getMessage(),__FILE__,__LINE__,__METHOD__);
                    }
                } else {
                    $this->addErrorMessage($generic_error_msg);
                    $this->logError('Twitter access tokens not set '. (isset($tok)?Utils::varDumpToString($tok):''),
                    __FILE__,__LINE__,__METHOD__);
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

                        try {
                            //Update subscriber record with Facebook auth information
                            $update_count = $subscriber_dao->update($subscriber_id, $fb_user_profile['username'],
                            $fb_user_profile['id'], 'facebook', $fb_user_profile['name'], $access_token, '',
                            0);
                            //                            echo "<pre>";
                            //                            print_r($fb_user_profile);
                            //                            echo "</pre>";
                        } catch (DuplicateSubscriberConnectionException $e) {
                            $this->addErrorMessage("Whoa! We love your enthusiasm, but ".
                            $fb_user_profile['name']." on Facebook has already joined ThinkUp. ".
                            "Connect another Facebook or Twitter account to ThinkUp.");
                            $this->addToView('do_show_just_auth_buttons', true);
                            $twitter_auth_link = self::getTwitterAuthLink();
                            $this->addToView('twitter_auth_link', $twitter_auth_link);
                            $fb_connect_link = self::getFacebookConnectLink();
                            $this->addToView('fb_connect_link', $fb_connect_link);
                        }
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
                        $this->addErrorMessage($generic_error_msg);
                        $this->logError( $error_msg, __FILE__,__LINE__,__METHOD__);
                    }
                } else {
                    $this->addErrorMessage($generic_error_msg);
                    $this->logError( "Facebook auth error: Invalid CSRF token", __FILE__,__LINE__,__METHOD__);
                }
            }

            if ($update_count == 1) {
                $this->addSuccessMessage("Hooray! You're now a ThinkUp member!");

                $subscriber = $subscriber_dao->getByID($subscriber_id);

                //Send confirmation email with URL that includes verification code & address
                MandrillMailer::sendConfirmationEmail($subscriber->email, $subscriber->full_name,
                UpstartHelper::getApplicationURL().'confirm.php?usr='.urlencode($subscriber->email)."&code=".
                $subscriber->verification_code);

                //Clear SessionCache values, we're done
                SessionCache::clearAllKeys();
                $do_show_form = false;
            }
        } else { //No recognizable POST or GET vars set
            $this->addErrorMessage($generic_error_msg);
            $this->logError('No recognizable POST or GET vars set', __FILE__,__LINE__,__METHOD__);
        }

        $this->addToView('do_show_form', $do_show_form);

        return $this->generateView();
    }

    private function hasUserPostedSignUpForm() {
        return (sizeof($_POST) > 0);
    }

    private function hasUserReturnedFromAmazon() {
        return (isset($_GET['callerReference'])  && isset($_GET['tokenID']) && isset($_GET["l"])
        && isset($_GET['status']) && isset($_GET['certificateUrl']) && isset($_GET['signatureMethod'])
        && isset($_GET['signature']) );
    }

    private function isAmazonResponseValid($internal_caller_reference) {
        //Check inputs match internal rules
        $endpoint_url = UpstartHelper::getApplicationURL().'new.php';
        $endpoint_url_params = array('l'=>$_GET['l']);
        return ($internal_caller_reference == $_GET['callerReference']
        && (array_key_exists($_GET["l"], SignUpController::$subscription_levels))
        && AmazonFPSAPIAccessor::isAmazonSignatureValid($endpoint_url, $endpoint_url_params));
    }
}