<?php
class SubscriptionOperation {
    /**
     * @var int Internal unique ID.
     */
    var $id;
    /**
     * @var str Timestamp of insertion.
     */
    var $timestamp;
    /**
     * @var int Subscriber ID.
     */
    var $subscriber_id;
    /**
     * @var str Operation performed on Amazon.
     */
    var $operation;
    /**
     * @var str Reason for payment.
     */
    var $payment_reason;
    /**
     * @var str Amount of transaction.
     */
    var $transaction_amount;
    /**
     * @var str How often subscription recurs, 1 month or 12 months.
     */
    var $recurring_frequency;
    /**
     * @var str Transaction status code.
     */
    var $status_code;
    /**
     * @var str Amazon's buyer email address.
     */
    var $buyer_email;
    /**
     * @var str Caller reference for transaction.
     */
    var $reference_id;
    /**
     * @var str Amazon's subscription ID.
     */
    var $amazon_subscription_id;
    /**
     * @var str Amazon's transaction date.
     */
    var $transaction_date;
    /**
     * @var str Amazon's buyer name.
     */
    var $buyer_name;
    /**
     * @var str Payment method.
     */
    var $payment_method;

    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->timestamp = $row['timestamp'];
            $this->subscriber_id = $row['subscriber_id'];
            $this->operation = $row['operation'];
            $this->payment_reason = $row['payment_reason'];
            $this->transaction_amount = $row['transaction_amount'];
            $this->recurring_frequency = $row['recurring_frequency'];
            $this->status_code = $row['status_code'];
            $this->buyer_email = $row['buyer_email'];
            $this->reference_id = $row['reference_id'];
            $this->amazon_subscription_id = $row['amazon_subscription_id'];
            $this->transaction_date = intval($row['transaction_date']);
            $this->buyer_name = $row['buyer_name'];
            $this->payment_method = $row['payment_method'];
        }
    }
}