<?php

abstract class AuthController extends UpstartController {
    public function __construct($session_started=false) {
        parent::__construct($session_started);
    }

    public function control() {
        $response = $this->preAuthControl();
        if (!$response) {
            if (Session::isLoggedIn()) {
                return $this->authControl();
            } else {
                return $this->bounce();
            }
        } else {
            return $response;
        }
    }

    /**
     * A child class can override this method to define other auth mechanisms.
     * If the return is not false it assumes the child class has validated the user and has called authControl()
     * @return boolean PreAuthed
     */
    protected function preAuthControl() {
        return false;
    }

    /**
     * Bounce user to public page or to error page.
     * @TODO bounce back to original action once signed in
     */
    protected function bounce() {
        $controller = new LoginController(true);
        return $controller->go();
    }
}