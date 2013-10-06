<?php
class Authorization {
    /**
     * @var int Internal unique ID.
     */
    var $id;
    /**
     * @var str Time of transaction.
     */
    var $timestamp;
    /**
     * @var str Token ID of transaction.
     */
    var $token_id;
    /**
     * @var int Monetary amount of transaction in US Dollars.
     */
    var $amount;
    /**
     * @var str The status of the transaction request.
     */
    var $status_code;
    /**
     * @var str Human readable message that specifies the reason for a request failure (optional).
     */
    var $error_message;
    /**
     * @var str Payment method expiration date (optional).
     */
    var $payment_method_expiry;
    /**
     * @var str Caller reference used for authorization request.
     */
    var $caller_reference;
    /**
     * @var str Recurrence period of payment authorization.
     */
    var $recurrence_period;
    /**
     * @var date Date the token becomes valid.
     */
    var $token_validity_start_date;
    /**
     * Status codes
     */
    public static $status_codes = array('SA', 'SB', 'SC', 'SE', 'A', 'CE', 'PE', 'NP', 'NM');
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->timestamp = $row['timestamp'];
            $this->token_id = $row['token_id'];
            $this->amount = $row['amount'];
            $this->status_code = $row['status_code'];
            $this->error_message = $row['error_message'];
            $this->payment_method_expiry = $row['payment_method_expiry'];
            $this->caller_reference = $row['caller_reference'];
            $this->recurrence_period = $row['recurrence_period'];
            $this->token_validity_start_date = $row['token_validity_start_date'];
        }
    }
}