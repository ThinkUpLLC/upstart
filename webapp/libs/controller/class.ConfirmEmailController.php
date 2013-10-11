<?php
class ConfirmEmailController extends Controller {
    /**
     * Required query string parameters
     * @var array usr = instance email address, code = activation code
     */
    var $REQUIRED_PARAMS = array('usr', 'code');
    /**
     *
     * @var boolean
     */
    var $is_missing_param = false;
    /**
     * Constructor
     * @param bool $session_started
     * @return ActivateAccountController
     */
    public function __construct($session_started=false) {
        parent::__construct($session_started);
        foreach ($this->REQUIRED_PARAMS as $param) {
            if (!isset($_GET[$param]) || $_GET[$param] == '' ) {
                $this->is_missing_param = true;
            }
        }
    }
    public function control() {
        $this->setViewTemplate('confirm.tpl');
        $generic_error_msg = '<strong>Oops!</strong> Something went wrong.<br> '.
        'Sorry for the trouble. Please try again, or '.
        '<a href="mailto:help@thinkup.com">contact us</a> with questions.';

        if ($this->is_missing_param) {
            $this->addErrorMessage($generic_error_msg);
        } else {
            $subscriber_dao = new SubscriberMySQLDAO();
            $verification_code = $subscriber_dao->getVerificationCode($_GET['usr']);

            if ($_GET['code'] == $verification_code['verification_code']) {
                $subscriber = $subscriber_dao->getByEmail($_GET['usr']);
                if (isset($subscriber) && isset($subscriber->is_email_verified)) {
                    $subscriber_dao->verifyEmailAddress($_GET['usr']);
                    $this->addSuccessMessage("Success! Your email has been verified.");
                } else {
                    $this->addErrorMessage('Houston, we have a problem: Email address not found.');
                }
            } else {
                $this->addErrorMessage('Houston, we have a problem: Email confirmation failed. Please try again or '.
                '<a href="mailto:help@thinkup.com">contact us</a> with questions.');
            }
        }
        return $this->generateView();
    }
}
