<?php
chdir('..');
require_once 'init.php';

$subscriber_dao = new SubscriberMySQLDAO();
$ids_to_install = $subscriber_dao->getSubscribersNotInstalled($count=25);
$installer = new AppInstaller();

echo "Got ".sizeof($ids_to_install)."
";
while (sizeof($ids_to_install) > 0) {
    foreach ($ids_to_install as $id_to_install) {
        echo "<ul>";
        $results = $installer->install($id_to_install['id']);
        echo $results;
        echo "</ul>";
    }
    $ids_to_install = $subscriber_dao->getSubscribersNotInstalled($count=25);
}