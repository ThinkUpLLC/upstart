<?php
class InstallApplicationController extends Controller {

    public function control() {
        $this->setContentType('text/html; charset=UTF-8');
        $this->setViewTemplate('install.run-top.tpl');
        echo $this->generateView();

        if (isset($_GET['id'])) {
            $installer = new AppInstaller();
            $installer->install($_GET['id'], true);
        } else {
            self::output('No user route specified');
        }
        $this->setViewTemplate('install.run-bottom.tpl');
        echo $this->generateView();
    }
}