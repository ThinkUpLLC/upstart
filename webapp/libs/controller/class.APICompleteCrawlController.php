<?php
/**
 * Set a subscriber's last_crawl time to now.
 */
class APICompleteCrawlController extends Controller {

    public function control() {
        if (isset($_GET['u'])) {
            $thinkup_username = $_GET['u'];
            $subscriber_dao = new SubscriberMySQLDAO();
            $total_updated = $subscriber_dao->setLastCrawl($thinkup_username);

            if ($total_updated > 0) {
                $response = array('thinkup_username'=>$thinkup_username, 'result'=>'success');
            } else {
                $response = array('thinkup_username'=>$thinkup_username, 'result'=>'none');
            }
        } else {
            $response = array('error'=>"No username specified");
        }
        $this->setJsonData($response);
        return $this->generateView();
    }
}