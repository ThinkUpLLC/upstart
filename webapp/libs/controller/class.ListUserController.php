<?php
/**
 * List users who have signed up for the waiting list.
 * @author gina
 */
class ListUserController extends Controller {
    public function control() {
        $this->setViewTemplate('admin-index.tpl');

        $page = (isset($_GET['p']))?(integer)$_GET['p']:1;
        if ($page < 1) {
            $page =1;
        }
        $dao = new UserRouteMySQLDAO();
        $users = $dao->getUserList($page, 51);
        $this->addToView('users', $users);
        $total = $dao->getListTotal();
        $this->addToView('total', $total);
        $this->addToView('page', $page);
        if (sizeof($users) == 51) {
            array_pop($users);
            $this->addToView('next_page', $page+1);
        }
        if ($page > 1) {
            $this->addToView('prev_page', $page-1);
        }

        return $this->generateView();
    }
}