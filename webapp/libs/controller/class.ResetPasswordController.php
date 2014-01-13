<?php
class ResetPasswordController extends Controller {

    public function control() {
        $session = new Session();
        $subscriber_dao = new SubscriberMySQLDAO();

        $this->setViewTemplate('user.resetpassword.tpl');
        $this->addHeaderJavaScript('assets/js/jqBootstrapValidation.js');
        $this->addHeaderJavaScript('assets/js/validate-fields.js');
        $this->disableCaching();

        $config = Config::getInstance();

        if (!isset($_GET['token']) || !preg_match('/^[\da-f]{32}$/', $_GET['token']) ||
        (!$subscriber = $subscriber_dao->getByPasswordToken($_GET['token']))) {
            // token is nonexistant or bad
            $this->addErrorMessage('You have reached this page in error.');
            return $this->generateView();
        }

        if (!$subscriber->validateRecoveryToken($_GET['token'])) {
            $this->addErrorMessage('Your token is expired.');
            return $this->generateView();
        }

        if (isset($_POST['password'])) {
            if ($_POST['password'] == $_POST['password_confirm']) {
                $is_valid_password = UpstartHelper::validatePassword($_POST['password']);
                if (!$is_valid_password) {
                    $this->addErrorMessage('Your password must be at least 8 characters, contain both numbers &amp; '.
                        'letters, and omit special characters.');
                } else {
                    $login_controller = new LoginController(true);
                    // Try to update the password
                    if ($subscriber_dao->updatePassword($subscriber->email, $_POST['password'] ) < 1 ) {
                        $login_controller->addErrorMessage('Oops! There was a problem changing your password.'.
                            ' Please try again.');
                    } else {
                        $subscriber_dao->activateSubscriber($subscriber->email);
                        $subscriber_dao->clearAccountStatus($subscriber->email);
                        $subscriber_dao->resetFailedLogins($subscriber->email);
                        $subscriber_dao->updatePasswordToken($subscriber->email, '');
                        $login_controller->addSuccessMessage('Nice! You have changed your password.');
                    }
                    return $login_controller->go();
                }
            } else {
                $this->addErrorMessage("The passwords must match.");
            }
        } else if (isset($_POST['Submit'])) {
            $this->addErrorMessage('Please enter a new password.');
        }
        return $this->generateView();
    }
}
