<?php
class ContactController extends Controller {
    public function control() {
        $this->setViewTemplate('contact.index.tpl');
        return $this->generateView();
    }
}