<?php
class DispatchCrawlJobsController extends Controller {
    public function control() {
        // get stalest subscriber installations
        $subscriber_dao = new SubscriberMySQLDAO();
        $cfg = Config::getInstance();
        $jobs_to_dispatch = $cfg->getValue('jobs_to_dispatch');

        $queue_size = Dispatcher::getQueueSize();
        //        echo "QUEUE SIZE ".$queue_size;
        if (is_int($queue_size) && $queue_size < $jobs_to_dispatch) {
            $number_of_calls = $jobs_to_dispatch / 25;
            while ($number_of_calls > 0) {

                $stale_installs = $subscriber_dao->getStaleInstalls10kAndUp();
                if (count($stale_installs) == 0) {
                    $stale_installs = $subscriber_dao->getStaleInstalls1kTo10k();
                }
                if (count($stale_installs) == 0) {
                    $stale_installs = $subscriber_dao->getStaleInstalls();
                }

                if (count($stale_installs) > 0 ) {
                    $jobs_array = array();
                    // json_encode them
                    foreach ($stale_installs as $install) {
                        $jobs_array[] = array(
                        'installation_name'=>$install['thinkup_username'],
                        'timezone'=>$cfg->getValue('dispatch_timezone'),
                        'db_host'=>$cfg->getValue('tu_db_host'),
                        'db_name'=>$cfg->getValue('user_installation_db_prefix').$install['thinkup_username'],
                        'db_socket'=>$cfg->getValue('tu_db_socket'),
                        'db_port'=>$cfg->getValue('tu_db_port')
                        );
                    }
                    // call Dispatcher
                    $result_decoded = Dispatcher::dispatch($jobs_array);
                    if (!isset($result_decoded->success)) {
                        echo $api_call . '\n';
                        echo $result;
                    }

                    // update last_dispatched_date on stale user routes
                    foreach ($stale_installs as $install) {
                        $subscriber_dao->updateLastDispatchedTime($id=$install['id']);
                    }
                }
                $number_of_calls--;
            }
        } else {
            if ($queue_size === false) {
                echo "Problem with Dispatch; Upstart didn't dispatch jobs.";
            } else {
                //echo "Dispatch queue (".$queue_size.") exceeds threshold (".$jobs_to_dispatch.")";
            }
        }
    }
}