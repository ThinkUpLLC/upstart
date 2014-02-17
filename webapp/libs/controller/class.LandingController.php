<?php

class LandingController extends SignUpController {
    public function control() {
        $this->setViewTemplate('landing.tpl');

        //@TODO Confirm caching can stay on for this page
        $twitter_link = $this->getTwitterAuthLink('register.php?n=twitter');
        $this->addToView('twitter_link', $twitter_link);
        $facebook_link = $this->getFacebookConnectLink('register.php?n=facebook');
        $this->addToView('facebook_link', $facebook_link);
        return $this->generateView();
    }
}