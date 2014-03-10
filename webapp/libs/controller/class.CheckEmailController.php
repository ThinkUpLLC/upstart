<?php
class CheckEmailController extends Controller {
    public function control() {
        $this->disableCaching();
    	$payload = array();
    	if (isset($_GET['em'])) {
    		$subscriber_dao = new SubscriberMySQLDAO();
    		$is_taken = $subscriber_dao->doesSubscriberEmailExist($_GET['em']);
    		$payload['available'] = ($is_taken)?false:true;
    	} else {
    		$payload['error'] = 'No email specified';
    	}
    	$this->setJsonData($payload);
        return $this->generateView();
    }
}
