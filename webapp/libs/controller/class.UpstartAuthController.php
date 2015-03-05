<?php

abstract class UpstartAuthController extends AuthController {
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