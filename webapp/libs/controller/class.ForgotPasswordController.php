<?php
class ForgotPasswordController extends Controller {

    public function control() {
        $config = Config::getInstance();

        if (isset($_POST['Submit']) && $_POST['Submit'] == 'Send Reset') {
            $this->disableCaching();

            $subscriber_dao = new SubscriberMySQLDAO();
            $subscriber = $subscriber_dao->getByEmail($_POST['email']);
            if (isset($subscriber)) {
                $token = $subscriber->setPasswordRecoveryToken();

                $email_view_mgr = new ViewManager();
                $email_view_mgr->caching=false;

                $email_view_mgr->assign('app_title', "ThinkUp.com" );
                $email_view_mgr->assign('recovery_url', "user/reset.php?token=$token");
                $email_view_mgr->assign('application_url', UpstartHelper::getApplicationURL(false));
                $email_view_mgr->assign('site_root_path', $config->getValue('site_root_path') );
                $message = $email_view_mgr->fetch('_email.forgotpassword.tpl');

                Mailer::mail($_POST['email'], "ThinkUp Password Recovery", $message);

                $this->addSuccessMessage('Check your email! Password recovery information has been sent.');
            } else {
                $this->addErrorMessage('Member does not exist.');
            }
        }
        $this->setViewTemplate('user.forgot.tpl');
        $this->addHeaderJavaScript('assets/js/jqBootstrapValidation.js');
        $this->addHeaderJavaScript('assets/js/validate-fields.js');

        return $this->generateView();
    }
}