<?php

abstract class UpstartAuthController extends AuthController {

    public function __construct($session_started=false) {
        parent::__construct($session_started);
        if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'logout.php') === false ) {
            $this->url_mapping = 'http://'.UpstartHelper::getApplicationHostName().$_SERVER['REQUEST_URI'];
        }
    }
    /**
     * Bounce user to log in.
     * A child class can override this method to define different bounce behavior.
     */
    protected function bounce() {
        if ($this->url_mapping != null ) {
            $this->redirect(UpstartHelper::getApplicationURL().'user/?redirect='.$this->url_mapping);
        } else {
            $controller = new LoginController(true);
            return $controller->go();
        }
    }
}