<?php
class TestLoginHelper {
    /**
     * For testing purposes only, to populate the pwd field in subscribers
     * @param str $password
     * @param str $salt
     * @return Hashed password used from beta 15 on
     */
    public static function hashPassword($password, $salt) {
        return hash('sha256', $password.$salt);
    }
}