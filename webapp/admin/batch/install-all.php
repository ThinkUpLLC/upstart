<?php
chdir('..');
chdir('..');
require_once 'init.php';

/* BEGIN CONFIGURATION */

$INSTALLATION_CAP = 50;

/* END CONFIGURATION */

$subscriber_dao = new SubscriberMySQLDAO();
$ids_to_install = $subscriber_dao->getSubscribersNotInstalled($count=25);
$installer = new AppInstaller();

$total_installed = 0;
echo 'Attempting to install '.$INSTALLATION_CAP.' members.<br />';

while ($total_installed < $INSTALLATION_CAP) {
	if (sizeof($ids_to_install) > 0 ) {
		echo "Have ".sizeof($ids_to_install)." members to install.<br />";
	    foreach ($ids_to_install as $id_to_install) {
	        echo "<ul>";
	        try {
	        	$results = $installer->install($id_to_install['id']);
	        	echo $results;
	    	} catch (Exception $e) {
	    		echo '<li>' . $e->getMessage() .'</li>';
	        }
	        echo "</ul>";
	    }
	    $total_installed += sizeof($ids_to_install);
	    $ids_to_install = $subscriber_dao->getSubscribersNotInstalled($count=25);
	} else {
		$total_installed = $INSTALLATION_CAP;
	}
}

echo "<br><br>Installation complete.";