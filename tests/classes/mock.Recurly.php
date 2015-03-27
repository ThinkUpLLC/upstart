<?php

class Recurly_Client {
   /**
	* @str
	*/
	public static $subdomain;
   /**
	* @str
	*/
	public static $apiKey;
}

class Recurly_Subscription {
    var $account;

    var $uuid;

    public function create() {
        if ($this->account->billing_info->amazon_billing_agreement_id !== 'billing-id-success') {
            throw new Recurly_ValidationError('This is the error message from Recurly');
        }
        $this->uuid = time().rand();
    }
}

class Recurly_Account {
    var $billing_info;

}

class Recurly_BillingInfo {
    var $amazon_billing_agreement_id;
}

class Recurly_ValidationError extends Exception {}