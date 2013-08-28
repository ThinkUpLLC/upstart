<?php
class AppInstaller {
    /**
     * Parent folder where the application source files for each installation will go.
     * @var str
     */
    var $app_source_path;
    /**
     * Master copy of ThinkUp application files which will be copied/symlinked for each installation.
     * @var str
     */
    var $master_app_source_path;
    /**
     * Chameleon copy of ThinkUp application files which run crawler.
     * @var str
     */
    var $chameleon_app_source_path;
    /**
     * Parent folder where the application data files for each installation will go.
     * @var str
     */
    var $data_path;
    /**
     * Administrator email address.
     * @var str
     */
    var $admin_email;
    /**
     * Default admin password.
     * @var str
     */
    var $admin_password;
    /**
     * Default user password.
     * @var str
     */
    var $user_password;
    /**
     * User installation URL
     * @var str
     */
    var $user_installation_url;
    /**
     * Whether or not to echo output
     * @var bool
     */
    var $echo_output;

    public function __construct() {
        $cfg = Config::getInstance();
        $this->app_source_path = $cfg->getValue('app_source_path');
        $this->master_app_source_path = $cfg->getValue('master_app_source_path');
        $this->chameleon_app_source_path = $cfg->getValue('chameleon_app_source_path');
        $this->data_path = $cfg->getValue('data_path');
        $this->admin_email = $cfg->getValue('admin_email');
        $this->admin_password = $cfg->getValue('admin_password');
        $this->user_password = $cfg->getValue('user_password');
        $this->user_installation_url = $cfg->getValue('user_installation_url');
    }

    public function install($route_id, $echo_output=false) {
        $this->echo_output = $echo_output;

        $commit_hash = self::getMasterInstallCommitHash();

        if (isset($this->app_source_path)
        && isset($this->master_app_source_path)
        && isset($this->data_path)
        && isset($this->admin_email)
        && isset($this->admin_password)
        && isset($this->user_password)
        && isset($this->user_installation_url)) {

            $route = null;
            if (isset($route_id)) {
                $dao = new UserRouteMySQLDAO();
                $route = $dao->getById($route_id);
            }
            if (!isset($route)) {
                if (isset($route_id)) {
                    self::output('User route doesn\'t exist');
                } else {
                    self::output('No user route specified');
                }
            } else {
                if ($route['route'] == '') {
                    try {
                        session_write_close();
                        $code = self::setUpAppFiles($route['twitter_username']);
                        $database_name = self::createDatabase($code);

                        // Run upgrade.php --with-new-sql
                        // Get in the right directory to exec the upgrade
                        $cfg = Config::getInstance();
                        $master_app_source_path = $cfg->getValue('master_app_source_path');
                        if (!chdir($master_app_source_path.'/install/cli/thinkupllc-chameleon-upgrader') ) {
                            throw new Exception("Could not chdir to ".
                            $master_app_source_path.'/install/cli/thinkupllc-chameleon-upgrader');
                        }

                        // Initialize upgrade call parameters that are the same for every installation
                        /*
                        * {"installation_name":"steveklabnik", "timezone":"America/Los_Angeles", "db_host":"localhost",
                        * "db_name":"thinkupstart_steveklabnik", "db_socket":"/tmp/mysql.sock",  "db_port":""}
                        */
                        $upgrade_params_array = array(
                        'installation_name'=>$code,
                        'timezone'=>$cfg->getValue('dispatch_timezone'),
                        'db_host'=>$cfg->getValue('db_host'),
                        'db_name'=>'thinkupstart_'.$code,
                        'db_socket'=>$cfg->getValue('dispatch_socket'),
                        'db_port'=>$cfg->getValue('db_port')
                        );
                        $upgrade_params_json = json_encode($upgrade_params_array);

                        // Capture returned JSON
                        if (!exec("php upgrade.php '".$upgrade_params_json."'", $upgrade_status_json) ) {
                            throw new Exception('Unable to exec php upgrade.php '.$upgrade_params_json);
                        }

                        // print_r($upgrade_status_json);
                        $upgrade_status_array = JSONDecoder::decode($upgrade_status_json[0], true);

                        // DEBUG start
                        //                        echo "php upgrade.php '".$upgrade_params_json."'";
                        //                        print_r($upgrade_status_array);
                        // DEBUG end

                        self::switchToUpstartDatabase();

                        if ($upgrade_status_array['migration_success'] === true) {
                            $dao->insertLogEntry($route_id, $commit_hash, 1,
                            $upgrade_status_array['migration_message']);
                        } else {
                            // If error, set inactive, and store message, status, commit in install_log
                            $dao->setActive($route_id, 0);
                            $dao->insertLogEntry($route_id, $commit_hash, 0,
                            $upgrade_status_array['migration_message']);
                        }
                        // END Run upgrade.php --with-new-sql

                        self::switchToInstallationDatabase($code);
                        self::setUpDatabaseOptions($code);

                        list($admin_id, $admin_api_key, $owner_id, $owner_api_key) = self::createUsers($route['email']);
                        self::setUpServiceUser($owner_id, $route);

                        $url = str_replace ("{user}", $code, $this->user_installation_url);

                        self::switchToUpstartDatabase();

                        $dao->updateRoute($route_id, $url, $database_name, $commit_hash, $is_active=1);
                        self::output("Updated waitlist with link and db name");
                        self::dispatchCrawlJob($code);
                        $dao->updateLastDispatchedTime($route_id);
                        $dao->insertLogEntry($route_id, $commit_hash, 1, "Installed");

                        self::output("Complete. Log in at <a href=\"$url\" target=\"new\">".$url."</a>.");
                    } catch (Exception $e) {
                        self::output($e->getMessage());
                    }
                } else {
                    self::output('Installation exists at <a href="'.$route['route'].' target="new">'.$route['route'].
                    "</a>.");
                }
            }
        } else {
            self::output('Sorry, the installer is not configured to run just yet. Yet!');
        }
    }

    protected function output($message) {
        if ($this->echo_output) {
            echo "<li>".$message."</li>";
        }
    }

    private static function switchToUpstartDatabase() {
        $cfg = Config::getInstance();
        $q = "USE ". $cfg->getValue('db_name');
        PDODAO::$PDO->exec($q);
    }

    private static function switchToInstallationDatabase($code) {
        $q = "USE ". 'thinkupstart_'.$code;
        PDODAO::$PDO->exec($q);
    }

    protected function setUpAppFiles($path) {
        $path = self::subdomainifyPath($path);

        if (is_dir ($this->app_source_path . $path )) {
            $unique = uniqid();
            $path .= substr($unique, strlen($unique)-4, strlen($unique));
        }
        if (!is_dir($this->master_app_source_path)) {
            throw new Exception($this->master_app_source_path . " is not a directory.");
        }
        $cmd = 'ln -s '.$this->master_app_source_path.' '.$this->app_source_path.$path;
        $cmd_result = exec($cmd, $output, $return_var);
        if (is_link($this->app_source_path.$path )) {
            self::output("Symlinked new ThinkUp installation at $path");
        } else {
            $result = Utils::varDumpToString($output);
            throw new Exception("Could not create symlink from ".$this->master_app_source_path." to ".
            $this->app_source_path.$path. "<br>Command: ".$cmd. "<br> Command output: ".$result . "<br>Return var ".
            $return_var. "<br>Command result ".$cmd_result);
        }
        $cmd = 'mkdir '. $this->data_path . $path;
        exec($cmd);
        if (is_dir($this->data_path . $path )) {
            self::output("Created new data directory " . $this->data_path . $path);
        } else {
            throw new Exception("Could not create new data directory");
        }
        return $path;
    }

    /**
     * Make sure that path is valid characters for subdomains - not capital letters or special characters.
     * @param str $path
     * @return str $path
     */
    protected function subdomainifyPath($path) {
        $path = strtolower($path);
        $path = preg_replace("/[^a-zA-Z0-9\s]/", "", $path);
        if ($path == '') {
            $unique = uniqid();
            $path .= substr($unique, strlen($unique)-4, strlen($unique));
        }
        return $path;
    }

    protected function createDatabase($code) {
        $q = "CREATE DATABASE thinkupstart_$code; USE thinkupstart_$code;";
        PDODAO::$PDO->exec($q);

        $query_file = $this->master_app_source_path . '/install/sql/build-db_mysql.sql';
        $q = file_get_contents($query_file);
        PDODAO::$PDO->exec($q);

        self::output("Created new database thinkupstart_$code");
        return "thinkupstart_$code";
    }

    protected function setUpDatabaseOptions($code) {
        $server_name = str_replace ("{user}", $code, $this->user_installation_url);
        $server_name = str_replace ("http://", '', $server_name);
        $q = "INSERT INTO   thinkupstart_".$code.".tu_options (namespace, option_name, option_value, last_updated,
        created) VALUES ( 'application_options',  'server_name',  '". $server_name .".thinkup.com', NOW(), NOW())";
        PDODAO::$PDO->exec($q);

        self::output("Added database options");
    }

    protected function createUsers($email) {
        $dao = new UserRouteMySQLDAO();
        //insert admin into owners
        list($admin_id, $admin_api_key) = $dao->createOwner($this->admin_email, $this->admin_password, true);

        //insert user into owners
        list($user_id, $user_api_key) = $dao->createOwner($email, $this->user_password);

        self::output("Inserted $this->admin_email password $this->admin_password and user ".$email.
        " with password $this->user_password");
        return array($admin_id, $admin_api_key, $user_id, $user_api_key);
    }

    protected function setUpServiceUser($owner_id, $route) {
        $dao = new UserRouteMySQLDAO();
        //insert Twitter user into instances
        $instance_id = $dao->insertInstance($route['twitter_user_id'], $route['twitter_username']);

        //associate owner with Twitter user in owner_instances and add auth tokens
        $dao->insertOwnerInstance($owner_id, $instance_id, $route['oauth_access_token'],
        $route['oauth_access_token_secret']);

        self::output("Inserted Twitter account and associate with ".$route['email']."");

        //add consumer key info to options
        $cfg = Config::getInstance();
        $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
        $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

        //add app keys to options table
        $dao->insertOptionValue('plugin_options-1', 'oauth_consumer_key', $oauth_consumer_key);
        $dao->insertOptionValue('plugin_options-1', 'oauth_consumer_secret', $oauth_consumer_secret);
    }

    protected function dispatchCrawlJob($installation_name) {
        $cfg = Config::getInstance();
        $jobs_array = array();
        $jobs_array[] = array(
        'installation_name'=>$installation_name,
        'timezone'=>$cfg->getValue('dispatch_timezone'),
        'db_host'=>$cfg->getValue('db_host'),
        'db_name'=>'thinkupstart_'.$installation_name,
        'db_socket'=>$cfg->getValue('dispatch_socket'),
        'db_port'=>$cfg->getValue('db_port'),
        'high_priority'=>'true'
        );
        // call Dispatcher
        $result_decoded = Dispatcher::dispatch($jobs_array);
        if (!isset($result_decoded->success)) {
            self::output($api_call . '\n'. $result_decoded);
        }
        self::output("Dispatched crawl job");
    }

    public function getMasterInstallCommitHash() {
        $cur_dir = getcwd();
        chdir($this->master_app_source_path);
        exec('git rev-parse --verify HEAD 2> /dev/null', $output);
        $commit_hash = $output[0];
        chdir($cur_dir);
        return $commit_hash;
    }

    public function getChameleonInstallCommitHash() {
        $cur_dir = getcwd();
        chdir($this->chameleon_app_source_path);
        exec('git rev-parse --verify HEAD 2> /dev/null', $output);
        $commit_hash = $output[0];
        chdir($cur_dir);
        return $commit_hash;
    }
}