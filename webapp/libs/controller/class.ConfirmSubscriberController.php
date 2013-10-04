<?php

class ConfirmSubscriberController extends SignUpController {
    public function control() {
        $this->setViewTemplate('subscribe-confirm.tpl');

        if (self::hasReturnedFromTwitterAuth()) {
            $this->addSuccessMessage("Back from Twitter, eh? This is the part where we check stuff, store stuff and send a confirmation email.");
        }
        return $this->generateView();
    }
}