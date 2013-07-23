<?php
class DispatchCrawlJobsController extends Controller {
    public function control() {
        // get stalest active user routes
        $dao = new UserRouteMySQLDAO();
        $cfg = Config::getInstance();
        $jobs_to_dispatch = $cfg->getValue('jobs_to_dispatch');

        $queue_size = Dispatcher::getQueueSize();
        //        echo "QUEUE SIZE ".$queue_size;
        if (is_int($queue_size) && $queue_size < $jobs_to_dispatch) {
            $number_of_calls = $jobs_to_dispatch / 25;
            while ($number_of_calls > 0) {

                $stale_routes = $dao->getStaleRoutes();

                if (count($stale_routes) > 0 ) {
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
                    // call Dispatcher
                    $result_decoded = Dispatcher::dispatch($jobs_array);
                    if (!isset($result_decoded->success)) {
                        echo $api_call . '\n';
                        echo $result;
                    }

                    // update last_dispatched_date on stale user routes
                    foreach ($stale_routes as $route) {
                        $dao->updateLastDispatchedTime($id=$route['id']);
                    }
                }
                $number_of_calls--;
            }
        } else {
            if ($queue_size === false) {
                echo "Problem with Dispatch; Upstart didn't dispatch jobs.";
            } else {
                echo "Dispatch queue (".$queue_size.") exceeds threshold (".$queue_size.")";
            }
        }
    }
}