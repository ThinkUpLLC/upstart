<?php

class LandingController extends SignUpHelperController {
    public function control() {
        //@TODO Confirm caching can stay on for this page
        $this->setViewTemplate('landing.tpl');
        return $this->generateView();
    }
}