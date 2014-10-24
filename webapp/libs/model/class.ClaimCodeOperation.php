<?php

class ClaimCodeOperation {
    /**
     * @var int Internal unique ID.
     */
    var $id;
    /**
     * @var str Timestamp of insertion.
     */
    var $timestamp;
    /**
     * @var str Amazon transaction ID.
     */
    var $transaction_id;
    /**
     * @var str Operation ID of code purchase.
     */
    var $operation_id;
    /**
     * @var str Amazon's buyer email address.
     */
    var $buyer_email;
    /**
     * @var str Amazon's buyer name.
     */
    var $buyer_name;
    /**
     * @var str Amount of transaction.
     */
    var $transaction_amount;
    /**
     * @var str Transaction status code.
     */
    var $status_code;
    /**
     * @var str Purchase association - bundle, gift, etc.
     */
    var $type;
    /**
     * @var int How many days of membership this code represents.
     */
    var $number_days;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->timestamp = $row['timestamp'];
            $this->transaction_id = $row['transaction_id'];
            $this->operation_id = $row['operation_id'];
            $this->buyer_email = $row['buyer_email'];
            $this->buyer_name = $row['buyer_name'];
            $this->transaction_amount = $row['transaction_amount'];
            $this->status_code = $row['status_code'];
            $this->type = $row['type'];
            $this->number_days = $row['number_days'];
        }
    }
}