<?php

class InvalidCSRFTokenException extends Exception {
    public function __construct($token = false) {
        $message = "Invalid CSRF token passed";
        if ($token) {
            $message .= ': ' . $token;
        }
        parent::__construct($message);
    }
}
