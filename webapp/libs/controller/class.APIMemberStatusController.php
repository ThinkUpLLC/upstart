<?php
/**
 * Return the subscription status of a member by email address; for use by the insights generator plugin for
 * subscription_status-based messages in insight emails.
 */
class APIMemberStatusController extends UpstartController {

    public function control() {
        if (isset($_GET['email'])) {
            $email = $_GET['email'];
            if (UpstartHelper::validateEmail( $email ) ) {
                $subscriber_dao = new SubscriberMySQLDAO();
                try {
                    $subscriber = $subscriber_dao->getByEmail($email);

                    $response = array('email'=>$subscriber->email,
                        'subscription_status'=>$subscriber->subscription_status);
                } catch (SubscriberDoesNotExistException $e) {
                    $response = array('error'=>"Subscriber does not exist");
                }
            } else {
                $response = array('error'=>"Not a valid email address");
            }
        } else {
            $response = array('error'=>"No email specified");
        }
        $this->setJsonData($response);
        return $this->generateView();
    }
}