<?php
class CheckUsernameController extends Controller {
    public function control() {
        $this->disableCaching();
    	$payload = array();
    	if (isset($_GET['un'])) {
    		$subscriber_dao = new SubscriberMySQLDAO();
    		$is_taken = $subscriber_dao->isUsernameTaken($_GET['un']);
    		$payload['available'] = ($is_taken)?false:true;
    	} else {
    		$payload['error'] = 'No username specified';
    	}
    	$this->setJsonData($payload);
        return $this->generateView();
    }
}
