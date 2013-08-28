<?php
chdir('..');
require_once 'init.php';

$dao = new UserRouteMySQLDAO();
$ids_to_install = $dao->getRouteIDsNotInstalled($count=25);
$installer = new AppInstaller();

echo "Got ".sizeof($ids_to_install)."
";
while (sizeof($ids_to_install) > 0) {
    foreach ($ids_to_install as $id_to_install) {
        echo "<ul>";
        $installer->install($id_to_install['id'], true);
        echo "</ul>";
    }
    $ids_to_install = $dao->getRouteIDsNotInstalled($count=25);
}