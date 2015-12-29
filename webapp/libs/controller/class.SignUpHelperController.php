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
        'member'=>array('1 month'=>5, '12 months'=>50, '12 months discount'=>50),
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
        $config = Config::getInstance();
        $site_root_path = $config->getValue('site_root_path');
        $twitter_auth_link = $site_root_path."twittersignin/?redir=".urlencode($redirect_location);
        return $twitter_auth_link;
    }
    /**
     * Get link to connect your Facebook account to Upstart.
     * @param str $redirect_location What page relative to the application root to redirect on return from Twitter,
     *                               for example 'new.php?n=facebook'
     * @return str Facebook Connect link
     */
    protected function getFacebookConnectLink($redirect_location) {
        $config = Config::getInstance();
        $site_root_path = $config->getValue('site_root_path');
        $facebook_auth_link = $site_root_path."facebooksignin/?redir=".urlencode($redirect_location);
        return $facebook_auth_link;
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