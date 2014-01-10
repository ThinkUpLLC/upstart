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
     * User installation URL
     * @var str
     */
    var $user_installation_url;
    /**
     * Installation results message
     * @var str
     */
    var $results_message = "";

    public function __construct() {
        $cfg = Config::getInstance();
        // @TODO Verify all the directories exist, throw an Exception if not
        $this->app_source_path = $cfg->getValue('app_source_path');
        $this->master_app_source_path = $cfg->getValue('master_app_source_path');
        $this->chameleon_app_source_path = $cfg->getValue('chameleon_app_source_path');
        $this->data_path = $cfg->getValue('data_path');

        $this->admin_email = $cfg->getValue('admin_email');
        $this->admin_password = $cfg->getValue('admin_password');
        // @TODO Verify this string includes {user} in it and is URLish
        $this->user_installation_url = $cfg->getValue('user_installation_url');
    }

    public function install($subscriber_id) {
        $commit_hash = self::getMasterInstallCommitHash();

        if (isset($this->app_source_path)
        && isset($this->master_app_source_path)
        && isset($this->data_path)
        && isset($this->admin_email)
        && isset($this->admin_password)
        && isset($this->user_installation_url)) {

            $subscriber = null;
            if (isset($subscriber_id)) {
                $subscriber_dao = new SubscriberMySQLDAO();
                $subscriber = $subscriber_dao->getById($subscriber_id);
            }
            if (!isset($subscriber)) {
                if (isset($subscriber_id)) {
                    throw new Exception('Subscriber doesn\'t exist');
                } else {
                    throw new Exception('No subscriber specified');
                }
            } else {
                if ($subscriber->date_installed != null && $subscrber->is_installation_active) {
                    throw new Exception('Installation already exists.');
                } elseif ($subscriber->thinkup_username == null) {
                    throw new Exception("ThinkUp username is not set.");
                } else {
                    session_write_close();
                    // Set up application files
                    self::setUpApplicationFiles($subscriber->thinkup_username);
                    // Set up installation database
                    $database_name = self::createDatabase($subscriber->thinkup_username);
                    // Upgrade database
                    $install_log_dao = new InstallLogMySQLDAO();
                    self::upgradeDatabaseToLatestMigrations($subscriber, $commit_hash, $install_log_dao,
                    $subscriber_dao);

                    // Create session API token Upstart will use to log into ThinkUp via the Session API
                    $api_key_private = hash('sha256', rand(). $subscriber->email);
                    $subscriber->api_key_private = substr($api_key_private, 0, 32);

                    self::switchToInstallationDatabase($subscriber->thinkup_username);
                    self::setUpApplicationOptions($subscriber->thinkup_username);

                    list($admin_id, $admin_api_key, $owner_id, $owner_api_key) =
                    self::createOwners($subscriber);
                    self::setUpServiceUser($owner_id, $subscriber);

                    $url = str_replace ("{user}", $subscriber->thinkup_username, $this->user_installation_url);

                    self::switchToUpstartDatabase();

                    $subscriber_dao->intializeInstallation($subscriber_id, $subscriber->api_key_private,
                    $commit_hash);
                    self::logToUserMessage("Updated waitlist with link and db name");
                    self::dispatchCrawlJob($subscriber->thinkup_username);
                    $subscriber_dao->updateLastDispatchedTime($subscriber_id);
                    $install_log_dao->insertLogEntry($subscriber_id, $commit_hash, 1, "Installed");

                    self::logToUserMessage("Complete. Log in at <a href=\"$url\" target=\"new\">".$url."</a>.");
                }
            }
            return $this->results_message;
        } else {
            throw new Exception("Sorry, the installer is not configured to run just yet. Yet!");
        }
    }

    public function uninstall($subscriber_id) {
        $subscriber = null;
        if (isset($subscriber_id)) {
            $subscriber_dao = new SubscriberMySQLDAO();
            $subscriber = $subscriber_dao->getById($subscriber_id);
        }
        if (!isset($subscriber)) {
            if (isset($subscriber_id)) {
                throw new Exception('Subscriber does not exist.');
            } else {
                throw new Exception('No subscriber specified.');
            }
        } else {
            // Check if installation exists
            if ($subscriber->date_installed == null || !$subscriber->is_installation_active) {
                throw new Exception('Installation does not exist.');
            } elseif ($subscriber->thinkup_username == null) {
                throw new Exception("ThinkUp username is not set.");
            } else {
                // De-symlink directory
                $cmd = 'rm -rf '.$this->app_source_path.$subscriber->thinkup_username;
                $cmd_result = exec($cmd, $output, $return_var);
                self::logToUserMessage("De-symlinked user application directory - ".$cmd);

                // rm -rf data directory
                $cmd = 'rm -rf '.$this->data_path.$subscriber->thinkup_username;
                $cmd_result = exec($cmd, $output, $return_var);
                self::logToUserMessage("Deleted user data directory - ". $cmd);

                // Drop database
                self::dropDatabase($subscriber->thinkup_username);

                // Set subscriber commit_hash, date_installed, is_installation_active, last_dispatched to null
                $subscriber_dao->updateDateInstalled($subscriber_id, null);
                $subscriber_dao->setInstallationActive($subscriber_id, 0);
                $subscriber_dao->updateCommitHash($subscriber_id, null);
                $subscriber_dao->resetLastDispatchedTime($subscriber_id);
                self::logToUserMessage("Updated subscriber record");

                // Insert uninstallation record in install log
                $install_log_dao = new InstallLogMySQLDAO();
                $install_log_dao->insertLogEntry( $subscriber_id, $subscriber->commit_hash, 1, "Uninstalled");
                self::logToUserMessage("Uninstallation complete.");
            }
        }
        return $this->results_message;
    }

    /**
     * Upgrade the user installation database to the latest migrations, the equivalent of running
     * upgrade.php --with-new-sql. We do this in case we're deploying code with migrations that haven't been rolled
     * into a release yet.
     * @param  Subscriber $subscriber
     * @param  str $commit_hash Commit has of master app source / user installation
     * @param  InstallLogMySQLDAO $install_log_dao
     * @param  SubscriberMySQLDAO $subscriber_dao
     * @throws Exception if unable to exec the upgrade command
     * @return void
     */
    protected function upgradeDatabaseToLatestMigrations(Subscriber $subscriber, $commit_hash,
        InstallLogMySQLDAO $install_log_dao, SubscriberMySQLDAO $subscriber_dao) {
        // Get in the right directory to exec the upgrade
        $cfg = Config::getInstance();
        $master_app_source_path = $cfg->getValue('chameleon_app_source_path');
        $cwd = getcwd();
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
            'installation_name'=>$subscriber->thinkup_username,
            'timezone'=>$cfg->getValue('dispatch_timezone'),
            'db_host'=>$cfg->getValue('db_host'),
            'db_name'=>'thinkupstart_'.$subscriber->thinkup_username,
            'db_socket'=>$cfg->getValue('dispatch_socket'),
            'db_port'=>$cfg->getValue('db_port')
        );
        $upgrade_params_json = json_encode($upgrade_params_array);

        // Capture returned JSON
        if (!exec("php upgrade.php '".$upgrade_params_json."'", $upgrade_status_json, $return_int) ) {
            throw new Exception('Unable to exec php upgrade.php '.$upgrade_params_json.
            "  Returned data was ". $return_int ." output " . Utils::varDumpToString($upgrade_status_json));
        }
        $upgrade_status_array = JSONDecoder::decode($upgrade_status_json[0], true);
        //Now that the upgrade is done, go back to the original working directory
        chdir($cwd);

        // DEBUG start
        // echo "php upgrade.php '".$upgrade_params_json."'";
        // print_r($upgrade_status_array);
        // DEBUG end

        self::switchToUpstartDatabase();

        if ($upgrade_status_array['migration_success'] === true) {
            $install_log_dao->insertLogEntry($subscriber->id, $commit_hash, 1,
            $upgrade_status_array['migration_message']);
        } else {
            // If error, set inactive, and store message, status, commit in install_log
            $subscriber_dao->setInstallationActive($subscriber->id, 0);
            $install_log_dao->insertLogEntry($subscriber->id, $commit_hash, 0,
            $upgrade_status_array['migration_message']);
        }
    }

    protected function logToUserMessage($message) {
        $this->results_message .= "<li>".$message."</li>";
    }

    private static function switchToUpstartDatabase() {
        $cfg = Config::getInstance();
        $q = "USE ". $cfg->getValue('db_name');
        PDODAO::$PDO->exec($q);
    }

    private static function switchToInstallationDatabase($thinkup_username) {
        $q = "USE ". 'thinkupstart_'.$thinkup_username;
        PDODAO::$PDO->exec($q);
    }

    /**
     * Create the user installation with a symlink in app_source_path to the master_app_source path.
     * Symlink the user installation data directory in data_path.
     * @param str $path Folder name of user installation (usually ThinkUp username)
     * @throws Exception If master_app_source is not a directory, if symlink was not created, if data dir symlink was
     *         not created
     */
    protected function setUpApplicationFiles($path) {
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
            self::logToUserMessage("Symlinked new ThinkUp installation at ". $this->app_source_path.$path);
        } else {
            $result = Utils::varDumpToString($output);
            throw new Exception("Could not create symlink from ".$this->master_app_source_path." to ".
            $this->app_source_path.$path. "<br>Command: ".$cmd. "<br> Command output: ".$result . "<br>Return var ".
            $return_var. "<br>Command result ".$cmd_result);
        }
        $cmd = 'mkdir '. $this->data_path . $path;
        exec($cmd);
        if (is_dir($this->data_path . $path )) {
            self::logToUserMessage("Created new data directory " . $this->data_path . $path);
            return true;
        } else {
            throw new Exception("Could not create new data directory ".$this->data_path . $path);
        }
    }

    protected function dropDatabase($thinkup_username) {
        $q = "DROP DATABASE IF EXISTS thinkupstart_".$thinkup_username;
        PDODAO::$PDO->exec($q);
        self::logToUserMessage("Dropped user database thinkupstart_".$thinkup_username);
    }
    /**
     * Create user installation database.
     * @param  str $thinkup_username Name of database (usually the user's ThinkUp username)
     * @return str Newly-created database name
     * @throws PDOException
     */
    protected function createDatabase($thinkup_username) {
        $q = "CREATE DATABASE thinkupstart_$thinkup_username; USE thinkupstart_$thinkup_username;";
        PDODAO::$PDO->exec($q);

        $query_file = $this->master_app_source_path . '/install/sql/build-db_mysql-upcoming-release.sql';
        $q = file_get_contents($query_file);
        PDODAO::$PDO->exec($q);

        self::logToUserMessage("Created new database thinkupstart_$thinkup_username");
        return "thinkupstart_$thinkup_username";
    }

    /**
     * Set any required application options, like server_name.
     * @param str $thinkup_username User installation username.
     * @return void
     */
    protected function setUpApplicationOptions($thinkup_username) {
        $server_name = self::getInstallationServerName($thinkup_username);
        $tu_tables_dao = new ThinkUpTablesMySQLDAO();
        $tu_tables_dao->insertOptionValue( 'application_options', 'server_name', $server_name);
        self::logToUserMessage("Added server_name application option (".$server_name.")");
    }

    /**
     * Get the servername for the user installation. For subdomain setups, this should be username.thinkup.com.
     * For subdirectory setups, this should be example.com.
     * @param  str $thinkup_username ThinkUp username
     * @return str Installation server name
     */
    public function getInstallationServerName($thinkup_username){
        $server_name = str_replace ("{user}", $thinkup_username, $this->user_installation_url);
        $server_name = str_replace ("http://", '', $server_name);
        $server_name = str_replace ("https://", '', $server_name);
        if (strpos($server_name, '/') !== false) {
            $split_server_name = explode('/', $server_name);
            $server_name = $split_server_name[0];
        }
        $server_name = str_replace ("/", '', $server_name);
        return $server_name;
    }

    protected function createOwners(Subscriber $subscriber) {
        $tu_tables_dao = new ThinkUpTablesMySQLDAO();
        //insert admin into owners
        list($admin_pwd_salt, $admin_hashed_pwd) = $tu_tables_dao->saltAndHashPwd($this->admin_email,
            $this->admin_password);
        list($admin_id, $admin_api_key) = $tu_tables_dao->createOwner($this->admin_email, $admin_hashed_pwd,
            $admin_pwd_salt, null, 'America/New_York', true);

        //insert user into owners
        list($user_id, $user_api_key) = $tu_tables_dao->createOwner($subscriber->email, $subscriber->pwd,
            $subscriber->pwd_salt, $subscriber->membership_level, $subscriber->timezone, false,
            $subscriber->api_key_private);

        self::logToUserMessage("Inserted $this->admin_email and user ".$subscriber->email);
        return array($admin_id, $admin_api_key, $user_id, $user_api_key);
    }

    protected function setUpServiceUser($owner_id, $subscriber) {
        $tu_tables_dao = new ThinkUpTablesMySQLDAO();
        //insert Twitter user into instances
        $instance_id = $tu_tables_dao->insertInstance($subscriber->network_user_id, $subscriber->network_user_name);

        //associate owner with Twitter user in owner_instances and add auth tokens
        $tu_tables_dao->insertOwnerInstance($owner_id, $instance_id, $subscriber->oauth_access_token,
        $subscriber->oauth_access_token_secret);

        self::logToUserMessage("Inserted Twitter account and associate with ".$subscriber->email."");

        //add consumer key info to options
        $cfg = Config::getInstance();
        $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
        $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

        //add app keys to options table
        $tu_tables_dao->insertOptionValue('plugin_options-1', 'oauth_consumer_key', $oauth_consumer_key);
        $tu_tables_dao->insertOptionValue('plugin_options-1', 'oauth_consumer_secret', $oauth_consumer_secret);
    }

    protected function dispatchCrawlJob($thinkup_username) {
        $cfg = Config::getInstance();
        $jobs_array = array();
        $jobs_array[] = array(
            'installation_name'=>$thinkup_username,
            'timezone'=>$cfg->getValue('dispatch_timezone'),
            'db_host'=>$cfg->getValue('db_host'),
            'db_name'=>'thinkupstart_'.$thinkup_username,
            'db_socket'=>$cfg->getValue('dispatch_socket'),
            'db_port'=>$cfg->getValue('db_port'),
            'high_priority'=>'true'
        );
        if (!UpstartHelper::isTest()) {
            // call Dispatcher
            $result_decoded = Dispatcher::dispatch($jobs_array);
            self::logToUserMessage("Dispatched crawl job: " .Utils::varDumpToString($result_decoded));
        } else {
            self::logToUserMessage("Didn't dispatch crawl job since this is a test");
        }
    }

    /**
     * Get the commit hash of the master app source.
     * @return str git commit hash
     */
    public function getMasterInstallCommitHash() {
        return self::getCommitHash($this->master_app_source_path);
    }

    /**
     * Get the commit hash of the chamelon app source.
     * @return str git commit hash
     */
    public function getChameleonInstallCommitHash() {
        return self::getCommitHash($this->chameleon_app_source_path);
    }

    /**
     * Get the commit hash of the git repository in a given directory.
     * @param str $path Location of git repository
     * @return str git commit hash
     */
    private function getCommitHash($path) {
        $cur_dir = getcwd();
        chdir($path);
        exec('git rev-parse --verify HEAD 2> /dev/null', $output);
        $commit_hash = $output[0];
        chdir($cur_dir);
        return $commit_hash;
    }
}