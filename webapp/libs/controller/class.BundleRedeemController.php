<?php
class BundleRedeemController extends SignUpHelperController {
    public function control() {
        $this->setViewTemplate('bundle.redeem.tpl');
        $this->disableCaching();

        $twitter_member_link = $this->getTwitterAuthLink('register.php?n=twitter&level=member');
        $this->addToView('twitter_member_link', $twitter_member_link);
        $facebook_member_link = $this->getFacebookConnectLink('register.php?n=facebook&level=member');
        $this->addToView('facebook_member_link', $facebook_member_link);
        return $this->generateView();
    }
}