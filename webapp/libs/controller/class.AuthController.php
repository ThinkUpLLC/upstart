<?php

abstract class AuthController extends UpstartController {

    /**
     * The web app URL this controller maps to.
     * @var str
     */
    var $url_mapping = null;

    public function __construct($session_started=false) {
        parent::__construct($session_started);
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'logout.php') === false ) {
            $this->url_mapping = 'http://'.UpstartHelper::getApplicationHostName().$_SERVER['REQUEST_URI'];
        }
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
     * Bounce user to log in.
     */
    protected function bounce() {
        if ($this->url_mapping != null ) {
            $cfg = Config::getInstance();
            $site_root_path = $cfg->getValue('site_root_path');
            $this->redirect(UpstartHelper::getApplicationURL().'user/?redirect='.$this->url_mapping);
        } else {
            $controller = new LoginController(true);
            return $controller->go();
        }
    }
}