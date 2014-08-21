<?php
class LogoutController extends AuthController {
    public function authControl() {
        $subscriber_dao = new SubscriberMySQLDAO();
        $logged_in_user = Session::getLoggedInUser();
        try {
            $subscriber = $subscriber_dao->getByEmail( $logged_in_user );
        } catch (SubscriberDoesNotExistException $e) {
            //Do nothing because this is a rare case when a user is logged in as a subscriber that no longer exists
            //ie, they got uninstalled overnight or something.
        }
        Session::logout();

        if (isset($subscriber->thinkup_username) && isset($subscriber->date_installed)
            && $subscriber->is_installation_active) {
        	// Log out via API
            $config = Config::getInstance();
            $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
            $config->getValue('user_installation_url'));
            $upstart_url = UpstartHelper::getApplicationURL() . $config->getValue('site_root_path');

            // Call Session API logout endpoint
            $url = $user_installation_url.'api/v1/session/logout.php';
            $this->getURLContents($url);
        }
        // Logout complete, show login screen
        $controller = new LoginController(true);
        $controller->disableCaching();
        $preset_success_message = $this->view_mgr->getTemplateVars('success_msg');
        if ( isset($preset_success_message)) {
            $controller->addSuccessMessage($preset_success_message);
        } else {
            $controller->addSuccessMessage("You have successfully logged out.");
        }
        return $controller->go();
    }

    /**
     * Get the contents of a URL via GET
     * @param str $URL
     * @return str contents
     */
    public static function getURLContents($URL) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $URL);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT ,2);
        $contents = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        //echo "URL: ".$URL."\n";
        //echo $contents;
        //echo "STATUS: ".$status."\n";
        if (isset($contents)) {
            return $contents;
        } else {
            return null;
        }
    }
}
