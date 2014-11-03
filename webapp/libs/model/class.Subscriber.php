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
     * @var str Status of subscription payment.
     */
    var $subscription_status;
    /**
     * @var str Membership is paid for through this date.
     */
    var $paid_through;
    /**
     * @var str How often membership renews, 1 month, 12 months or None.
     */
    var $subscription_recurrence;
    /**
     * @var int The number of payment reminder emails sent to this subscriber.
     */
    var $total_payment_reminders_sent;
    /**
     * @var str Last time a payment reminder was sent to this subscriber.
     */
    var $payment_reminder_last_sent;
    /**
     * @var bool Whether or not the member closed their account.
     */
    var $is_account_closed = false;
    /**
     * @var str Redeemed claim code.
     */
    var $claim_code;
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
            $this->paid_through = $row['paid_through'];
            $this->subscription_recurrence = $row['subscription_recurrence'];
            $this->total_payment_reminders_sent = $row['total_payment_reminders_sent'];
            $this->payment_reminder_last_sent = $row['payment_reminder_last_sent'];
            $this->is_account_closed = PDODAO::convertDBToBool($row['is_account_closed']);
            $this->claim_code = $row['claim_code'];
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
    * Calculate how many days are left in a 14-day trial.
    * @return int
    */
    public function getDaysLeftInFreeTrial() {
        $creation_date = new DateTime($this->creation_time);
        $now = new DateTime();
        $end_of_trial = $creation_date->add(new DateInterval('P15D'));
        $days_left = 0;
        if ($end_of_trial >= $now) {
            $interval = $now->diff($end_of_trial);
            $days_left = $interval->format('%a');
        }
        return intval($days_left);
    }
}
