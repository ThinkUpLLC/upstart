<?php
class BookController extends Controller {
    public function control() {
        $this->setPageTitle('Insights: Interviews about the Future of Social Media ');
        $this->setViewTemplate('about.book.tpl');
        return $this->generateView();
    }
}