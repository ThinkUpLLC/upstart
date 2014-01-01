<?php

abstract class UpstartController extends Controller {
    public function __construct($session_started=false) {
        parent::__construct($session_started);
    }
    /**
     * Send Location header
     * @param str $destination
     * @return bool Whether or not redirect header was sent
     */
    protected function redirect($destination=null) {
        if (!isset($destination)) {
            $destination = Utils::getSiteRootPathFromFileSystem();
        }
        $this->redirect_destination = $destination; //for validation
        if ( !headers_sent() ) {
            header('Location: '.$destination);
            return true;
        } else {
            return false;
        }
    }
}