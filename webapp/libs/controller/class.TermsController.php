<?php
class TermsController extends Controller {
    public function control() {
        $this->setPageTitle('Terms of Service');
        $this->setViewTemplate('about.terms.tpl');
        return $this->generateView();
    }
}