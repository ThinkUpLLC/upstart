<?php
chdir('..');
chdir('..');
require_once 'init.php';

/* BEGIN CONFIGURATION */

$INSTALLATION_CAP = 100;

/* END CONFIGURATION */

$subscriber_dao = new SubscriberMySQLDAO();
$ids_to_install = $subscriber_dao->getSubscribersNotInstalled($count=25);
$installer = new AppInstaller();

echo "Have ".sizeof($ids_to_install)." members to install.<br />";

$total_installed = 0;

if ($total_installed < $INSTALLATION_CAP) {
	while (sizeof($ids_to_install) > 0) {
	    foreach ($ids_to_install as $id_to_install) {
	        echo "<ul>";
	        try {
	        	$results = $installer->install($id_to_install['id']);
	    	} catch (Exception $e) {
	    		$results = '<li>' . $e->getMessage() .'</li>';
	        }
	    	echo $results;
	        echo "</ul>";
	    }
	    $total_installed += sizeof($ids_to_install);
	    $ids_to_install = $subscriber_dao->getSubscribersNotInstalled($count=25);
	}
}

echo "<br><br>Installation complete.";