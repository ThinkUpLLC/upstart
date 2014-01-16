<?php
chdir('..');
chdir('..');
require_once 'init.php';

/* BEGIN CONFIGURATION */

$CHARGE_CAP = 10;

/* END CONFIGURATION */

$subscriber_dao = new SubscriberMySQLDAO();
$total_members_to_charge = $subscriber_dao->getTotalSubscribersToCharge();
echo "<h1>".$total_members_to_charge." ThinkUp members have payments due</h1>";

echo '<form method="post"><input type="hidden" name="go" value="yes"><input type="submit" value="Charge Up To Next '.
$CHARGE_CAP.'" /></form>';


if ($_POST['go'] == 'yes') {
    try {
        $api_accessor = new AmazonFPSAPIAccessor();
        // Retrieve subscribers (with authorization info) who have authorizations but who do NOT have payments
        $ids_to_charge = $subscriber_dao->getSubscribersToCharge($CHARGE_CAP);
        $total_charged = 0;
        echo 'Charging '.
        (($CHARGE_CAP > $total_members_to_charge)?$total_members_to_charge:$CHARGE_CAP).' members...<br />';

        while ($total_charged < $CHARGE_CAP) {
            if (sizeof($ids_to_charge) > 0 ) {
                // Foreach subscriber, charge
                echo "<ul>";
                foreach ($ids_to_charge as $id_to_charge) {
                    echo "<li>";
                    $results = $api_accessor->invokeAmazonPayAction($id_to_charge['id'], $id_to_charge['token_id'],
                        $id_to_charge['amount']);
                    if ($results) {
                        echo 'Success charging '.$id_to_charge['email'];
                    } else {
                        echo 'Failure charging '.$id_to_charge['email'];
                    }
                    echo "</li>";
                    $results = null;
                }
                echo "</ul>";
            } else {
                break;
            }
            $total_charged += sizeof($ids_to_charge);
            $ids_to_charge = $subscriber_dao->getSubscribersToCharge($CHARGE_CAP);
        }

        echo "<br><br>Charged ".$total_charged." members.";
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
