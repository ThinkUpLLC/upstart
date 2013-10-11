<?php
class ListErrorController extends Controller {
    public function control() {
        $this->setViewTemplate('admin-errorlog.tpl');

        $page = (isset($_GET['p']))?(integer)$_GET['p']:1;
        if ($page < 1) {
            $page =1;
        }
        $errorlog_dao = new ErrorLogMySQLDAO();
        $raw_errors = $errorlog_dao->getErrorList($page, 51);
        $errors = array();
        foreach ($raw_errors as $error) {
            $path_parts = explode( '/webapp/', $error['filename'] );
            $relative_path = $path_parts[1];
            $error['github_link'] = 'https://github.com/ThinkUpLLC/upstart/blob/'.$error['commit_hash'].'/webapp/'.
            $relative_path.'#L'.$error['line_number'];
            array_push($errors, $error);
        }
        $this->addToView('errors', $errors);

        $this->addToView('page', $page);
        if (sizeof($subscribers) == 51) {
            array_pop($subscribers);
            $this->addToView('next_page', $page+1);
        }
        if ($page > 1) {
            $this->addToView('prev_page', $page-1);
        }
        return $this->generateView();
    }
}