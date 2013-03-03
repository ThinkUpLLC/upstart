<?php
class SessionCache {
    /**
     * Put a value in ThinkUp's $_SESSION key.
     * @param str $key
     * @param str $value
     */
    public static function put($key, $value) {
        $config = Config::getInstance();
        $_SESSION[$config->getValue('source_root_path')][$key] = $value;
    }

    /**
     * Get a value from ThinkUp's $_SESSION.
     * @param str $key
     * @return mixed Value
     */
    public static function get($key) {
        $config = Config::getInstance();
        if (self::isKeySet($key)) {
            return $_SESSION[$config->getValue('source_root_path')][$key];
        } else {
            return null;
        }
    }

    /**
     * Check if a key in ThinkUp's $_SESSION has a value set.
     * @param str $key
     * @return bool
     */
    public static function isKeySet($key) {
        $config = Config::getInstance();
        return isset($_SESSION[$config->getValue('source_root_path')][$key]);
    }

    /**
     * Unset key's value in ThinkUp's $_SESSION
     * @param str $key
     */
    public static function unsetKey($key) {
        $config = Config::getInstance();
        unset($_SESSION[$config->getValue('source_root_path')][$key]);
    }
}