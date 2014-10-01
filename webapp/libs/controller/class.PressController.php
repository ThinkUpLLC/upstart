<?php
class PressController extends Controller {
    public function control() {
        $this->setPageTitle('Press');
        $this->setViewTemplate('about.press.tpl');
        return $this->generateView();
    }
}