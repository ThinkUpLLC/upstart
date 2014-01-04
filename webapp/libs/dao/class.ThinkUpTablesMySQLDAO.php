<?php

class ThinkUpTablesMySQLDAO extends PDODAO {

    public function insertInstance($network_user_id, $network_username, $network = "twitter", $viewer_id = false) {
        $q  = "INSERT INTO tu_instances ";
        $q .= "(network_user_id, network_username, network, network_viewer_id, last_post_id, is_public) ";
        $q .= "VALUES (:user_id , :username, :network, :viewer_id, '', 1) ";
        $vars = array(
            ':user_id'=>(string)$network_user_id,
            ':username'=>$network_username,
            ':network'=>$network,
            ':viewer_id'=>(string)($viewer_id ? $viewer_id : $network_user_id)
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $ps = $this->execute($q, $vars);
        return $this->getInsertId($ps);
    }

    public function insertOwnerInstance($owner_id, $instance_id, $oauth_token = '', $oauth_token_secret = '') {
        $q = "INSERT INTO tu_owner_instances
                (owner_id, instance_id, oauth_access_token, oauth_access_token_secret)
                    VALUES (:owner_id,:instance_id,:oauth_access_token,:oauth_access_token_secret)";

        $vars = array(':owner_id' => $owner_id,
                      ':instance_id' => $instance_id,
                      ':oauth_access_token' => $oauth_token,
                      ':oauth_access_token_secret' => $oauth_token_secret
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $stmt = $this->execute($q, $vars);
        if ( $this->getInsertCount($stmt) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insertOptionValue($namespace, $option_name, $option_value) {
        $q = "INSERT INTO tu_options (namespace, option_name, option_value, last_updated, created)
        VALUES (:namespace, :option_name, :option_value, NOW(), NOW())";

        $vars = array(
          ':namespace' => $namespace,
          ':option_name' => $option_name,
          ':option_value' => $option_value
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $stmt = $this->execute($q, $vars);
        if ( $this->getInsertCount($stmt) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function createOwner($email, $hashed_pwd, $pwd_salt, $membership_level, $timezone='UTC',
        $is_admin=false, $api_key_private = null ) {
        $activation_code = rand(1000, 9999);
        $api_key = $this->generateAPIKey();

        $q = "INSERT INTO tu_owners SET email=:email, pwd=:hashed_pwd, pwd_salt=:pwd_salt, joined=NOW(), ";
        $q .= "activation_code=:activation_code, full_name=:full_name, api_key=:api_key, ";
        $q .= "membership_level=:membership_level, timezone=:timezone, ";
        $q .= "api_key_private=:api_key_private, is_activated=1 ";

        if ($is_admin) {
            $q .= ", is_admin=1";
        }
        $vars = array(
                ':email'=>$email,
                ':hashed_pwd'=>$hashed_pwd,
                ':pwd_salt'=>$pwd_salt,
                ':activation_code'=>$activation_code,
                ':full_name'=>'',
                ':api_key'=>$api_key,
                ':api_key_private'=>$api_key_private,
                ':membership_level'=>$membership_level,
                ':timezone'=>$timezone
        );
        if ($this->profiler_enabled) Profiler::setDAOMethod(__METHOD__);
        $ps = $this->execute($q, $vars);
        return array($this->getInsertId($ps), $api_key);
    }

    /**
     * Generate a password salt and hash it given an email address and password.
     * @param  str $email ThinkUp member email address
     * @param  str $pwd   ThinkUp member password
     * @return arr array(salted pass, hashed pass)
     */
    public function saltAndHashPwd($email, $pwd) {
        $pwd_salt = $this->generateSalt($email);
        $hashed_pwd = $this->hashPassword($pwd, $pwd_salt);
        return array($pwd_salt, $hashed_pwd);
    }

    /**
     * Generate a new API KEY - md5 hashed random string
     * @return str A generated API Key
     */
    private function generateAPIKey() {
        return md5(uniqid(mt_rand(), true)); // generate random api key
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
}