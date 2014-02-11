<?php
/**
 * Parent controller for signing up for ThinkUp via waiting list or subscribing.
 * @author gina
 *
 */
abstract class SignUpController extends UpstartController {
    /*
     * Subscription level amounts
     */
    public static $subscription_levels = array('earlybird'=>50, 'member'=>60, 'pro'=>120, 'executive'=>996);
    /*
     * Membership level names
     */
    public static $membership_levels = array('60'=>'Member', '120'=>'Pro', '996'=>'Exec');
    /**
     * Verify posted email address input and add appropriate error message if not
     * return bool
     */
    protected function isEmailInputValid() {
        if (isset($_POST['email']) && empty($_POST['email'])) {
            $this->addInfoMessage('Please enter your email address.', 'email');
        }
        $is_valid_address = false;
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $is_valid_address = UpstartHelper::validateEmail($_POST['email']);
            if (!$is_valid_address) {
                $this->addInfoMessage('Please enter a valid email address.', 'email');
            }
        }
        return (isset($_POST['email']) && $is_valid_address);
    }
    /**
     * Verify posted password input and add appropriate error message if not
     * return bool
     */
    protected function isPasswordInputValid() {
        if (isset($_POST['password']) && empty($_POST['password'])) {
            $this->addInfoMessage('Please enter a password.', 'password');
        }
        $is_valid_password = false;
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $is_valid_password = UpstartHelper::validatePassword($_POST['password']);
            if (!$is_valid_password) {
                $this->addInfoMessage('Password must be at least 8 characters and contain both numbers and letters.',
                'password');
            }
        }
        return (isset($_POST['password']) && $is_valid_password);
    }

    /**
     * Get link to connect your Twitter account to Upstart.
     * @param str $redirect_location What page relative to the application root to redirect on return from Twitter,
     *                               for example 'new.php?n=twitter'
     * @return str Twitter link
     */
    protected function getTwitterAuthLink($redirect_location) {
        $twitter_auth_link = null;
        $cfg = Config::getInstance();
        $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
        $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

        $to = new TwitterOAuth($oauth_consumer_key, $oauth_consumer_secret);
        //Add unique waitlisted user ID from previous DB operation to callback
        $tok = $to->getRequestToken(UpstartHelper::getApplicationURL().$redirect_location);

        if (isset($tok['oauth_token'])) {
            $token = $tok['oauth_token'];
            SessionCache::put('oauth_request_token_secret', $tok['oauth_token_secret']);
            // Build Twitter authorization URL
            $twitter_auth_link = $to->getAuthorizeURL($token);
        } else {
            $this->addErrorMessage($generic_error_msg);
            $this->logError('Twitter auth link failure, token not set '.(isset($tok))?Utils::varDumpToString($tok):'',
            __FILE__,__LINE__,__METHOD__);
        }
        return $twitter_auth_link;
    }

    /**
     * Get link to connect your Facebook account to Upstart.
     * @param str $redirect_location What page relative to the application root to redirect on return from Twitter,
     *                               for example 'new.php?n=facebook'
     * @return str Facebook Connect link
     */
    protected function getFacebookConnectLink($redirect_location) {
        $fbconnect_link = null;
        $cfg = Config::getInstance();
        $facebook_app_id = $cfg->getValue('facebook_app_id');
        $facebook_api_secret = $cfg->getValue('facebook_api_secret');

        // Create Facebook Application instance
        $facebook_app = new Facebook(array('appId'  => $facebook_app_id, 'secret' => $facebook_api_secret ));

        try {
            //Plant unique token for CSRF protection during auth
            //per https://developers.facebook.com/docs/authentication/
            if (SessionCache::get('facebook_auth_csrf') == null) {
                SessionCache::put('facebook_auth_csrf', md5(uniqid(rand(), true)));
            }

            $params = array('scope'=>'read_stream,user_likes,user_location,user_website,'.
                'read_friendlists,friends_location,manage_pages,read_insights,manage_pages',
                'state'=>SessionCache::get('facebook_auth_csrf'),
                'redirect_uri'=>UpstartHelper::getApplicationURL().$redirect_location);

            $fbconnect_link = $facebook_app->getLoginUrl($params);
        } catch (FacebookApiException $e) {
            $this->addErrorMessage($generic_error_msg);
            $this->logError(get_class($e).': '.$e->getMessage(), __FILE__,__LINE__,__METHOD__);
        }
        return $fbconnect_link;
    }
}