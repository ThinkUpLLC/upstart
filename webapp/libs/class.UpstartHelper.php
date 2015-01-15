<?php
class UpstartHelper {
    /**
     * Validate email address
     * This method uses a raw regex instead of filter_var because as of PHP 5.3.3,
     * filter_var($email, FILTER_VALIDATE_EMAIL) validates local email addresses.
     * From 5.2 to 5.3.3, it does not.
     * Therefore, this method uses the PHP 5.2 regex instead of filter_var in order to return consistent results
     * regardless of PHP version.
     * http://svn.php.net/viewvc/php/php-src/trunk/ext/filter/logical_filters.c?r1=297250&r2=297350
     *
     * @param str $email Email address to validate
     * @return bool Whether or not it's a valid address
     */
    public static function validateEmail($email = '') {
        //return filter_var($email, FILTER_VALIDATE_EMAIL));
        $reg_exp = "/^((\\\"[^\\\"\\f\\n\\r\\t\\b]+\\\")|([A-Za-z0-9_][A-Za-z0-9_\\!\\#\\$\\%\\&\\'\\*\\+\\-\\~\\".
        "/\\=\\?\\^\\`\\|\\{\\}]*(\\.[A-Za-z0-9_\\!\\#\\$\\%\\&\\'\\*\\+\\-\\~\\/\\=\\?\\^\\`\\|\\{\\}]*)*))@((\\".
        "[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.".
        "((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\\])|".
        "(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.".
        "((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|".
        "((([A-Za-z0-9])(([A-Za-z0-9\\-])*([A-Za-z0-9]))?(\\.(?=[A-Za-z0-9\\-]))?)+[A-Za-z]+))$/D";
        //return (preg_match($reg_exp, $email) === false)?false:true;
        return (preg_match($reg_exp, $email)>0)?true:false;
    }
    public static function validatePassword($passwd) {
        return (preg_match("/(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9])/", $passwd));
    }
    /**
     * Get application URL
     * @param bool $replace_localhost_with_ip
     * @param bool $use_filesystem_path Use filesystem path instead of path specified in config.inc.php
     * @return str application URL
     */
    public static function getApplicationURL($replace_localhost_with_ip = false, $use_filesystem_path = true,
    $should_url_encode = true) {
        $server = self::getApplicationHostName();
        if ($replace_localhost_with_ip) {
            $server = ($server == 'localhost')?'127.0.0.1':$server;
        }
        if ($use_filesystem_path) {
            $site_root_path = self::getSiteRootPathFromFileSystem();
        } else {
            $cfg = Config::getInstance();
            $site_root_path = $cfg->getValue('site_root_path');
        }
        if ($should_url_encode) {
            //URLencode everything except spaces in site_root_path
            $site_root_path = str_replace('%2f', '/', strtolower(urlencode($site_root_path)));
        }
        if  (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') { //non-standard port
            if (isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == '443') { //account for standard https port
                $port = '';
            } else {
                $port = ':'.$_SERVER['SERVER_PORT'];
            }
        } else {
            $port = '';
        }
        return 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$server.$port.$site_root_path;
    }

    /**
     * Get the application's host name or server name, i.e., example.com.
     * @return str Host name either set by PHP global vars or stored in the database
     */
    public static function getApplicationHostName() {
        //First attempt to get the host name without querying the database
        //Try SERVER_NAME
        $server = empty($_SERVER['SERVER_NAME']) ? '' : $_SERVER['SERVER_NAME'];
        //Second, try HTTP_HOST
        if ($server == '' ) {
            $server = empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST'];
        }
        //Finally fall back to stored application setting set by Installer::storeServerName
        if ($server == '') {
            $config = Config::getInstance();
            $server = $config->getValue('upstart_host');
        }
        //domain name is always lowercase
        $server = strtolower($server);
        return $server;
    }

    public static function getSiteRootPathFromFileSystem() {
        $dirs_under_root = array('admin', 'tests', 'user');
        if (isset($_SERVER['PHP_SELF'])) {
            $current_script_path = explode('/', $_SERVER['PHP_SELF']);
        } else {
            $current_script_path = array();
        }
        array_pop($current_script_path);
        if ( in_array( end($current_script_path), $dirs_under_root ) ) {
            array_pop($current_script_path);
        }
        $current_script_path = implode('/', $current_script_path) . '/';
        return $current_script_path;
    }
    /**
     * Return whether currently in test mode.
     * @return bool Whether in test mode
     */
    public static function isTest() {
        return (isset($_SESSION["MODE"]) && $_SESSION["MODE"] == "TESTS") || getenv("MODE")=="TESTS";
    }
    /**
     * Return whether currently on staging server (stage.thinkup.com).
     * @return bool Whether on staging
     */
    public static function isStage() {
        return ($_SERVER['SERVER_NAME'] == 'stage.thinkup.com');
    }
    /**
     * Return whether or not a ThinkUp username is valid - between 3 and 15 chars, alphanumeric, no spaces,
     * allow underscores but not dashes.
     * @param  str  $username Username to test
     * @return bool
     */
    public static function isUsernameValid($username) {
        $is_valid = true;
        //between 3 and 15 chars long
        if (strlen($username) < 3 || strlen($username) > 15) {
            $is_valid = false;
        }
        //alphanumeric, no spaces, allow underscores but not dashes
        if ($is_valid) {
            return preg_match("/^[[A-Za-z0-9_]+$/", $username, $matches);
        }
        return $is_valid;
    }

    /**
     * Get an array of time zone options formatted for display in a select field.
     *
     * @return arr An associative array of options, ready for optgrouping.
     */
    public static function getTimeZoneList() {
        $tz_options = timezone_identifiers_list();
        $view_tzs = array();

        foreach ($tz_options as $option) {
            $option_data = explode('/', $option);

            // don't allow user to select UTC
            if ($option_data[0] == 'UTC') {
                continue;
            }

            // handle things like the many Indianas
            if (isset($option_data[2])) {
                $option_data[1] = $option_data[1] . ': ' . $option_data[2];
            }

            // avoid undefined offset error
            if (!isset($option_data[1])) {
                $option_data[1] = $option_data[0];
            }

            $view_tzs[$option_data[0]][] = array(
                'val' => $option,
                'display' => str_replace('_', ' ', $option_data[1])
            );
        }
        return $view_tzs;
    }

    /**
     * Check if URL GET params are set.
     * @param  array  $params
     * @return bool
     */
    public static function areGetParamsSet($params = array()) {
        foreach ($params as $param) {
            if (!isset($_GET[$param])) return false;
        }
        return true;
    }

    /**
     * Post a message to a ThinkUp Slack channel.
     * @param str $channel like #signups
     * @param str $text
     * @return str contents
     */
    public static function postToSlack($channel, $text, $bot_name = 'upstartbot') {
        $url = 'https://thinkup.slack.com/services/hooks/incoming-webhook?token=mPEOeIpng7h2EIskwtNd9hNF';

        $payload = '{"channel": "'.$channel.'", "username": "'.$bot_name.'", "text": "'. $text.
            '", "icon_emoji": ":cubimal_chick:"}';
        //debug
        //echo $payload;
        $fields = array('payload'=>$payload);

        if (!self::isTest() && !self::isStage()) {
            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //execute post
            $contents = curl_exec($ch);

            //close connection
            curl_close($ch);

            if (isset($contents)) {
                return $contents;
            } else {
                return null;
            }
        } else {
            return $payload;
        }
    }

    public static function buildChartImageURL($first_data_set, $second_data_set = null, $y_axis_divisor = 5,
        $chart_key = null) {
        $chart_url = 'https://chart.googleapis.com/chart?cht=lc&chs=1000x300&chdlp=t&chd=t:';
        // First data set
        end($first_data_set);
        $last_key = key($first_data_set);
        foreach ($first_data_set as $date=>$total) {
            $chart_url .= $total;
            if ($date !== $last_key) {
                $chart_url .= ',';
            }
        }
        if (isset($second_data_set)) {
            // Second data set
            $chart_url .= '|';
            end($second_data_set);
            $last_key = key($second_data_set);
            foreach ($second_data_set as $date=>$total) {
                $chart_url .= $total;
                if ($date !== $last_key) {
                    $chart_url .= ',';
                }
            }
        }
        // X-axis
        $chart_url .= '&chxt=x,y&chxl=0:|';
        if (sizeof($first_data_set) >= 28) { // On big charts, only label every 5 X-axis items
            $i = 0;
            foreach ($first_data_set as $date=>$total) {
                if ($i % 5 == 0) {
                    $chart_url .= substr($date, 5); //Remove year from date
                } else {
                    $chart_url .= '';
                }
                $chart_url .= '|';
                $i++;
            }
        } else {
            foreach ($first_data_set as $date=>$total) {
                $chart_url .= substr($date, 5); //Remove year from date
                $chart_url .= '|';
            }
        }
        $chart_url .='1:|';
        // Y-axis markers
        asort($first_data_set, SORT_NUMERIC);
        $max_count = array_pop($first_data_set);
        $y_axis_max = ((floor($max_count / 2 )) + 1) * 2;
        $total_y_axis_markers = $y_axis_max / 2;
        $i = 0;
        while ($i < $y_axis_max ) {
            $i = $i+$y_axis_divisor;
            $chart_url .= '|'.$i;
        }
        $chart_url .= '&chds=0,'.$i;
        if (isset($second_data_set)) {
            $chart_url .= "&chco=9dd767,00aeef";
        } else {
            $chart_url .= "&chco=00aeef";
        }
        $chart_url .= "&chg=50,10";
        if (isset($chart_key)) {
            $chart_url .= "&chdl=".$chart_key;
        }
        return $chart_url;
    }
}