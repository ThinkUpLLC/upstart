<?php
/**
 * Authorize user on Twitter and add user to user_routes table.
 * @author gina
 */
class RouteUserController extends Controller {
    /**
     * Twitter OAuth consumer key
     * @var str
     */
    var $oauth_consumer_key;
    /**
     * Twitter OAuth consumer secret
     * @var str
     */
    var $oauth_consumer_secret;

    public function control() {
        $this->setViewTemplate('index.tpl');

        $cfg = Config::getInstance();
        $this->oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
        $this->oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

        if (self::isFormInputValid()) {
            //Store email address in Session Cache
            SessionCache::put('waitlisted_email', $_POST['email']);

            $to = new TwitterOAuth($this->oauth_consumer_key, $this->oauth_consumer_secret);
            //Add unique waitlisted user ID from previous DB operation to callback
            $tok = $to->getRequestToken(self::getApplicationURL());

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
        } elseif (self::hasReturnedFromTwitterAuth()) {
            $request_token = $_GET['oauth_token'];
            $request_token_secret = SessionCache::get('oauth_request_token_secret');
            $to = new TwitterOAuth($this->oauth_consumer_key, $this->oauth_consumer_secret,
            $request_token, $request_token_secret);

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
                    $waitlisted_email = SessionCache::get('waitlisted_email');
                    $dao = new UserRouteMySQLDAO();
                    $route_id = $dao->insert($waitlisted_email, $authed_twitter_user['user_name'],
                    $authed_twitter_user['user_id'], $tok['oauth_token'], $tok['oauth_token_secret'],
                    $authed_twitter_user['is_verified'], $authed_twitter_user['follower_count'],
                    $authed_twitter_user['full_name']);
                    if ($route_id !== false) {
                        if (self::subscribeUserViaMailChimp($waitlisted_email)) {
                            $this->addSuccessMessage("Thanks, @".$authed_twitter_user['user_name'].
                            "!<br><br>You're on ThinkUp's waiting list. We'll let you know when your spot opens up." );
                            //Let up on the email for now
                            //self::notifyAdmins($authed_twitter_user, $waitlisted_email);
                        }
                        //Install application
                        try {
                            $installer = new AppInstaller();
                            $installer->install($route_id);
                        } catch (Exception $e) {
                            self::notifyAdmins($authed_twitter_user, $waitlisted_email, $e->getMessage());
                        }

                    } else {
                        $this->addErrorMessage("Something went wrong. Couldn't add  @".
                        $authed_twitter_user['user_name']." to the list." );
                    }
                } else {
                    $this->addErrorMessage("Oops! Something went wrong. Twitter didn't return a valid user.");
                }
            } else {
                $this->addErrorMessage("Oops! Something went wrong. ".Utils::varDumpToString($tok) );
            }
        } elseif (isset($_GET['e']) && self::validateEmail($_GET['e'])) {
            $this->addToView('prefill_email', $_GET['e']);
        }

        return $this->generateView();
    }
    /**
     * Subscribe new user to MailChimp list.
     */
    protected function subscribeUserViaMailChimp($waitlisted_email) {
        $cfg = Config::getInstance();

        $api_key = $cfg->getValue('mailchimp_api');
        $list_id = $cfg->getValue('mailchimp_list_id');

        $api = new MCAPI($api_key);
        $api->useSecure(true);

        $merge_vars = array('FNAME'=>'', 'LNAME'=>'', 'GROUPINGS'=>array());

        // By default this sends a confirmation email - you will not see new members in MailChimp
        // until the link contained in it is clicked!
        $retval = $api->listSubscribe( $list_id, $waitlisted_email, $merge_vars );

        if ($api->errorCode){
            if ($api->errorCode != '214') { //Error other than 'already subscribed'
                $this->addErrorMessage('MailChimp error '.$api->errorCode.': '.$api->errorMessage);
                return false;
            } else {
                // Email address is already subscribed; that's okay.
                return true;
            }
        } else {
            return true;
        }
    }
    /**
     * Email admin notification of new user.
     * @param unknown_type $authed_twitter_user
     * @param unknown_type $waitlisted_email
     */
    protected function notifyAdmins($authed_twitter_user, $waitlisted_email, $message ) {
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $email_view_mgr->assign('twitter_user', $authed_twitter_user);
        $email_view_mgr->assign('waitlisted_email', $waitlisted_email);
        $email_view_mgr->assign('message', $message);
        $message = $email_view_mgr->fetch('_email.notifyadmin.tpl');
        Mailer::mail('upstart@thinkup.com', '[Upstart] @'.$authed_twitter_user['user_name']." is on the waitlist",
        $message);
    }
    /**
     * Verify form input and add appropriate error message if not
     * return bool
     */
    private function isFormInputValid() {
        if (isset($_POST['email']) && empty($_POST['email'])) {
            $this->addErrorMessage('Please enter your email address.');
        }
        $is_valid_address = false;
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $is_valid_address = self::validateEmail($_POST['email']);
            if (!$is_valid_address) {
                $this->addErrorMessage('Please enter a valid email address.');
            }
        }
        return (isset($_POST['email']) && $is_valid_address);
    }
    /**
     * Chek if user has returned from Twitter authorization process
     * @return bool
     */
    private function hasReturnedFromTwitterAuth() {
        return (isset($_GET['oauth_token']));
    }
    /**
     * Get application URL
     * @param bool $replace_localhost_with_ip
     * @return str application URL
     */
    private static function getApplicationURL($replace_localhost_with_ip = false) {
        //First attempt to get the host name without querying the database
        $server = empty($_SERVER['SERVER_NAME']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        if ($replace_localhost_with_ip) {
            $server = ($server == 'localhost')?'127.0.0.1':$server;
        }
        $site_root_path = self::getSiteRootPathFromFileSystem();
        //URLencode everything except spaces in site_root_path
        $site_root_path = str_replace('%2f', '/', strtolower(urlencode($site_root_path)));
        if  (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') { //non-standard port
            if (isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == '443') { //account for standard https port
                $port = '';
            } else {
                $port = ':'.$_SERVER['SERVER_PORT'];
            }
        } else {
            $port = '';
        }
        return 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$server.$port.$site_root_path;
    }

    private static function getSiteRootPathFromFileSystem() {
        $dirs_under_root = array('admin');
        if (isset($_SERVER['PHP_SELF'])) {
            $current_script_path = explode('/', $_SERVER['PHP_SELF']);
        } else {
            $current_script_path = array();
        }
        array_pop($current_script_path);
        if ( in_array( end($current_script_path), $dirs_under_root ) ) {
            array_pop($current_script_path);
        }
        $current_script_path = implode('/', $current_script_path) . '/';
        return $current_script_path;
    }
    /**
     * Validate email address
     * This method uses a raw regex instead of filter_var because as of PHP 5.3.3,
     * filter_var($email, FILTER_VALIDATE_EMAIL) validates local email addresses.
     * From 5.2 to 5.3.3, it does not.
     * Therefore, this method uses the PHP 5.2 regex instead of filter_var in order to return consistent results
     * regardless of PHP version.
     * http://svn.php.net/viewvc/php/php-src/trunk/ext/filter/logical_filters.c?r1=297250&r2=297350
     *
     * @param str $email Email address to validate
     * @return bool Whether or not it's a valid address
     */
    private static function validateEmail($email = '') {
        //return filter_var($email, FILTER_VALIDATE_EMAIL));
        $reg_exp = "/^((\\\"[^\\\"\\f\\n\\r\\t\\b]+\\\")|([A-Za-z0-9_][A-Za-z0-9_\\!\\#\\$\\%\\&\\'\\*\\+\\-\\~\\".
        "/\\=\\?\\^\\`\\|\\{\\}]*(\\.[A-Za-z0-9_\\!\\#\\$\\%\\&\\'\\*\\+\\-\\~\\/\\=\\?\\^\\`\\|\\{\\}]*)*))@((\\".
        "[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.".
        "((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\\])|".
        "(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.".
        "((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|".
        "((([A-Za-z0-9])(([A-Za-z0-9\\-])*([A-Za-z0-9]))?(\\.(?=[A-Za-z0-9\\-]))?)+[A-Za-z]+))$/D";
        //return (preg_match($reg_exp, $email) === false)?false:true;
        return (preg_match($reg_exp, $email)>0)?true:false;
    }
}
