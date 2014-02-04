<?php
class AboutController extends Controller {
    public function control() {
        $this->setViewTemplate('about.tpl');
        if ($this->shouldRefreshCache() ) {
            $today = date('z');
            $deadline = date('z', mktime(12, 0, 0, 11, 16, 2013));
            $days_to_go = $deadline - $today;
            $this->addToView('days_to_go', $days_to_go);
        }
        return $this->generateView();
    }
}