<?php
/**
 * List users who have signed up for the waiting list.
 * @author gina
 */
class ListUserController extends Controller {
    public function control() {
        $this->setViewTemplate('admin-index.tpl');
        $dao = new UserRouteMySQLDAO();

        $page = (isset($_GET['p']))?(integer)$_GET['p']:1;
        if ($page < 1) {
            $page =1;
        }
        $users = $dao->getUserList($page);
        $this->addToView('users', $users);
        $this->addToView('page', $page);
        return $this->generateView();
    }
}