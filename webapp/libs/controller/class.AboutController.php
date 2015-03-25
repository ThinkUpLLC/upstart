<?php
class AboutController extends Controller {
    public function control() {
        $this->setViewTemplate('about.index.tpl');
        $this->setPageTitle('About');
        return $this->generateView();
    }
}