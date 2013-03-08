<?php
class UserRouteMySQLDAO extends PDODAO {

    public function insert($email, $twitter_username, $twitter_user_id, $oauth_access_token, $oauth_access_token_secret,
    $is_verified, $follower_count) {
        $q  = "INSERT INTO user_routes (email, twitter_username, twitter_user_id, oauth_access_token, ";
        $q .= "oauth_access_token_secret, is_verified, follower_count) VALUES (:email, ";
        $q .= ":twitter_username, :twitter_user_id, :oauth_access_token, :oauth_access_token_secret, ";
        $q .= ":is_verified, :follower_count); ";
        $vars = array(
            ':email'=>$email,
            ':twitter_username'=>$twitter_username,
            ':twitter_user_id'=>$twitter_user_id,
            ':oauth_access_token'=>$oauth_access_token,
            ':oauth_access_token_secret'=>$oauth_access_token_secret,
            ':is_verified'=>(integer) $is_verified,
            ':follower_count'=>(integer) $follower_count
        );
        //echo self::mergeSQLVars($q, $vars);
        try {
            $ps = $this->execute($q, $vars);
        } catch (PDOException $e) {
            $exception = $e->getMessage();
            if ((preg_match('/Duplicate entry/', $exception)>0) && (preg_match('/for key \'email\'/', $exception)>0)) {
                return 1;
            }
        }
        return $this->getUpdateCount($ps);
    }

    public function get($email, $twitter_user_id) {
        $q  = "SELECT id FROM user_routes ";
        $q .= "WHERE email = :email AND twitter_user_id = :twitter_user_id ";
        $vars = array(
            ':email'=>$email,
            ':twitter_user_id'=>$twitter_user_id
        );
        $ps = $this->execute($q, $vars);
        $result = $this->getDataRowAsArray($ps);
        return $result['id'];
    }

    public function getUserList($page_number=1, $count=50) {
        $start_on_record = ($page_number - 1) * $count;
        $q  = "SELECT * FROM user_routes ";
        $q .= "ORDER BY follower_count DESC, is_verified DESC ";
        $q .= "LIMIT :start_on_record, :limit;";

        $vars = array(
            ':start_on_record'=>$start_on_record,
            ':limit'=>$count
        );
        //echo self::mergeSQLVars($q, $vars);
        $ps = $this->execute($q, $vars);
        return $this->getDataRowsAsArrays($ps);
    }

    private static function mergeSQLVars($sql, $vars) {
        foreach ($vars as $k => $v) {
            $sql = str_replace($k, (is_int($v))?$v:"'".$v."'", $sql);
        }
        $config = Config::getInstance();
        $prefix = $config->getValue('table_prefix');
        $gmt_offset = $config->getGMTOffset();
        $sql = str_replace('#gmt_offset#', $gmt_offset, $sql);
        $sql = str_replace('#prefix#', $prefix, $sql);
        return $sql;
    }
}
