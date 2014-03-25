<?php
class FAQController extends Controller {
    public function control() {
        $this->setPageTitle('Frequently Asked Questions');
        $this->setViewTemplate('about.faq.tpl');
        return $this->generateView();
    }
}