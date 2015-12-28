<?php
class DispatchCrawlJobsController extends Controller {
    /**
     * Number of jobs to dispatch per call; should evenly divide jobs_to_dispatch in config file.
     * @var int
     */
    var $jobs_divisor = 10;

    public function control() {
        // get stalest subscriber installations
        $subscriber_dao = new SubscriberMySQLDAO();
        $cfg = Config::getInstance();
        $jobs_to_dispatch = $cfg->getValue('jobs_to_dispatch');

        $dispatch = "";
        try {
            $queue_size = Dispatcher::getQueueSize();
        } catch (DispatchException $e) {
            $debug .= "DispatchException ". $e->getMessage()." \n";
        }
        $debug .= "QUEUE SIZE ". Utils::varDumpToString($queue_size)." \n";

        if (is_int($queue_size) && $queue_size < $jobs_to_dispatch) {
            $number_of_calls = $jobs_to_dispatch / $this->jobs_divisor;
            while ($number_of_calls > 0) {

                $stale_installs = $subscriber_dao->getPaidStaleInstalls($hours_stale=2, $count=$this->jobs_divisor);
                if (count($stale_installs) == 0) {
                    $stale_installs = $subscriber_dao->getNotYetPaidStaleInstalls($hours_stale=4,
                        $count=$this->jobs_divisor);
                    $debug .= "Got ".count($stale_installs)." not yet paid stale installs. \n";
                } else {
                    $debug .= "Got ".count($stale_installs)." stale paid installs. \n";
                }

                //No installs to crawl? Okay, get less stale options
                if (count($stale_installs) == 0 ) {
                    $stale_installs = $subscriber_dao->getNotYetPaidStaleInstalls($hours_stale=3,
                        $count=$this->jobs_divisor);
                    $debug .= "Got ".count($stale_installs)." not yet paid less stale installs. \n";
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
                        $debug .= "Install ".$install['thinkup_username']." crawl completed ".
                            $install['last_crawl_completed']." last dispatched ". $install['last_dispatched']." \n";
                    }
                    // call Dispatcher
                    $result_decoded = Dispatcher::dispatch($jobs_array);
                    if (!isset($result_decoded->success)) {
                        $debug .= $api_call . '\n';
                        $debug .= $result;
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
                $debug .= "Problem with Dispatch; Upstart didn't dispatch jobs.";
            } else {
                $debug .= "Dispatch queue (".$queue_size.") exceeds threshold (".$jobs_to_dispatch.")";
            }
        }
        //DEBUG
        //Logger::logError($debug, __FILE__,__LINE__,__METHOD__);
    }
}