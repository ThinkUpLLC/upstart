<?php
/**
 * Show subscriber information and offer actions to modify/manage.
 * @author gina
 */
class ManageSubscriberController extends Controller {
    public function control() {
        $this->disableCaching();
        $this->setViewTemplate('admin-subscriber.tpl');

        $subscriber_id = (isset($_GET['id']))?(integer)$_GET['id']:false;
        if ($subscriber_id !== false ) {
            //Get subscriber and assign to view
            $subscriber_dao = new SubscriberMySQLDAO();
            $subscriber = $subscriber_dao->getByID($subscriber_id);

            if (isset($subscriber)) {
                $this->addToView('subscriber', $subscriber);

                //Get authorizations and assign to view
                $subscriber_auth_dao = new SubscriberAuthorizationMySQLDAO();
                $authorizations = $subscriber_auth_dao->getBySubscriberID($subscriber_id);
                $this->addToView('authorizations', $authorizations);

                //If action specified, perform it
                if (isset($_GET['action']) && $_GET['action'] == 'archive') {
                    $result = $this->archiveSubscriber($subscriber_id);
                    if ($result) {
                        $this->addSuccessMessage("Subscriber archived.");
                        $this->addToView('subscriber', null);
                    }
                }
            } else {
                $this->addErrorMessage("Subscriber does not exist.");
            }
        } else {
            $this->addErrorMessage("No subscriber specified.");
        }

        return $this->generateView();
    }

    private function archiveSubscriber($subscriber_id) {
        //Archive subscriber and auth
        $result = 0;
        $subscriber_dao = new SubscriberMySQLDAO();
        $result += $subscriber_dao->archiveSubscriber($subscriber_id);

        //Delete auth
        $auth_dao = new AuthorizationMySQLDAO();
        $result += $auth_dao->deleteBySubscriberID($subscriber_id);

        //Delete sub_auth
        $subscriber_auth_dao = new SubscriberAuthorizationMySQLDAO();
        $result += $subscriber_auth_dao->deleteBySubscriberID($subscriber_id);

        //Delete subscriber
        $result += $subscriber_dao->deleteBySubscriberID($subscriber_id);
        return ($result > 0);
    }
}