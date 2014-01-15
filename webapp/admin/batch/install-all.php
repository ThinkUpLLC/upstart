<?php
chdir('..');
chdir('..');
require_once 'init.php';

/* BEGIN CONFIGURATION */

$INSTALLATION_CAP = 50;

/* END CONFIGURATION */

$subscriber_dao = new SubscriberMySQLDAO();
$total_members_to_install = $subscriber_dao->getTotalSubscribersToInstall();
echo "<h1>".$total_members_to_install." ThinkUp members are ready for their installation</h1>";

echo '<form method="post"><input type="hidden" name="go" value="yes"><input type="submit" value="Install Up To Next '.
$INSTALLATION_CAP.'" /></form>';


if ($_POST['go'] == 'yes') {
	try {
		$installer = new AppInstaller();
		$ids_to_install = $subscriber_dao->getSubscribersNotInstalled($count=25);
		$total_installed = 0;
		echo 'Installing '.
		(($INSTALLATION_CAP > $total_members_to_install)?$total_members_to_install:$INSTALLATION_CAP).' members...<br />';

		while ($total_installed < $INSTALLATION_CAP) {
			if (sizeof($ids_to_install) > 0 ) {
			    foreach ($ids_to_install as $id_to_install) {
			        echo "<ul>";
			        try {
			        	$results = $installer->install($id_to_install['id']);
			        	echo $results;
			    	} catch (Exception $e) {
			    		echo '<li>' . $e->getMessage() .'</li>';
			        }
			        echo "</ul>";
			        $results = null;
			    }
			    $total_installed += sizeof($ids_to_install);
			    $ids_to_install = $subscriber_dao->getSubscribersNotInstalled($count=25);
			} else {
				$total_installed = $INSTALLATION_CAP;
			}
		}

		echo "<br><br>Installed ".$total_installed." members.";
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}
