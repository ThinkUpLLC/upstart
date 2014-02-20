<?php
class SubscriberMySQLDAO extends PDODAO {
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
            if (strpos($message,'Duplicate entry') !== false && strpos($message,'network_user_id') !== false) {
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

    public function getTotalSubscribers($amount = 0) {
        $q = "SELECT count(*) as total FROM subscribers s ";
        if ($amount > 0) {
            $q .= "INNER JOIN subscriber_authorizations sa ON s.id = sa.subscriber_id ";
            $q .= "INNER JOIN authorizations a ON a.id = sa.authorization_id WHERE a.amount = :amount";
            $vars = array ( ':amount' => $amount);
            $ps = $this->execute($q, $vars);
        } else {
            if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
            $ps = $this->execute($q);
        }
        $result = $this->getDataRowAsArray($ps);
        return $result["total"];
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
        return $this->getDataRowAsObject($ps, "Subscriber");
    }

    public function getByID($subscriber_id) {
        $q = "SELECT * FROM subscribers WHERE id = :subscriber_id";
        $vars = array ( ':subscriber_id' => $subscriber_id);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowAsObject($ps, "Subscriber");
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

    public function getSubscribersWithoutSubscriptionStatus($limit=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM subscribers s WHERE membership_level != 'Waitlist' AND subscription_status IS null ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$limit
        );
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsObjects($ps, 'Subscriber');
    }

    public function getListTotal() {
        $q  = "SELECT count(*) as total FROM subscribers s ";
        $q .= "INNER JOIN subscriber_authorizations sa ON sa.subscriber_id = s.id;";
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
        $q .= "a.token_validity_start_date FROM subscribers s LEFT JOIN subscriber_authorizations sa ";
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

    public function getSubscribersNotInstalled($count=25) {
        $q  = "SELECT id FROM subscribers WHERE thinkup_username IS NOT NULL AND date_installed IS null ";
        $q .= "AND membership_level != 'Waitlist' LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getTotalSubscribersToInstall() {
        $q  = "SELECT count(id) as total FROM subscribers ";
        $q .= "WHERE thinkup_username IS NOT NULL AND date_installed IS null ";
        $q .= "AND membership_level != 'Waitlist';";

        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
    }

    public function getTotalSubscribersToUninstall() {
        $q  = "SELECT count(id) as total FROM subscribers ";
        $q .= "WHERE thinkup_username IS NOT NULL AND date_installed IS NOT null ";
        $q .= "AND membership_level != 'Waitlist';";

        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
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

    public function getStaleInstalls($count=25) {
        $q  = "SELECT * FROM subscribers WHERE is_installation_active = 1 ";
        $q .= "AND (last_dispatched < DATE_SUB(NOW(), INTERVAL 1 HOUR) OR last_dispatched IS NULL) ";
        $q .= "ORDER BY last_dispatched ASC ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getStaleInstalls10kAndUp($count=25) {
        $q  = "SELECT * FROM subscribers WHERE is_installation_active = 1 AND follower_count >= 10000 ";
        $q .= "AND last_dispatched < DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY last_dispatched ASC ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getStaleInstalls1kTo10k($count=25) {
        $q  = "SELECT * FROM subscribers WHERE is_installation_active = 1 AND ";
        $q .= "(follower_count < 10000 AND follower_count >= 1000) ";
        $q .= "AND last_dispatched < DATE_SUB(NOW(), INTERVAL 3 HOUR) ORDER BY last_dispatched ASC ";
        $q .= "LIMIT :limit;";

        $vars = array(
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    public function getStalestInstall1kTo10kLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM subscribers WHERE is_installation_active=1 ";
        $q .= "AND (follower_count < 10000 AND follower_count >= 1000) ";
        $q .= "ORDER BY last_dispatched ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_dispatched'];
    }

    public function getStalestInstall10kAndUpLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM subscribers WHERE is_installation_active=1  AND follower_count >= 10000 ";
        $q .= "ORDER BY last_dispatched ASC LIMIT 1";
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['last_dispatched'];
    }

    public function getStalestInstallLastDispatchTime() {
        $q  = "SELECT last_dispatched FROM subscribers WHERE is_installation_active=1 AND follower_count < 1000 ";
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

    public function updateSubscriptionStatus($id, $status) {
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
        $q = "SELECT thinkup_username FROM subscribers WHERE thinkup_username=:thinkup_username";
        $vars = array(
            ':thinkup_username'=>$thinkup_username
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

    /**
     * Get total subscribers who have have never paid.
     * @TODO: Make this work for second payments (a year from now)
     * @return int  Number of subscribers to charge
     */
    public function getTotalSubscribersToCharge() {
        $q = "SELECT count(s.id) as total FROM subscribers s ";
        $q .= "INNER JOIN subscriber_authorizations sa ON sa.subscriber_id = s.id ";
        $q .= "INNER JOIN authorizations a ON sa.authorization_id = a.id ";
        $q .= "LEFT JOIN subscriber_payments sp ON sp.subscriber_id = s.id ";
        $q .= "LEFT JOIN payments p ON p.id = sp.payment_id ";
        $q .= "WHERE s.is_membership_complimentary = 0 AND s.membership_level != 'Waitlist' AND request_id IS NULL ";
        //echo self::mergeSQLVars($q, $vars);
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q);
        $result = $this->getDataRowAsArray($ps);
        return $result['total'];
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
            'is_membership_complimentary'=>$is_complimentary
        );
        if ($this->profiler_enabled) { Profiler::setDAOMethod(__METHOD__); }
        $ps = $this->execute($q, $vars);
        return $this->getUpdateCount($ps);
    }
}