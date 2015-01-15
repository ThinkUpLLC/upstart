<?php
/**
 * Charge annual subscribers who have a payment due.
 */
class ChargeAnnualSubscribersController extends Controller {

    var $charge_limit = 2;

    public function control() {
        //Number of subscribers to charge per loop
        $sizeof_rowset = 50;
        $subscriber_dao = new SubscriberMySQLDAO();
        $total_members_to_charge = $subscriber_dao->getTotalAnnualSubscribersToCharge();
        $this->addToView('total_members_to_charge', $total_members_to_charge);
        $this->addToView('charge_limit', $this->charge_limit);

        echo "<h1>".$total_members_to_charge." ThinkUp members have payments due</h1>";
        if ($total_members_to_charge > 0) {
            echo '<form method="post"><input type="hidden" name="go" value="yes">'.
                '<input type="submit" value="Charge Up To Next '.$this->charge_limit.'" /></form>';
        }

        if ($_POST['go'] == 'yes') {
            $api_accessor = new AmazonFPSAPIAccessor($use_deprecated_tokens = true);
            // Retrieve subscribers (with authorization info) who have authorizations but who do NOT have payments
            $ids_to_charge = $subscriber_dao->getAnnualSubscribersToCharge($sizeof_rowset);
            $total_charged = 0;
            echo 'Charging '.(($sizeof_rowset > $total_members_to_charge)?$total_members_to_charge:$sizeof_rowset)
                .' members...<br />';

            $subscription_helper = new SubscriptionHelper();
            while ($total_charged < $this->charge_limit) {
                if (sizeof($ids_to_charge) > 0 ) {
                    // Foreach subscriber, charge
                    echo "<ul>";
                    foreach ($ids_to_charge as $sub) {
                        $amount = $subscription_helper->getNextAnnualChargeAmount($sub['membership_level']);
                        echo "<li>";
                        try {
                            echo 'Charging '.$sub['email'].' '.$amount.' for '.$sub['membership_level']."<br>";
                            $results = $api_accessor->invokeAmazonPayAction($sub['id'], $sub['token_id'], $amount);
                        } catch (Exception $e) {
                            echo 'Error: '.$e->getMessage();
                        }
                        if ($results) {
                            echo 'Success charging '.$sub['email'];
                        } else {
                            echo 'Failure charging '.$sub['email'];
                        }
                        echo "</li>";
                        $subscriber = $subscriber_dao->getByID($sub['id']);
                        $subscriber_dao->updateSubscriberSubscriptionDetails($subscriber);
                        $results = null;
                    }
                    echo "</ul>";
                } else {
                    break;
                }
                flush();
                $total_charged += sizeof($ids_to_charge);
                $ids_to_charge = $subscriber_dao->getAnnualSubscribersToCharge($sizeof_rowset);
            }
            echo "<br><br>Charged ".$total_charged." members.";
        }
    }
}
