<?php
class LandingOriginalController extends Controller {
    public function control() {
        $this->setViewTemplate('landing-original.tpl');
        return $this->generateView();
    }
}