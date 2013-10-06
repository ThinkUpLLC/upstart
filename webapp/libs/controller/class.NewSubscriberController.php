<?php
/**
 * Create ThinkUp account for new subscriber
 */
class NewSubscriberController extends SignUpController {
    public function control() {
        $this->setViewTemplate('subscribe-newsubscriber.tpl');
        $do_show_form = false;

        if ($this->hasUserPostedSignUpForm()) {
            if (self::isEmailInputValid() & self::isPasswordInputValid()) {
                //Store email address in Session Cache
                SessionCache::put('newaccount_email', $_POST['email']);
                SessionCache::put('newaccount_pass', $_POST['password']);

                $cfg = Config::getInstance();
                $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
                $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

                $to = new TwitterOAuth($oauth_consumer_key, $oauth_consumer_secret);
                //Add unique waitlisted user ID from previous DB operation to callback
                $tok = $to->getRequestToken(UpstartHelper::getApplicationURL().'newsubscriber.php?n=twitter');

                if (isset($tok['oauth_token'])) {
                    $token = $tok['oauth_token'];
                    SessionCache::put('oauth_request_token_secret', $tok['oauth_token_secret']);
                    // Build the authorization URL
                    $oauthorize_link = $to->getAuthorizeURL($token);
                    //Redirect to oauthorize link
                    header('Location: '.$oauthorize_link);
                } else {
                    $this->addErrorMessage("Oops! Something went wrong. ".Utils::varDumpToString($tok) );
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

                //Record transaction
                $transaction_dao = new TransactionMySQLDAO();
                $amount = SignUpController::$subscription_levels[$_GET['l']];
                $payment_expiry_date = (isset($_GET['expiry']))?$_GET['expiry']:null;

                try {
                    $transaction_id = $transaction_dao->insert($_GET['tokenID'], $amount, $_GET["status"],
                    $error_message, $payment_expiry_date);
                } catch (DuplicateTransactionException $e) {
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
            //@TODO Verify that transactions.token_id exists in database, show error if not.
            //@TODO Check that email address doesn't already exist in database. If it does, check if there's already
            // a successful transaction associated.
            //If so, show error. If no transaction, overwrite existing subscriber row.

            if ($this->hasUserReturnedFromTwitter()) {
                $request_token = $_GET['oauth_token'];
                $request_token_secret = SessionCache::get('oauth_request_token_secret');
                $to = new TwitterOAuth($this->oauth_consumer_key, $this->oauth_consumer_secret, $request_token,
                $request_token_secret);

                if (isset($_GET['oauth_verifier'])) {
                    $tok = $to->getAccessToken($_GET['oauth_verifier']);
                } else {
                    $tok = null;
                }

                if (isset($tok['oauth_token']) && isset($tok['oauth_token_secret'])) {
                    $api = new TwitterAPIAccessorOAuth($tok['oauth_token'], $tok['oauth_token_secret'],
                    $this->oauth_consumer_key, $this->oauth_consumer_secret, 5,  false);

                    $authed_twitter_user = $api->verifyCredentials();

                    if (isset($authed_twitter_user['user_name'])) {
                        //                    echo "<pre>";
                        //                    print_r($authed_twitter_user);
                        //                    echo "</pre>";
                        //Update waitlisted user with user name, user id, tokens, is_verified, follower_count
                        $account_email = SessionCache::get('newaccount_email');
                        $account_pass = SessionCache::get('newaccount_password');

                        //@TODO Save values to subscribers table.
                        //@TODO Save transaction ID and subscriber ID in subscriber_transactions table.
                        //                    $dao = new UserRouteMySQLDAO();
                        //                    $route_id = $dao->insert($waitlisted_email, $authed_twitter_user['user_name'],
                        //                    $authed_twitter_user['user_id'], $tok['oauth_token'], $tok['oauth_token_secret'],
                        //                    $authed_twitter_user['is_verified'], $authed_twitter_user['follower_count'],
                        //                    $authed_twitter_user['full_name']);

                        //@TODO Send email to validate email address with URL that includes verification code & address.
                    } else {
                        $this->addErrorMessage("Oops! Something went wrong. Twitter didn't return a valid user.");
                    }
                } else {
                    $this->addErrorMessage("Oops! Something went wrong. ".Utils::varDumpToString($tok) );
                }

            } elseif ($this->hasUserReturnedFromFacebook()) {
                //@TODO Process Facebook signup here
            }
        } else { //No recognizable POST or GET vars set
            //@TODO Link Please try again to the subscribe page
            $this->addErrorMessage("Oops! Something went wrong. Please try again.");
        }
        //for debugging
        $internal_caller_reference = SessionCache::get('caller_reference');
        $this->addToView('internal_caller_reference', $internal_caller_reference);
        if (isset($_GET['callerReference'])) {
            $this->addToView('amazon_caller_reference', $_GET['callerReference']);
        }

        $this->addToView('do_show_form', $do_show_form);
        return $this->generateView();
    }

    private function hasUserPostedSignUpForm() {
        return (sizeof($_POST) > 0);
    }

    private function hasUserReturnedFromFacebook() {
        return (isset($_GET['n']) && isset($_GET['oauth_token']) && $_GET["n"] == 'facebook');
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
            $endpoint_url = UpstartHelper::getApplicationURL().'newsubscriber.php?l='.$_GET['l'];
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

            /*
             echo ("Service Response\n");
             echo ("=============================================================================\n");

             echo("        VerifySignatureResponse\n");
             if ($response->isSetVerifySignatureResult()) {
             echo("            VerifySignatureResult\n");
             $verifySignatureResult = $response->getVerifySignatureResult();
             if ($verifySignatureResult->isSetVerificationStatus())
             {
             echo("                VerificationStatus\n");
             echo("                    " . $verifySignatureResult->getVerificationStatus() . "\n");
             }
             }
             if ($response->isSetResponseMetadata()) {
             echo("            ResponseMetadata\n");
             $responseMetadata = $response->getResponseMetadata();
             if ($responseMetadata->isSetRequestId())
             {
             echo("                RequestId\n");
             echo("                    " . $responseMetadata->getRequestId() . "\n");
             }
             }
             */
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