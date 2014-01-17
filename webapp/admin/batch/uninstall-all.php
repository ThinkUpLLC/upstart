<?php
/*
Commented out to make sure this script isn't invoked by accident at any time

chdir('..');
chdir('..');
require_once 'init.php';

$subscriber_dao = new SubscriberMySQLDAO();

$total_members_to_uninstall = $subscriber_dao->getTotalSubscribersToUninstall();
echo "<h1>".$total_members_to_uninstall." ThinkUp members have installations</h1>";

echo '<form method="post"><input type="hidden" name="go" value="yes"><input type="submit" value="Uninstall All" /></form>';

$ids_to_uninstall = $subscriber_dao->getSubscribersInstalled($count=25);
try {
    $installer = new AppInstaller();

    $total_uninstalled = 0;
    if ($_POST['go'] == 'yes') {
        while (sizeof($ids_to_uninstall) > 0) {
            foreach ($ids_to_uninstall as $id_to_uninstall) {
                echo "<ul>";
                try {
                	$results = $installer->uninstall($id_to_uninstall['id']);
                } catch (Exception $e) {
                	$results = '<li>' . $e->getMessage() . '</li>';
                }
                echo $results;
                $results = null;
                echo "</ul>";
            }
            $ids_to_uninstall = $subscriber_dao->getSubscribersInstalled($count=25);
            $total_uninstalled += sizeof($ids_to_uninstall);
        }
        echo "<br><br>Uninstalled ".$total_uninstalled." members";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
*/