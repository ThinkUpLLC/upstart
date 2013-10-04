<?php
abstract class SignUpController extends Controller {
    /**
     * Chek if user has returned from Twitter authorization process
     * @return bool
     */
    protected function hasReturnedFromTwitterAuth() {
        return (isset($_GET['oauth_token']));
    }
    /**
     * Verify posted email address input and add appropriate error message if not
     * return bool
     */
    protected function isEmailInputValid() {
        if (isset($_POST['email']) && empty($_POST['email'])) {
            $this->addErrorMessage('Please enter your email address.');
        }
        $is_valid_address = false;
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $is_valid_address = UpstartHelper::validateEmail($_POST['email']);
            if (!$is_valid_address) {
                $this->addErrorMessage('Please enter a valid email address.');
            }
        }
        return (isset($_POST['email']) && $is_valid_address);
    }
    /**
     * Verify posted password input and add appropriate error message if not
     * return bool
     */
    protected function isPasswordInputValid() {
        if (isset($_POST['password']) && empty($_POST['password'])) {
            $this->addErrorMessage('Please enter a password.');
        }
        $is_valid_password = false;
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $is_valid_password = UpstartHelper::validatePassword($_POST['password']);
            if (!$is_valid_password) {
                $this->addErrorMessage('Password must be at least 8 characters and contain both numbers and letters.');
            }
        }
        return (isset($_POST['password']) && $is_valid_password);
    }

}