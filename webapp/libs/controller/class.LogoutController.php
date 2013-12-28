<?php
class LogoutController extends AuthController {
    public function authControl() {
        Session::logout();
        $controller = new LoginController(true);
        $controller->disableCaching();
        $controller->addSuccessMessage("You have successfully logged out.");
        return $controller->go();
    }
}
