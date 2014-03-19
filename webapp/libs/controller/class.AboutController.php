<?php
class AboutController extends Controller {
    public function control() {
        $this->setViewTemplate('about.index.tpl');
        return $this->generateView();
    }
}