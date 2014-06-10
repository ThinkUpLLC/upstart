<?php
class Facebook {

    /**
     * @var str type of user we want to mock
     */
    public static $user_type = "user";

    public function __construct($config) {
    }

    public function setAppId($appId) {
    }

    public function getSession() {
        return 'session';
    }

    public function getUser() {
        $session = $this->getSession();
        return $session ? $session['uid'] : null;
    }

    public function getAccessToken() {
        return 'accesstoken';
    }

    public function getLoginUrl($params=array()) {
        return 'mockloginurl';
    }

    public function getLogoutUrl($params=array()) {
        return 'mocklogouturl';
    }

    public function api($str) {
        if ($str = '/me') {
            if (Facebook::$user_type == 'business') {
                return array('username'=>'businessaccount', 'id'=>'606837591');
            } else {
                return array('name'=>'Gina Trapani', 'id'=>'606837591');
            }
        }
    }

    public function setAccessToken($token) {
    }
}

class BaseFacebook {
    //placeholder for mock class load detection in facebook.php plugin file
}
