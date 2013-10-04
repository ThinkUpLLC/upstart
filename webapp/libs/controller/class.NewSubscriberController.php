<?php
/**
 * Create ThinkUp account for new subscriber
 */
class NewSubscriberController extends SignUpController {
    public function control() {
        $this->setViewTemplate('subscribe-newsubscriber.tpl');
        $do_show_form = false;

        if (sizeof($_POST) > 0) { //user has submitted account creation form
            if (self::isEmailInputValid() & self::isPasswordInputValid()) {
                //Store email address in Session Cache
                SessionCache::put('newaccount_email', $_POST['email']);
                SessionCache::put('newaccount_pass', $_POST['password']);

                $cfg = Config::getInstance();
                $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
                $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

                $to = new TwitterOAuth($oauth_consumer_key, $oauth_consumer_secret);
                //Add unique waitlisted user ID from previous DB operation to callback
                $tok = $to->getRequestToken(UpstartHelper::getApplicationURL().'confirm.php');

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
        } else { //user has returned from Amazon
            $internal_caller_reference = SessionCache::get('caller_reference');
            if (isset($internal_caller_reference) && $this->isAmazonResponseValid($internal_caller_reference)) {
                $amazon_caller_reference = $_GET['callerReference'];
                $this->addToView('amazon_caller_reference', $amazon_caller_reference);
                $this->addSuccessMessage("W00t! Thanks for subscribing to ThinkUp, you glorious member, you.");
                $do_show_form = true;
                //@TODO Record transaction
                //@TODO Clear caller_reference from cache and add tokenID to cache

            } else {
                $this->addErrorMessage("Oops! Something went wrong. Amazon's redirect is invalid or incomplete. ".
                //@TODO Link Please try again to the subscribe page
                "Please try again.");
            }
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
    /*
     http://dev.upstart.com/subscribe/newaccount.php?l=member&
     tokenID=64QF9XGGXVD9UNI9S7KB53GYHLQEJRPSG8I8AM34MFGMCXVTD7HPKOEDNGDHS6D7&
     signatureMethod=RSA-SHA1&status=SC&signatureVersion=2&
     signature=HESfhdbwQfp0fYyx6%2BmpK%2F4N9tZPTe8N%2Fja0%2B32ab5suvlCqFJi4WU4JuteLY5O3zvuA4uNRJDwL%0A05FbSLhb3jZok4H9c
     %2BOJbF8VofF7qEUdwhXCHpoXD%2F5LaYTeGCNe8zuSpDupN1Fhcje9sr07oBrB%0AnXNkqKSQW8e7pS5rfvr2KA1eZWGWbnswRT09QJmcTSDzym5%
     2BGXilhkLIqDr0sxozM21dWprdqGWO%0ATkMoIj%2BHVfha2vE023%2B2p8PGAAb7yStWo4tlan7ER9Gj19qm1QR4fkF6oT5R1wkn1F2X%2Ft9uHzrl
     %0Ayn3L8iqFxebcfAtN3%2BzOQ6yr0a%2Fg4QJLKxWq6A%3D%3D&
     certificateUrl=https%3A%2F%2Ffps.amazonaws.com%2Fcerts%2F110713%2FPKICert.pem%3F
     requestId%3Dbk0guw95btzlsf08awrjgkgv2lw82id929cu6nwonow976zuek3
     &expiry=01%2F20
     */
    private function isAmazonResponseValid($internal_caller_reference) {
        if ( isset($_GET['callerReference']) &&
        $internal_caller_reference == $_GET['callerReference'] &&
        isset($_GET['tokenID']) && isset($_GET['expiry']) && isset($_GET['certificateUrl']) &&
        isset($_GET['signatureMethod']) && isset($_GET['signature']) && isset($_GET["l"]) &&
        (array_key_exists($_GET["l"], SignUpController::$subscription_levels))) {
            return true;
        } else {
            return false;
        }
    }
}