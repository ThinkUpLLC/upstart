<?php

class Payment {
    /**
     * @var int Internal unique ID.
     */
    var $id;
    /**
     * @var str Time of transaction.
     */
    var $timestamp;
    /**
     * @var str Transaction ID of payment from Amazon.
     */
    var $transaction_id;
    /**
     * @var str Request ID of transaction, assigned by Amazon.
     */
    var $request_id;
    /**
     * @var str The status of the payment request.
     */
    var $transaction_status;
    /**
     * @var str Human readable message that specifies the reason for a request failure (optional).
     */
    var $status_message;
    /**
     * @var int Amount of payment in USD.
     */
    var $amount;
    /**
     * @var str Caller reference used for charge request.
     */
    var $caller_reference;
    /**
     * @var str Refund timestamp (if there was a refund).
     */
    var $refund_date;
    /**
     * @var str Refund caller reference (if there was one).
     */
    var $refund_caller_reference;
    /**
     * @var float Refund amount (if there was one).
     */
    var $refund_amount;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->timestamp = $row['timestamp'];
            $this->transaction_id = $row['transaction_id'];
            $this->request_id = $row['request_id'];
            $this->transaction_status = $row['transaction_status'];
            $this->status_message = $row['status_message'];
            $this->amount = $row['amount'];
            $this->caller_reference = $row['caller_reference'];
            $this->refund_date = $row['refund_date'];
            $this->refund_caller_reference = $row['refund_caller_reference'];
            $this->refund_amount = $row['refund_amount'];
        }
    }
}
