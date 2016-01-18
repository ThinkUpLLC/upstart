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
        switch ($this->account->billing_info->amazon_billing_agreement_id) {
            case 'billing-id-success':
                break;
            case 'billing-id-error':
                throw new Recurly_ValidationError('This is a general error message from Recurly');
                break;
            case 'billing-id-fullname-error':
                throw new Recurly_ValidationError('Amazon billing agreement id Billing Agreement B01-blahblah '.
                    ' is currently in Draft state. ValidateBillingAgreement can only be requested in the OPEN '.
                    'state, first name can\'t be blank.');
                break;
            default:
                break;
        }
        $this->uuid = time().rand();
    }

    public function terminateAndPartialRefund() {
        return true;
    }

    public static function get($uuid, $client = null) {
        if ($uuid == 'abc-valid') {
            return new Recurly_Subscription();
        } elseif ($uuid == 'abc-invalid') {
            throw new Recurly_NotFoundError('Subscription not found');
        }
    }
}

class Recurly_Account {
    var $billing_info;

}

class Recurly_BillingInfo {
    var $amazon_billing_agreement_id;
}

class Recurly_ValidationError extends Exception {}

class Recurly_SubscriptionList {
    public static function getForAccount($id) {
        if ($id == 101) {
            throw new Recurly_NotFoundError("Couldn't find Account with account_code = ".$id);
        } else {
            $sub = new Recurly_Subscription();
            $sub->state = 'active';
            return array($sub);
        }
    }
}

class Recurly_Error extends Exception {}

class Recurly_NotFoundError extends Recurly_Error {}
