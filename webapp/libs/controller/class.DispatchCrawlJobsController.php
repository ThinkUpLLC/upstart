<?php
class DispatchCrawlJobsController extends Controller {
    public function control() {
        // get stalest active user routes
        $dao = new UserRouteMySQLDAO();
        $stale_routes = $dao->getStaleRoutes();

        if (count($stale_routes) > 0 ) {
            $cfg = Config::getInstance();
            $jobs_array = array();
            // json_encode them
            foreach ($stale_routes as $route) {
                $jobs_array[] = array(
            'installation_name'=>$route['twitter_username'],
            'timezone'=>$cfg->getValue('dispatch_timezone'),
            'db_host'=>$cfg->getValue('db_host'),
            'db_name'=>$route['database_name'],
            'db_socket'=>$cfg->getValue('dispatch_socket'),
            'db_port'=>$cfg->getValue('db_port')
                );
            }
            // call Dispatch endpoint
            $api_call = self::buildAPICallURL($jobs_array);
            $result = self::getURLContents($api_call, $cfg->getValue('dispatch_http_username'),
            $cfg->getValue('dispatch_http_passwd'));
            echo $api_call . '\n';
            echo $result;

            // update last_dispatched_date on stale user routes
            foreach ($stale_routes as $route) {
                $dao->updateLastDispatchedTime($id=$route['id']);
            }
        }
    }

    private function buildAPICallURL($jobs_array) {
        $cfg = Config::getInstance();
        $base_url = $cfg->getValue('dispatch_endpoint');
        $auth_token = $cfg->getValue('dispatch_auth_token');
        $jobs = str_replace('\/', '/', json_encode($jobs_array));
        $params = array('auth_token'=>$auth_token, 'jobs'=>$jobs);
        $query = http_build_query($params);
        return $base_url.'?'.$query;
    }

    private static function getURLContents($url, $username, $password) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_USERPWD, $username . ":" . $password);
        $contents = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        if (isset($contents)) {
            return $contents;
        } else {
            return null;
        }
    }

}