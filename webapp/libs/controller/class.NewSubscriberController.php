<?php
/**
 * Create ThinkUp account for new subscriber
 */
class NewSubscriberController extends SignUpController {
    public function control() {
        $this->setViewTemplate('subscribe-newsubscriber.tpl');
        $internal_caller_reference = SessionCache::get('caller_reference');
        $this->addToView('internal_caller_reference', $internal_caller_reference);
        //TODO Verify all of Amazon's return values are set and that there's no error

        if (isset($_GET['callerReference'])) {
            $amazon_caller_reference = $_GET['callerReference'];
            $this->addToView('amazon_caller_reference', $amazon_caller_reference);
            if ($internal_caller_reference == $amazon_caller_reference) {
                $this->addSuccessMessage("W00t! Thanks for subscribing to ThinkUp, you glorious member, you.");
            } else {
                $this->addErrorMessage("Oops! Something went wrong. Caller reference mismatch.");
            }
        } else {
            $this->addErrorMessage("Oops! Something went wrong. No external callerReference.");
        }

        if (self::isEmailInputValid() && self::isPasswordInputValid()) {
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
        }
        return $this->generateView();
    }
}