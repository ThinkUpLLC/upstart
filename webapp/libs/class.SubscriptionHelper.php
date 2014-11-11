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
     * - Payment due (For non-recurring and complimentary memberships which have expired)
     * - Authorization pending
     * - Authorization failed
     * - Refunded
     * @param Subscriber $subscriber
     * @return arr 'subscription_status'=>$status, 'paid_through'=>$paid_through_time
     */
    public function getSubscriptionStatusAndPaidThrough(Subscriber $subscriber) {
        $subscription_status = "";
        $paid_through_time = null;
        if ($subscriber->is_membership_complimentary) {
            $subscription_status = "Complimentary membership";
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
                } elseif (strtotime($subscriber->creation_time) > strtotime('-16 days') /* give extra 2 days */) {
                    $subscription_status = "Free trial";
                } else {
                    $subscription_status = "Payment due";
                }
            }
        }
        if (isset($paid_through_time)) {
            $paid_through_time = date('Y-m-d H:i:s', $paid_through_time);
        }
        return array("subscription_status"=>$subscription_status, "paid_through"=>$paid_through_time);
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
            $paid_through_time = strtotime('+1 month', strtotime($operation->transaction_date));
            $paid_through_time = date('Y-m-d H:i:s', $paid_through_time);
        }
        $result += $subscriber_dao->setPaidThrough($subscriber->id, $paid_through_time);
        return ($result > 0); //return true if a field got updated
    }
}