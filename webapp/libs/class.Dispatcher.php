<?php
class Dispatcher {
    public function dispatch($jobs) {
        $cfg = Config::getInstance();
        $api_call = self::buildAPICallURL($jobs);
        //echo $api_call;
        $result = self::getURLContents($api_call, $cfg->getValue('dispatch_http_username'),
        $cfg->getValue('dispatch_http_passwd'));
        //print_r($result);
        $result_decoded = JSONDecoder::decode($result);
        //print_r($result_decoded);
        return $result_decoded;
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