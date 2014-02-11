<?php

class RegisterNewUserController extends SignUpController {
    public function control() {
        $this->setViewTemplate('register-new-user.tpl');
        return $this->generateView();
    }
}