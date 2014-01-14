<?php
chdir('..');
chdir('..');
require_once 'init.php';

$subscriber_dao = new SubscriberMySQLDAO();
$ids_to_uninstall = $subscriber_dao->getSubscribersInstalled($count=25);
$installer = new AppInstaller();

echo "Got ".sizeof($ids_to_uninstall)." members to uninstall.
";
while (sizeof($ids_to_uninstall) > 0) {
    foreach ($ids_to_uninstall as $id_to_uninstall) {
        echo "<ul>";
        try {
        	$results = $installer->uninstall($id_to_uninstall['id']);
        } catch (Exception $e) {
        	$results = '<li>' . $e->getMessage() . '</li>';
        }
        echo $results;
        echo "</ul>";
    }
    $ids_to_uninstall = $subscriber_dao->getSubscribersInstalled($count=25);
}

echo "<br><br>Uninstallation complete.";