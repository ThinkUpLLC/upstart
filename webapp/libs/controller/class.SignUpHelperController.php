<?php
/**
 * Parent controller for signing up for ThinkUp via waiting list or subscribing.
 * @author gina
 *
 */
abstract class SignUpHelperController extends Controller {
    /*
     * Subscription level amounts
     */
    public static $subscription_levels = array(
        'earlybird'=>array('12 months'=>50),
        'member'=>array('1 month'=>5, '12 months'=>60, '12 months discount'=>50),
        'pro'=>array('1 month'=>10, '12 months'=>120, '12 months discount'=>100),
        'executive'=>array('12 months'=>996)
    );
    /**
     * Verify ThinkUp username and add appropriate error message if not
     * return bool
     */
    protected function isUsernameValid() {
        if (isset($_POST['username']) && empty($_POST['username'])) {
            $this->addErrorMessage('Please choose your Insights URL.', 'username');
        }
        $is_valid_username = false;
        if (isset($_POST['username']) && !empty($_POST['username'])) {
            $is_valid_username = UpstartHelper::isUsernameValid($_POST["username"]);
            if (!$is_valid_username) {
                $this->addErrorMessage('Must be between 3 - 15 unaccented numbers or letters',
                    'username');
            }
        }
        return (isset($_POST['username']) && $is_valid_username);
    }
    /**
     * Verify posted email address input and add appropriate error message if not
     * return bool
     */
    protected function isEmailInputValid() {
        if (isset($_POST['email']) && empty($_POST['email'])) {
            $this->addErrorMessage('Please enter an email address.', 'email');
        }
        $is_valid_address = false;
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $is_valid_address = UpstartHelper::validateEmail($_POST['email']);
            if (!$is_valid_address) {
                $this->addErrorMessage('Please enter a valid email address.', 'email');
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
            $this->addErrorMessage('Please enter a password.', 'password');
        }
        $is_valid_password = false;
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $is_valid_password = UpstartHelper::validatePassword($_POST['password']);
            if (!$is_valid_password) {
                $this->addErrorMessage('Password must be at least 8 characters and contain both numbers and letters.',
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
        $tok = $to->getRequestToken(UpstartHelper::getApplicationURL(false, false).$redirect_location);

        if (isset($tok['oauth_token'])) {
            $token = $tok['oauth_token'];
            SessionCache::put('oauth_request_token_secret', $tok['oauth_token_secret']);
            // Build Twitter authorization URL
            $twitter_auth_link = $to->getAuthorizeURL($token);
        } else {
            $this->addErrorMessage($generic_error_msg);
            Logger::logError('Twitter auth link failure, token not set '.htmlentities(Utils::varDumpToString($tok)),
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

        //Plant unique token for CSRF protection during auth
        //per https://developers.facebook.com/docs/authentication/
        if (SessionCache::get('facebook_auth_csrf') == null) {
            SessionCache::put('facebook_auth_csrf', md5(uniqid(rand(), true)));
        }

        $scope = 'user_posts,email';
        $state = SessionCache::get('facebook_auth_csrf');
        $redirect_url = UpstartHelper::getApplicationURL(false, false).$redirect_location;

        $fbconnect_link = FacebookGraphAPIAccessor::getLoginURL($facebook_app_id, $scope, $state, $redirect_url);
        return $fbconnect_link;
    }

    /**
     * Return whether or not user has returned from Facebook with necessary parameters.
     * @return bool
     */
    protected function hasUserReturnedFromFacebook() {
        return (isset($_GET['n']) && isset($_GET['code']) && isset($_GET['state']) && $_GET["n"] == 'facebook');
    }

    /**
     * Return whether or not user has returned from Twitter with necessary parameters.
     * @return bool
     */
    protected function hasUserReturnedFromTwitter() {
        return (isset($_GET['n']) && isset($_GET['oauth_token']) && $_GET["n"] == 'twitter');
    }
}