<?php

class RouteUserController extends Controller {
    public function control() {
        $this->setViewTemplate('index.tpl');
        $this->addToView('greeting', 'Greetings, humans');
        return $this->generateView();
    }
}
