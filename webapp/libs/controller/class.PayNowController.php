<?php
class PayNowController extends Controller {

    public function control() {
        $this->disableCaching();
        $this->setPageTitle('Pay now and get a free gift');
        $this->setViewTemplate('paynow.tpl');

        $new_subscriber_id = SessionCache::get('new_subscriber_id');
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByID($new_subscriber_id);
        $this->addToView('subscriber', $subscriber);
        Session::completeLogin($subscriber);
        $subscriber_dao->updateLastLogin($subscriber->email);

        //Process claim code
        if (isset($_POST['claim_code'])) {
            //Check if claim code is valid
            $claim_code_dao = new ClaimCodeMySQLDAO();
            //Strip spaces and go uppercase
            $code_str = str_replace(' ', '', strtoupper($_POST['claim_code']));
            $claim_code = $claim_code_dao->get($code_str);
            if (isset($claim_code)) {
                if ($claim_code->is_redeemed) {
                    $this->addErrorMessage('Whoops! It looks like that code has already been used.');
                } else {
                    //Send to confirm payment with code on the query string
                    return $this->redirect(UpstartHelper::getApplicationURL().'confirm-payment.php?code='.
                        $claim_code->code);
                }
            } else {
                $this->addErrorMessage("That code doesn't seem right. Check it and try again?");
            }
            SessionCache::unsetKey('claim_code');
        }
        $checkout_button = SubscriptionHelper::getCheckoutButton($subscriber);
        $this->addToView('checkout_button', $checkout_button);

        $cfg = Config::getInstance();
        $user_installation_url = $cfg->getValue('user_installation_url');
        $subscriber->installation_url = str_replace ("{user}", $subscriber->thinkup_username, $user_installation_url);
        $this->addToView('new_subscriber', $subscriber);
        $this->addToView('thinkup_url', $subscriber->installation_url);
        $this->addToView('claim_code',SessionCache::get('claim_code') );

        return $this->generateView();
    }
    /**
     * Send Location header
     * @param str $destination
     * @return bool Whether or not redirect header was sent
     */
    protected function redirect($destination=null) {
        if (!isset($destination)) {
            $destination = Utils::getSiteRootPathFromFileSystem();
        }
        $this->redirect_destination = $destination; //for validation
        if ( !headers_sent() ) {
            header('Location: '.$destination);
            return true;
        } else {
            return false;
        }
    }
}
