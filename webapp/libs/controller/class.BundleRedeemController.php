<?php
class BundleRedeemController extends SignUpHelperController {
    public function control() {
        $this->setViewTemplate('bundle.redeem.tpl');
        $this->disableCaching();

        $code_string = (isset($_GET['code']))?('&code='.$_GET['code']):'';
        $twitter_member_link = $this->getTwitterAuthLink('register.php?n=twitter&level=member'.$code_string);
        $this->addToView('twitter_member_link', $twitter_member_link);
        $facebook_member_link = $this->getFacebookConnectLink('register.php?n=facebook&level=member'.$code_string);
        $this->addToView('facebook_member_link', $facebook_member_link);
        return $this->generateView();
    }
}