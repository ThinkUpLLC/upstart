<?php
class SubscriberMySQLDAO extends PDODAO {

    public function insertCompleteSubscriber(Subscriber $subscriber) {
        $verification_code = rand(1000, 9999);
        $pwd_salt = $this->generateSalt($subscriber->email);
        $hashed_pwd = $this->hashPassword($subscriber->pwd, $pwd_salt);

        $q  = "INSERT INTO subscribers (email, pwd, pwd_salt, creation_time, verification_code, network_user_id, ";
        $q .= "network_user_name, network, full_name, follower_count, is_verified, oauth_access_token, ";
        $q .= "oauth_access_token_secret, membership_level, thinkup_username, timezone ) VALUES ";
        $q .= "(:email, :pwd, :pwd_salt, CURRENT_TIMESTAMP, :verification_code, :network_user_id, ";
        $q .= ":network_user_name, :network, :full_name, :follower_count, :is_verified, :oauth_access_token, ";
        $q .= ":oauth_access_token_secret, :membership_level, :thinkup_username, :timezone); ";
        $vars = array(
            ':email'=>$subscriber->email,
            ':pwd'=>$hashed_pwd,
            ':pwd_salt'=>$pwd_salt,
            ':verification_code'=>$verification_code,
            ':network_user_id'=>$subscriber->network_user_id,
            ':network_user_name'=>$subscriber->network_user_name,
            ':network'=>$subscriber->network,
            ':full_name'=>$subscriber->full_name,
            ':follower_count'=>$subscriber->follower_count,
            ':is_verified'=>$subscriber->is_verified,
            ':oauth_access_token'=>$subscriber->oauth_access_token,
            ':oauth_access_token_secret'=>$subscriber->oauth_access_token_secret,
            ':membership_level'=>$subscriber->membership_level,
            ':thinkup_username'=>$subscriber->thinkup_username,
            ':timezone'=>$subscriber->timezone
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        try {
            $ps = $this->execute($q, $vars);
            return $this->getInsertId($ps);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message, "for key 'email'") !== false) {
                throw new DuplicateSubscriberEmailException($message);
            } elseif (strpos($message,'Duplicate entry') !== false
                && strpos($message,"for key 'thinkup_username'") !== false) {
                throw new DuplicateSubscriberUsernameException($message);
            } elseif (strpos($message,'Duplicate entry') !== false
                && strpos($message,"for key 'network_user_id'") !== false) {
                throw new DuplicateSubscriberConnectionException($message);
            } else {
                throw new PDOException($message);
            }
        }

    }

    public function insert($email, $pwd ) {
        $verification_code = rand(1000, 9999);
        $pwd_salt = $this->generateSalt($email);
        $hashed_pwd = $this->hashPassword($pwd, $pwd_salt);

        $q  = "INSERT INTO subscribers (email, pwd, pwd_salt, creation_time, verification_code) VALUES ";
        $q .= "(:email, :pwd, :pwd_salt, CURRENT_TIMESTAMP, :verification_code); ";
        $vars = array(
            ':email'=>$email,
            ':pwd'=>$hashed_pwd,
            ':pwd_salt'=>$pwd_salt,
            ':verification_code'=>$verification_code
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        try {
            $ps = $this->execute($q, $vars);
            return $this->getInsertId($ps);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message,'email') !== false) {
                throw new DuplicateSubscriberEmailException($message);
            } else {
                throw new PDOException($message);
            }
        }
    }

    public function update($subscriber_id, $network_user_name, $network_user_id, $network, $full_name,
    $oauth_access_token, $oauth_access_token_secret, $is_verified=0, $follower_count=0) {
        $q = " UPDATE subscribers SET network_user_name=:network_user_name, network_user_id = :network_user_id, ";
        $q .= "network = :network, full_name = :full_name, oauth_access_token = :oauth_access_token, ";
        $q .= "oauth_access_token_secret = :oauth_access_token_secret, is_verified = :is_verified, ";
        $q .= "follower_count = :follower_count WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':network_user_name'=>$network_user_name,
            ':network_user_id'=>$network_user_id,
            ':network'=>$network,
            ':full_name'=>$full_name,
            ':oauth_access_token'=>$oauth_access_token,
            ':oauth_access_token_secret'=>$oauth_access_token_secret,
            ':follower_count'=>$follower_count,
            ':is_verified'=>(integer) $is_verified
        );
        try {
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            $ps = $this->execute($q, $vars);
            return $this->getUpdateCount($ps);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false
                && strpos($message,"for key 'network_user_id'") !== false) {
                throw new DuplicateSubscriberConnectionException($message);
            }
        }
    }

    public function setUsername($subscriber_id, $username) {
        $q = " UPDATE subscribers SET thinkup_username = :thinkup_username WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':thinkup_username'=>$username
        );
        try {
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            $ps = $this->execute($q, $vars);
            if ($this->getUpdateCount($ps) == 0) {
                return false;
            } else {
                return true;
            }
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message,'thinkup_username') !== false) {
                throw new DuplicateSubscriberUsernameException($message);
            }
        }
    }

    public function setEmail($subscriber_id, $email) {
        $q = " UPDATE subscribers SET email = :email WHERE id=:id";
        $vars = array(
            ':id'=>$subscriber_id,
            ':email'=>$email
        );
        try {
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            $ps = $this->execute($q, $vars);
            if ($this->getUpdateCount($ps) == 0) {
                return false;
            } else {
                return true;
            }
        } catch (PDOException $e) {
            $message = $e->getMessage();
            if (strpos($message,'Duplicate entry') !== false && strpos($message,'email') !== false) {
                throw new DuplicateSubscriberEmailException($message);
            }
        }
    }

    public function getVerificationCode($email) {
        $q = "SELECT verification_code FROM subscribers WHERE email = :email;";
        $vars = array (':email'=>$email);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsArray($ps);
    }

    public function verifyEmailAddress($email) {
        $q = "UPDATE subscribers SET is_email_verified = 1 WHERE email = :email ";
        $vars = array (':email'=>$email);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }
    /**
     * Generate a unique, random salt by appending the users email to a random number and returning the hash of it
     * @param str $email
     * @return str Salt
     */
    private function generateSalt($email){
        return hash('sha256', rand().$email);
    }
    /**
     * Hashes a password with a given salt.
     * @param str $password
     * @param str $salt
     * @param str Hashed password
     */
    private function hashPassword($password, $salt) {
        return hash('sha256', $password.$salt);
    }

    public function getByEmail($email) {
        $q = "SELECT * FROM subscribers WHERE email = :email";
        $vars = array ( ':email' => $email);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $subscriber =  $this->getDataRowAsObject($ps, "Subscriber");
        if (!isset($subscriber)) {
            throw new SubscriberDoesNotExistException('Subscriber '.$email.' does not exist.');
        }
        return $subscriber;
    }

    public function getByID($subscriber_id) {
        $q = "SELECT * FROM subscribers WHERE id = :subscriber_id";
        $vars = array ( ':subscriber_id' => $subscriber_id);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $subscriber = $this->getDataRowAsObject($ps, "Subscriber");
        if (!isset($subscriber)) {
            throw new SubscriberDoesNotExistException('Subscriber ID '.$subscriber_id.' does not exist.');
        }
        return $subscriber;
    }

    public function getSubscriberList($page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM subscribers s ";
        $q .= "ORDER BY s.creation_time DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function getSubscriberListWithPaymentStatus($status, $page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM subscribers s WHERE subscription_status = :status ";
        $q .= "ORDER BY s.creation_time DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':status'=>$status,
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function getPaidTotal() {
        $q  = "SELECT count(*) as total FROM subscribers s WHERE subscription_status LIKE 'Paid through%' ";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function getSearchResults($search_term, $page_number = 1, $count = 50) {
        $start_on_record = ($page_number - 1) * $count;
        $q = "SELECT * FROM subscribers s ";
        $q .= "WHERE email LIKE :search_term OR network_user_name LIKE :search_term1 OR full_name LIKE :search_term2 ";
        $q .= "OR thinkup_username LIKE :search_term ";
        $q .= "ORDER BY s.creation_time DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':search_term1'=>'%'.$search_term.'%',
            ':search_term2'=>'%'.$search_term.'%',
            ':search_term'=>'%'.$search_term.'%',
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function archiveSubscriber($subscriber_id) {
        $q = "INSERT INTO subscriber_archive SELECT s.email, s.pwd, s.pwd_salt, s.creation_time, s.network_user_id, ";
        $q .= "s.network_user_name, s.network, s.full_name, s.follower_count, s.is_verified, s.oauth_access_token, ";
        $q .= "s.oauth_access_token_secret, s.verification_code, s.is_email_verified, s.is_from_waitlist, ";
        $q .= "s.membership_level, s.thinkup_username, s.date_installed, s.api_key_private, s.last_dispatched, ";
        $q .= "s.commit_hash, s.is_installation_active, a.token_id, a.amount, ";
        $q .= "a.status_code, a.error_message, a.payment_method_expiry, a.caller_reference, a.recurrence_period, ";
        $q .= "a.token_validity_start_date, s.subscription_status, s.total_payment_reminders_sent, ";
        $q .= "s.payment_reminder_last_sent, s.is_account_closed ";
        $q .= "FROM subscribers s LEFT JOIN subscriber_authorizations sa ";
        $q .= "ON s.id = sa.subscriber_id LEFT JOIN authorizations a ON a.id = sa.authorization_id ";
        $q .= "WHERE s.id = :subscriber_id";

        $vars = array(
            ':subscriber_id'=>$subscriber_id
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function deleteBySubscriberID($subscriber_id) {
        $q  = "DELETE FROM subscribers WHERE id = :subscriber_id";
        $vars = array(
            ':subscriber_id'=>$subscriber_id
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function intializeInstallation($id, $api_key_private, $commit_hash) {
        $q  = "UPDATE subscribers SET date_installed=NOW(), api_key_private = :api_key_private, ";
        $q .= "commit_hash = :commit_hash, is_installation_active=1 WHERE id = :id ";
        $vars = array(
            ':id'=>$id,
            ':api_key_private'=>$api_key_private,
            ':commit_hash'=>$commit_hash,
        );
        $ps = $this->execute($q, $vars);
    }

    public function updateDateInstalled($id, $date_installed) {
        $q  = "UPDATE subscribers SET date_installed = :date_installed ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':id'=>(int) $id,
            ':date_installed'=>$date_installed
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updateLastDispatchedTime($id) {
        $q  = "UPDATE subscribers SET last_dispatched = CURRENT_TIMESTAMP() ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function resetLastDispatchedTime($id) {
        $q  = "UPDATE subscribers SET last_dispatched = null ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updateCommitHash($id, $commit_hash) {
        $q  = "UPDATE subscribers SET commit_hash = :commit_hash ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':commit_hash'=>$commit_hash,
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function doesSubscriberConnectionExist($network_user_id, $network) {
        $q  = "SELECT id FROM subscribers WHERE network_user_id = :network_user_id AND network = :network ";
        $vars = array(
            ':network_user_id'=>$network_user_id,
            ':network'=>$network
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $returned_subscribers = $this->getDataRowsAsArrays($ps);
        return (count($returned_subscribers) > 0);
    }

    public function doesSubscriberEmailExist($email) {
        $q  = "SELECT id FROM subscribers WHERE email = :email ";
        $vars = array(
            ':email'=>$email
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $returned_subscribers = $this->getDataRowsAsArrays($ps);
        return (count($returned_subscribers) > 0);
    }

    public function getTotalInstallsToUpgrade($commit_hash) {
        $q  = "SELECT count(*) as total FROM subscribers WHERE is_installation_active = 1 ";
        $q .= "AND commit_hash != :commit_hash;";

        $vars = array(
            ':commit_hash'=>$commit_hash
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function getInstallsToUpgrade($commit_hash) {
        $q  = "SELECT * FROM subscribers WHERE is_installation_active = 1 ";
        $q .= "AND commit_hash != :commit_hash LIMIT 10;";

        $vars = array(
            ':commit_hash'=>$commit_hash
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function setInstallationActive($id, $is_installation_active) {
        $q  = "UPDATE subscribers SET is_installation_active = :is_installation_active ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':is_installation_active'=> (int) $is_installation_active,
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function setMembershipLevel($id, $membership_level) {
        $q  = "UPDATE subscribers SET membership_level = :membership_level ";
        $q .= "WHERE id = :id ";

        $vars = array(
            ':membership_level'=> $membership_level,
            ':id'=>(int) $id
        );
        //echo self::mergeSQLVars($q, $vars)."\n";
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function getSubscribersInstalled($count=25) {
        $q  = "SELECT id FROM subscribers WHERE date_installed IS NOT NULL AND is_installation_active = 1 ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getPaidStaleInstalls($count=25) {
        $q  = "SELECT * FROM subscribers WHERE is_installation_active = 1 ";
        $q .= "AND (subscription_status LIKE 'Paid through%' OR is_membership_complimentary = 1) ";
        $q .= "AND (last_dispatched < DATE_SUB(NOW(), INTERVAL 90 MINUTE) OR last_dispatched IS NULL) ";
        $q .= "ORDER BY last_dispatched ASC ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    /**
     * Get installations for members who have not paid.
     * This function does not return members who have received 3 email reminders to pay, and 12 days have passed since
     * last reminder was sent. That's so they're not being crawled when they are uninstalled automatically at the
     * 14-day threshold.
     * @param  integer $count How many installs to retrieve; defaults to 25
     * @return arr Array of installation information
     */
    public function getNotYetPaidStaleInstalls($count=25) {
        $q  = "SELECT * FROM subscribers WHERE is_installation_active = 1 ";
        $q .= "AND subscription_status NOT LIKE 'Paid through%' ";
        $q .= "AND ((last_dispatched < DATE_SUB(NOW(), INTERVAL 3 HOUR) OR last_dispatched IS NULL)) ";
        // Upstart isn't sending payment reminders or isn't finished sending them
        $q .= "AND (total_payment_reminders_sent < 3  OR ";
        // Upstart's sent all the payment reminders but the last one was sent within the last 12 days
        $q .= "(total_payment_reminders_sent = 3 AND payment_reminder_last_sent > DATE_SUB(NOW(), INTERVAL 12 DAY ))) ";
        $q .= "ORDER BY last_dispatched ASC ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getPaidStalestInstallLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM subscribers WHERE is_installation_active=1 ";
        $q .= "AND subscription_status LIKE 'Paid through%' ";
        $q .= "ORDER BY last_dispatched ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_dispatched'];
    }

    public function getNotPaidStalestInstallLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM subscribers WHERE is_installation_active=1 ";
        $q .= "AND subscription_status NOT LIKE 'Paid through%' ";
        // Upstart isn't sending payment reminders or isn't finished sending them
        $q .= "AND (total_payment_reminders_sent < 3  OR ";
        // Upstart's sent all the payment reminders but the last one was sent within the last 12 days
        $q .= "(total_payment_reminders_sent = 3 AND payment_reminder_last_sent > DATE_SUB(NOW(), INTERVAL 12 DAY ))) ";
        $q .= "ORDER BY last_dispatched ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_dispatched'];
    }

    public function getTotalActiveInstalls() {
        $q  = "SELECT count(*) AS total FROM subscribers WHERE is_installation_active = 1;";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function isAuthorized($email, $password) {
        // Get salt from the database
        $db_salt = $this->getSaltByEmail($email);
        // Get password from the database
        $db_password = $this->getPass($email);

        $hashed_pwd = $this->hashPassword($password, $db_salt); // Hash the new way
        // Check if it matches the password stored in the database
        return ($hashed_pwd == $db_password);
    }

    /**
     * Retrives the salt for a given user
     * @param str $email
     * @return str Salt
     */
    private function getSaltByEmail($email){
        $q = "SELECT pwd_salt ";
        $q .= "FROM subscribers s ";
        $q .= "WHERE s.email = :email";
        $vars = array(':email'=>$email);
        $ps = $this->execute($q, $vars);
        $query = $this->getDataRowAsArray($ps);
        return $query['pwd_salt'];
    }

    public function getPass($email) {
        $q = "SELECT pwd FROM subscribers WHERE email = :email LIMIT 1;";
        $vars = array(
            ':email'=>$email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        if (isset($result['pwd'])) {
            return $result['pwd'];
        } else {
            return false;
        }
    }

    public function updateLastLogin($email) {
        $q = " UPDATE subscribers SET last_login=now() WHERE email=:email";
        $vars = array(
            ':email'=>$email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function resetFailedLogins($email) {
        $q = "UPDATE subscribers
              SET failed_logins=0
              WHERE email=:email";
        $vars = array(
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return ( $this->getUpdateCount($ps) > 0 )? true : false;
    }

    public function setAccountStatus($email, $status) {
        $q = "UPDATE subscribers
              SET account_status=:account_status
              WHERE email=:email";
        $vars = array(
            ":account_status" => $status,
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return ( $this->getUpdateCount($ps) > 0 )? true : false;
    }

    public function clearAccountStatus($email) {
        return  $this->setAccountStatus($email, '');
    }

    public function incrementFailedLogins($email) {
        $q = "UPDATE subscribers
              SET failed_logins=failed_logins+1
              WHERE email=:email";
        $vars = array(
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return ( $this->getUpdateCount($ps) > 0 )? true : false;
    }

    public function deactivateSubscriber($email) {
        $this->updateActivation($email, false);
    }

    public function activateSubscriber($email) {
        $this->updateActivation($email, true);
    }

    /**
     * Set the value of the is_activated field.
     * @param str $email
     * @param bool $is_activated
     * @return int Count of affected rows
     */
    private function updateActivation($email, $is_activated) {
        $q = " UPDATE subscribers SET is_activated=:is_activated WHERE email=:email";
        $vars = array(
            ':email'=>$email,
            ':is_activated'=>(($is_activated)?1:0)
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updatePasswordToken($email, $token) {
        $q = "UPDATE subscribers
              SET password_token=:token
              WHERE email=:email";
        $vars = array(
            ":token" => $token,
            ":email" => $email
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function updateSubscriptionStatus($id, $status=null) {
        if (!isset($status)) {
            $subscriber = $this->getByID($id);
            if (isset($subscriber)) {
                $status = $subscriber->getSubscriptionStatus();
            } else {
                throw new SubscriberDoesNotExistException('Subscriber ID '.$id.' does not exist.');
            }
        }
        $q = "UPDATE subscribers
              SET subscription_status=:status
              WHERE id=:id";
        $vars = array(
            ":id" => $id,
            ":status" => $status
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    public function getByPasswordToken($token) {
        $q = "SELECT * FROM subscribers WHERE password_token LIKE :token";
        $vars = array(':token' => $token . '_%');
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, 'Subscriber');
    }

    public function updatePassword($email, $pwd) {
        // Generate new unique salt and store it in the database
        $salt = $this->generateSalt($email);
        $this->updateSalt($email, $salt);
        //Hash the password using the new salt
        $hashed_password = $this->hashPassword($pwd, $salt);
        //Store the new hashed password in the database
        $q = " UPDATE subscribers SET pwd=:hashed_password WHERE email=:email";
        $vars = array(
            ':email'=>$email,
            ':hashed_password'=>$hashed_password
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * Updates the password salt for a given user
     * @param str $email
     * @param str $salt
     * @return int Number of rows updated
     */
    private function updateSalt($email, $salt) {
        $q = "UPDATE subscribers SET pwd_salt=:salt WHERE email=:email";
        $vars = array(
            ':email'=>$email,
            ':salt'=>$salt
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * Check if a ThinkUp username is in use.
     * @param  str  $thinkup_username Username to check
     * @return bool  Whether or not it is in use
     */
    public function isUsernameTaken($thinkup_username) {
        $reserved_names = array('stage', 'demo');
        if (in_array($thinkup_username, $reserved_names) ) {
            return true;
        }
        $q = "SELECT thinkup_username FROM subscribers WHERE thinkup_username=:thinkup_username";
        $vars = array(
            //The username is always lowercase
            ':thinkup_username'=>strtolower($thinkup_username)
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataIsReturned($ps);
    }

    /**
     * Get subscribers who have have never paid.
     * @TODO: Make this work for second payments (a year from now)
     * @param  int $count Default to 10
     * @return bool  Whether or not it is in use
     */
    public function getSubscribersToCharge($count=10) {
        $q = "SELECT s.id, s.email, a.token_id, a.amount, p.request_id FROM subscribers s ";
        $q .= "INNER JOIN subscriber_authorizations sa ON sa.subscriber_id = s.id ";
        $q .= "INNER JOIN authorizations a ON sa.authorization_id = a.id ";
        $q .= "LEFT JOIN subscriber_payments sp ON sp.subscriber_id = s.id ";
        $q .= "LEFT JOIN payments p ON p.id = sp.payment_id ";
        $q .= "WHERE s.is_membership_complimentary = 0 AND s.membership_level != 'Waitlist' AND request_id IS NULL ";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        return $this->getDataRowsAsArrays($ps);
    }

    public function compSubscription($subscriber_id) {
        return $this->setIsComplimentary($subscriber_id, true);
    }

    public function decompSubscription($subscriber_id) {
        return $this->setIsComplimentary($subscriber_id, false);
    }

    private function setIsComplimentary($subscriber_id, $is_complimentary) {
        $q = "UPDATE subscribers SET is_membership_complimentary = :is_membership_complimentary ";
        $q .="WHERE id = :subscriber_id;";
        $vars = array(
            ':subscriber_id'=>$subscriber_id,
            ':is_membership_complimentary'=>$is_complimentary
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }
    /**
     * Get 25 subscribers with a payment due who should get an email reminder.
     * @param  int $total_reminders_sent How many reminders already sent to these subscribers
     * @param  int $hours_past_time      How many hours past signup or last reminder time.
     * @return arr                       Array of Subscriber objects
     */
    public function getSubscribersDueReminder($total_reminders_sent, $hours_past_time) {
        $q = "SELECT * FROM subscribers WHERE membership_level != 'Waitlist' AND subscription_status = 'Payment due' ";
        $q .= "AND total_payment_reminders_sent = :total_reminders_sent AND (";
        //If total_reminders_sent = 0, use creation_time to compare. Otherwise, use payment_reminder_last_sent.
        if ($total_reminders_sent == 0) {
            $q .= "creation_time ";
        } else {
            $q .= "payment_reminder_last_sent ";
        }
        $q .= "< DATE_SUB(NOW(), INTERVAL :hours_past_time HOUR )) ORDER BY creation_time ASC LIMIT 25";
        $vars = array(':total_reminders_sent' => $total_reminders_sent, ':hours_past_time' => $hours_past_time);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    /**
     * Get 25 subscribers to uninstall because payment has been due 14 days past the third reminder email.
     * @return arr Array of Subscriber objects
     */
    public function getSubscribersToUninstallDueToNonPayment() {
        $q = "SELECT * FROM subscribers WHERE membership_level != 'Waitlist' AND subscription_status = 'Payment due' ";
        $q .= "AND total_payment_reminders_sent = 3 AND ";
        $q .= "(payment_reminder_last_sent < DATE_SUB(NOW(), INTERVAL 14 DAY )) ";
        $q .= "ORDER BY creation_time ASC LIMIT 25";
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function setTotalPaymentRemindersSent($subscriber_id, $total_payment_reminders_sent) {
        $q = "UPDATE subscribers SET total_payment_reminders_sent = :total_payment_reminders_sent, ";
        $q .="payment_reminder_last_sent =  NOW() WHERE id = :subscriber_id;";
        $vars = array(
            ':subscriber_id'=>$subscriber_id,
            ':total_payment_reminders_sent'=>$total_payment_reminders_sent
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }

    /**
     * Get last three days worth of member signups.
     * @return array
     */
    public function getDailySignups() {
        $q = "SELECT count(id) as new_members, ";
        $q .= "DATE(creation_time) AS date  FROM subscribers WHERE membership_level != 'Waitlist' ";
        $q .= "GROUP BY DATE(creation_time) ORDER BY creation_time DESC LIMIT 3;";
        $ps = $this->execute($q);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        return $this->getDataRowsAsArrays($ps);
    }
}