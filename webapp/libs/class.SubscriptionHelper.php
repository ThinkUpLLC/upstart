<?php

class SubscriptionHelper {
    /**
     * Get a valid subscription_status and paid_through date for a given subscriber.
     * Possible status values:
     * - Free trial
     * - Paid (Display as: Paid through %paid_through)
     * - Complimentary membership
     * - Payment pending
     * - Payment failed
     * - Payment due (For expired non-recurring and comp memberships, or manual overrides for up/downgrades)
     * - Authorization pending
     * - Authorization failed
     * - Refunded
     * @param Subscriber $subscriber
     * @return arr 'subscription_status'=>$status, 'paid_through'=>$paid_through_time
     */
    public function getSubscriptionStatusAndPaidThrough(Subscriber $subscriber) {
        $subscription_status = "";
        $paid_through_time = null;
        $subscription_recurrence = null;
        if ($subscriber->is_membership_complimentary) {
            //Complimentary memberships
            $subscription_status = "Complimentary membership";
        } elseif (isset($subscriber->claim_code) and isset($subscriber->paid_through)) {
            //Members with redeemed claim codes
            $subscription_status = "Paid";
            $paid_through_time = strtotime($subscriber->paid_through);
            $subscription_recurrence = "None";
        } elseif ($subscriber->subscription_status == 'Payment due') {
            //Don't overwrite Payment due statuses; they may have been set manually in Upstart
            $subscription_status = "Payment due";
        } else {
            //Get latest subscription operation
            $sub_op_dao = new SubscriptionOperationMySQLDAO();
            $latest_operation = $sub_op_dao->getLatestOperation($subscriber->id);
            if (isset($latest_operation)) {
                $subscription_status = self::getSubscriptionStatusBasedOnOperation($latest_operation);
                if ($latest_operation->operation == 'pay'
                    && ($latest_operation->status_code == 'SS' || $latest_operation->status_code == 'PS')) {
                    $paid_through_time = strtotime('+'.$latest_operation->recurring_frequency,
                        strtotime($latest_operation->transaction_date));
                }
                $subscription_recurrence = $latest_operation->recurring_frequency;
            } else {
                //Get latest payment
                $subscriber_payment_dao = new SubscriberPaymentMySQLDAO();
                $latest_payment = $subscriber_payment_dao->getBySubscriber($subscriber->id, 1);
                if (sizeof($latest_payment) > 0) {
                    $latest_payment = $latest_payment[0];
                } else {
                    $latest_payment = null;
                }
                if ( $latest_payment !== null ) {
                    if ( $latest_payment['transaction_status'] == 'Success') {
                        $paid_through_time = strtotime('+1 year', strtotime($latest_payment['timestamp']));
                        $subscription_status = "Paid";
                    } elseif ( $latest_payment['transaction_status'] == 'Pending') {
                        $subscription_status = "Payment pending";
                    } elseif ( $latest_payment['transaction_status'] == 'Failure') {
                        $subscription_status = "Payment failed";
                    } else {
                        $subscription_status = "Payment failed";
                    }
                    $subscription_recurrence = '12 months';
                // } elseif (strtotime($subscriber->creation_time) > strtotime('-16 days') /* give extra 2 days */) {
                //     $subscription_status = "Free trial";
                } else {
                    $subscription_status = "Free trial";
                }
            }
        }
        if (isset($paid_through_time)) {
            $paid_through_time = date('Y-m-d H:i:s', $paid_through_time);
        }
        return array("subscription_status"=>$subscription_status, "paid_through"=>$paid_through_time,
            "subscription_recurrence"=>$subscription_recurrence);
    }
    /**
     * Get subscriber.subscription_status based on the last subscription operation details.
     * @param  SubscriptionOperation $operation
     * @return str Paid, Payment failed, Payment pending, Refunded
     */
    public static function getSubscriptionStatusBasedOnOperation(SubscriptionOperation $operation) {
        if ($operation->operation == 'pay') {
            if ($operation->status_code == 'SS' || $operation->status_code == 'PS') {
                return "Paid";
            } elseif ($operation->status_code == 'SF' || $operation->status_code == 'PF') {
                return "Payment failed";
            } elseif ($operation->status_code == 'SI' || $operation->status_code == 'PI') {
                return "Payment pending";
            }
        } elseif ($operation->operation == 'refund') {
            return "Refunded";
        } else {
            //@TODO handle other actions!
            return '';
        }
    }
    /**
     * Update a subscriber's subscription_status and paid_through date based on an operation.
     * @param  Subscriber            $subscriber
     * @param  SubscriptionOperation $operation
     * @return bool
     */
    public function updateSubscriptionStatusAndPaidThrough(Subscriber $subscriber, SubscriptionOperation $operation) {
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscription_status = self::getSubscriptionStatusBasedOnOperation($operation);
        $result = $subscriber_dao->setSubscriptionStatus($subscriber->id, $subscription_status);

        $paid_through_time = null;
        if ($operation->status_code == 'SS' || $operation->status_code == 'PS') {
            $paid_through_time = strtotime('+'.$operation->recurring_frequency,
                strtotime($operation->transaction_date));
            $paid_through_time = date('Y-m-d H:i:s', $paid_through_time);
        }
        $result += $subscriber_dao->setPaidThrough($subscriber->id, $paid_through_time);
        $result += $subscriber_dao->setSubscriptionRecurrence($subscriber->id, $operation->recurring_frequency);
        return ($result > 0); //return true if a field got updated
    }

    public function getNextAnnualChargeAmount($membership_level) {
        switch ($membership_level) {
            case "Early Bird":
                return 50;
            case "Late Bird":
                return 50;
            case "Member":
                return 50;
            case "Pro":
                return 120;
            case "Exec":
                return 996;
        }
        return 0;
    }

    public static function getCheckoutButton($subscriber) {
        $normalized_membership_level = strtolower($subscriber->membership_level);
        $normalized_membership_level =
            ($normalized_membership_level == 'late bird')?'member':$normalized_membership_level;
        $normalized_membership_level =
            ($normalized_membership_level == 'early bird')?'member':$normalized_membership_level;
        $normalized_membership_level =
            ($normalized_membership_level == 'exec')?'executive':$normalized_membership_level;

        if ($subscriber->subscription_recurrence == '12 months') {
            $amount_recurrence = '12 months discount';
        } elseif ($subscriber->subscription_recurrence == 'None') {
            $amount_recurrence = '1 month';
        } else {
            $amount_recurrence = $subscriber->subscription_recurrence;
        }
        $amount = SignUpHelperController::$subscription_levels[strtolower($normalized_membership_level)]
            [$amount_recurrence];
        $button_freq_label = $subscriber->subscription_recurrence;
        $button_freq_label = str_replace("None", "month", $button_freq_label);
        $button_freq_label = str_replace("1 month", "month", $button_freq_label);
        $button_freq_label = str_replace("12 months", "year", $button_freq_label);

        $site_root_path = Config::getInstance()->getValue('site_root_path');

        return '<form method="POST" action="'.$site_root_path.'checkout.php"> <button type="submit" '
            .'class="btn-pill-large has-note">Subscribe Now<br><small>Just '.$amount
            .' bucks a '.$button_freq_label.'</small></button><br><br></form>';
    }
}