<?php
class Session {

    /**
     * @return bool Is user logged in
     */
    public static function isLoggedIn() {
        if (!SessionCache::isKeySet('user')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return str Currently logged-in username (email address)
     */
    public static function getLoggedInUser() {
        if (self::isLoggedIn()) {
            return SessionCache::get('user');
        } else {
            return null;
        }
    }

    /**
     * Complete login action.
     * @param Subscriber $subscriber
     */
    public static function completeLogin($subscriber) {
        SessionCache::put('user', $subscriber->email);
        // set a CSRF token
        SessionCache::put('csrf_token', uniqid(mt_rand(), true));
        if (isset($_SESSION["MODE"]) && $_SESSION["MODE"] == 'TESTS') {
            SessionCache::put('csrf_token', 'TEST_CSRF_TOKEN');
        }
    }

    /**
     * Log out
     */
    public static function logout() {
        SessionCache::unsetKey('user');
    }

    /**
     * Returns a CSRF token that should be used whith _GETs and _POSTs requests.
     * @return str CSRF token
     */
    public static function getCSRFToken() {
        if (self::isLoggedIn()) {
            return SessionCache::get('csrf_token');
        } else {
            return null;
        }
    }
}