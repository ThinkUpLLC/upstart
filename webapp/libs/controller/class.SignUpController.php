<?php
/**
 * Parent controller for signing up for ThinkUp via waiting list or subscribing.
 * @author gina
 *
 */
abstract class SignUpController extends Controller {
    /*
     * Subscription level names
     */
    public static $subscription_levels = array('member'=>60, 'developer'=>120, 'executive'=>996);
    /**
     * Verify posted email address input and add appropriate error message if not
     * return bool
     */
    protected function isEmailInputValid() {
        if (isset($_POST['email']) && empty($_POST['email'])) {
            $this->addErrorMessage('Please enter your email address.', 'email');
        }
        $is_valid_address = false;
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $is_valid_address = UpstartHelper::validateEmail($_POST['email']);
            if (!$is_valid_address) {
                $this->addErrorMessage('Please enter a valid email address.', 'email');
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
            $this->addErrorMessage('Please enter a password.', 'password');
        }
        $is_valid_password = false;
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $is_valid_password = UpstartHelper::validatePassword($_POST['password']);
            if (!$is_valid_password) {
                $this->addErrorMessage('Password must be at least 8 characters and contain both numbers and letters.',
                'password');
            }
        }
        return (isset($_POST['password']) && $is_valid_password);
    }

    protected function logError($method, $extra_debug = null) {
        exec('git rev-parse --verify HEAD 2> /dev/null', $output);
        $commit_hash = $output[0];
        $debug = "SESSION:
".Utils::varDumpToString($_SESSION)."

GET:
".Utils::varDumpToString($_GET)."

POST:
".Utils::varDumpToString($_POST)."

Notes:
".$extra_debug;
        $error_dao = new ErrorLogMySQLDAO();
        $error_dao->insert($commit_hash, $method, $debug);
    }

}