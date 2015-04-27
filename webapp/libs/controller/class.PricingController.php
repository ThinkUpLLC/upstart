<?php
/**
 * Create various pricing level interfaces to Twitter and Facebook.
 */
class PricingController extends SignUpHelperController {
    public function control() {
        $this->setViewTemplate('pricing.tpl');

        $twitter_member_link = $this->getTwitterAuthLink('register.php?n=twitter&level=member');
        $this->addToView('twitter_member_link', $twitter_member_link);
        $facebook_member_link = $this->getFacebookConnectLink('register.php?n=facebook&level=member');
        $this->addToView('facebook_member_link', $facebook_member_link);

        if (SessionCache::isKeySet('auth_error_message')) {
            $error_message = SessionCache::get('auth_error_message');
            $this->addErrorMessage($error_message);
            SessionCache::unsetKey('auth_error_message');
        }

        return $this->generateView();
    }
}
