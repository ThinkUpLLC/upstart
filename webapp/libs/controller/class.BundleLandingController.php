<?php
class BundleLandingController extends SignUpHelperController {
    /**
     * Name of the bundle
     * @var string
     */
    var $name = "The Good Web Bundle";
    /**
     * The total number of days of membership the user is purchasing.
     * @var integer
     */
    var $total_days = 365;
    /**
     * Price of the bundle.
     * @var integer
     */
    var $price = 96;

    public function control() {
        $this->setViewTemplate('bundle.landing.tpl');
        $this->disableCaching();
        $this->setPageTitle($this->name);
        $this->addToView('title', $this->name);

        // Figure out number of days left in countdown
        $today = date('z');
        $deadline = 370;
        if ($today > 6) { // we're still in 2014
            $days_to_go = $deadline - $today;
        } else {
            $days_to_go = 5 - $today;
        }
        $this->addToView('days_to_go', $days_to_go);

        return $this->generateView();
    }
    /**
     * Send bundle purchase confirmation email via Mandrill. If test, return the email HTML.
     * @param  str $email
     * @param  str $code
     * @param  str $code_readable
     * @return mixed str if test, otherwise bool
     */
    public function sendConfirmationEmail($email, $code, $code_readable) {
        $template_name = "Upstart System Messages";
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $subject = "Thanks for buying the Good Web Bundle!";

        $cfg = Config::getInstance();
        $api_key = $cfg->getValue('mandrill_api_key');

        $email_view_mgr->assign('claim_code', $code );
        $email_view_mgr->assign('claim_code_readable', $code_readable );
        $email_view_mgr->assign('headline', $subject );

        $message = $email_view_mgr->fetch('_email.bundle-purchase-confirmation.tpl');
        if (UpstartHelper::isTest()) {
            return $message;
        } else {
            return Mailer::mailHTMLViaMandrillTemplate($email, $subject, $template_name, array('html_body'=>$message),
                $api_key);
        }
    }
}