<?php
class PrivacyController extends Controller {
    public function control() {
        $this->setPageTitle('Privacy Policy');
        $this->setViewTemplate('about.privacy.tpl');
        return $this->generateView();
    }
}