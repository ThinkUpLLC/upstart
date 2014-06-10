<?php
class Subscriber {
    /**
     * @var int Internal unique ID.
     */
    var $id;
    /**
     * @var str Subscriber email address.
     */
    var $email;
    /**
     * @var str Subscriber password.
     */
    var $pwd;
    /**
     * @var str Subscriber password salt.
     */
    var $pwd_salt;
    /**
     * @var str Time of subscription.
     */
    var $creation_time;
    /**
     * @var str Subscriber's network user ID.
     */
    var $network_user_id;
    /**
     * @var str Subscriber's network username.
     */
    var $network_user_name;
    /**
     * @var str Subscriber's authorized network, ie, Twitter or Facebook.
     */
    var $network;
    /**
     * @var str Subscriber's full name (as specified on network).
     */
    var $full_name;
    /**
     * @var int Follower or subscriber count of service user.
     */
    var $follower_count = 0;
    /**
     * @var bool Whether or not the service user is verified.
     */
    var $is_verified = false;
    /**
     * @var str OAuth access token for network authorization.
     */
    var $oauth_access_token;
    /**
     * @var str OAuth secret access token for network authorization.
     */
    var $oauth_access_token_secret = '';
    /**
     * @var int Code for verifying email address.
     */
    var $verification_code;
    /**
     * @var bool Whether or not email address has been verified, 1 or 0.
     */
    var $is_email_verified = false;
    /**
     * @var bool Whether or not subscriber was on waitlist (1 if so, 0 if not).
     */
    var $is_from_waitlist = false;
    /**
     * @var str Subscriber's membership level (Member, Pro, Exec, Early Bird, etc).
     */
    var $membership_level;
    /**
     * @var bool Whether or not the membership is complimentary, ie, free/not paid for.
     */
    var $is_membership_complimentary = false;
    /**
     * @var str ThinkUp username.
     */
    var $thinkup_username;
    /**
     * @var str Installation start time.
     */
    var $date_installed;
    /**
     * @var str API key for authorizing on installation.
     */
    var $api_key_private;
    /**
     * @var str Last time this installation was dispatched for crawl.
     */
    var $last_dispatched;
    /**
     * @var str Git commit hash of installation version.
     */
    var $commit_hash;
    /**
     * @var bool Whether or not the installation is active.
     */
    var $is_installation_active = false;
    /**
     * @var date Last time member logged in.
     */
    var $last_login;
    /**
     * @var int Current number of failed login attempts.
     */
    var $failed_logins;
    /**
     * @var str Description of account status, i.e., "Inactive due to excessive failed login attempts".
     */
    var $account_status;
    /**
     * @var bool If user is activated, 1 for true, 0 for false.
     */
    var $is_activated = false;
    /**
     * @var str Password reset token.
     */
    var $password_token;
    /**
     * @var str Subscriber timezone.
     */
    var $timezone;
    /**
     * @var str Subscription payment status.
     */
    var $subscription_status;
    /**
     * @var bool Whether or not the member closed their account.
     */
    var $is_account_closed = false;
    public function __construct($row = false) {
        if ($row) {
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->pwd = $row['pwd'];
            $this->pwd_salt = $row['pwd_salt'];
            $this->creation_time = $row['creation_time'];
            $this->network_user_id = $row['network_user_id'];
            $this->network_user_name = $row['network_user_name'];
            $this->network = $row['network'];
            $this->full_name = $row['full_name'];
            $this->follower_count = $row['follower_count'];
            $this->is_verified = PDODAO::convertDBToBool($row['is_verified']);
            $this->oauth_access_token = $row['oauth_access_token'];
            $this->oauth_access_token_secret = $row['oauth_access_token_secret'];
            $this->verification_code = $row['verification_code'];
            $this->is_email_verified = PDODAO::convertDBToBool($row['is_email_verified']);
            $this->is_from_waitlist = PDODAO::convertDBToBool($row['is_from_waitlist']);
            $this->membership_level = $row['membership_level'];
            $this->is_membership_complimentary = PDODAO::convertDBToBool($row['is_membership_complimentary']);
            $this->thinkup_username = $row['thinkup_username'];
            $this->date_installed = $row['date_installed'];
            $this->api_key_private = $row['api_key_private'];
            $this->last_dispatched = $row['last_dispatched'];
            $this->commit_hash = $row['commit_hash'];
            $this->is_installation_active = PDODAO::convertDBToBool($row['is_installation_active']);
            $this->last_login = $row['last_login'];
            $this->failed_logins = $row['failed_logins'];
            $this->account_status = $row['account_status'];
            $this->is_activated = PDODAO::convertDBToBool($row['is_activated']);
            $this->password_token = $row['password_token'];
            $this->timezone = $row['timezone'];
            $this->subscription_status = $row['subscription_status'];
            $this->is_account_closed = PDODAO::convertDBToBool($row['is_account_closed']);
        }
    }

    /**
     * Generates a new password recovery token and returns it.
     *
     * The internal format of the token is a Unix timestamp of when it was set (for checking if it's stale), an
     * underscore, and then the token itself.
     *
     * @return string A new password token for embedding in a link and emailing a user.
     */
    public function setPasswordRecoveryToken() {
        $token = md5(uniqid(rand()));
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber_dao->updatePasswordToken($this->email, $token . '_' . time());
        return $token;
    }

    /**
     * Returns whether a given password recovery token is valid or not.
     *
     * This requires that the token not be stale (older than a day), and that  token itself matches what's in the
     * database.
     *
     * @param string $token The token to validate against the database.
     * @return bool Whether the token is valid or not.
     */
    public function validateRecoveryToken($token) {
        $data = explode('_', $this->password_token);
        return ((time() - $data[1] <= 86400) && ($token == $data[0]));
    }

    /**
     * Get a simple string indicating the payment status of a subscriber's subscription payment.
     * Possible values:
     * - Payment due (Soon to be Free trial)
     * - Paid through [date]
     * - Complimentary membership
     * - Payment pending
     * - Payment failed
     * - Authorization pending
     * - Authorization failed
     * @return str
     */
    public function getSubscriptionStatus() {
        $subscription_status = "";
        if ($this->is_membership_complimentary) {
            $subscription_status = "Complimentary membership";
        } else {
            //Get latest payment
            $subscriber_payment_dao = new SubscriberPaymentMySQLDAO();
            $latest_payment = $subscriber_payment_dao->getBySubscriber($this->id, 1);
            if (sizeof($latest_payment) > 0) {
                $latest_payment = $latest_payment[0];
            } else {
                $latest_payment = null;
            }
            if ( $latest_payment !== null ) {
                if ( $latest_payment['transaction_status'] == 'Success') {
                    $paid_through_year = intval(date('Y', strtotime($latest_payment['timestamp']))) + 1;
                    $paid_through_date = date('M j, ', strtotime($latest_payment['timestamp']));
                    $subscription_status = "Paid through ".$paid_through_date.$paid_through_year;
                } elseif ( $latest_payment['transaction_status'] == 'Pending') {
                    $subscription_status = "Payment pending";
                } elseif ( $latest_payment['transaction_status'] == 'Failure') {
                    $subscription_status = "Payment failed";
                } else {
                    $subscription_status = "Payment failed";
                } 
            } else {
                //Get latest authorization
                $subscriber_auth_dao = new SubscriberAuthorizationMySQLDAO();
                $latest_auth = $subscriber_auth_dao->getBySubscriberID($this->id, 1);
                if (sizeof($latest_auth) > 0) {
                    $latest_auth = $latest_auth[0];
                } else {
                    $latest_auth = null;
                }
                if ($latest_auth !== null ) {
                    if ($latest_auth->error_message === null) {
                        $subscription_status = 'Authorization pending';
                    } else {
                        $subscription_status = 'Authorization failed';
                    }
                } else { //no auth, no payment
                    $subscription_status = "Payment due";
                }
            }
        }
        return $subscription_status;
    }
}
