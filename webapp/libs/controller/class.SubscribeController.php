<?php
/**
 * Create interface to Twitter and Facebook
 */
class SubscribeController extends SignUpHelperController {
    public function control() {
        // $this->disableCaching(); //Don't cache because click ID/Amazon caller reference must be unique per user
        $this->setViewTemplate('subscribe.tpl');
        $twitter_member_link = $this->getTwitterAuthLink('register.php?n=twitter&level=member');
        $this->addToView('twitter_member_link', $twitter_member_link);
        $facebook_member_link = $this->getFacebookConnectLink('register.php?n=facebook&level=member');
        $this->addToView('facebook_member_link', $facebook_member_link);

        $twitter_pro_link = self::getTwitterAuthLink('register.php?n=twitter&level=pro');
        $this->addToView('twitter_pro_link', $twitter_pro_link);
        $facebook_pro_link = self::getFacebookConnectLink('register.php?n=facebook&level=pro');
        $this->addToView('facebook_pro_link', $facebook_pro_link);

        if (SessionCache::isKeySet('auth_error_message')) {
            $error_message = SessionCache::get('auth_error_message');
            $this->addErrorMessage($error_message);
            SessionCache::unsetKey('auth_error_message');
        }

        return $this->generateView();
    }
}
