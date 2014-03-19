<?php
class ValuesController extends Controller {
    public function control() {
        $this->setPageTitle('Values');
        $this->setViewTemplate('about.values.tpl');
        return $this->generateView();
    }
}