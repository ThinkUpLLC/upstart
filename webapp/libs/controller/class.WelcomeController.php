<?php
class WelcomeController extends AuthController {
    /*
     * @var array Options for notification frequency
     */
    var $notification_frequencies = array('daily'=>'Daily','weekly'=>'Weekly', 'never'=>'Never');

    public function authControl() {
        $this->disableCaching();
        $this->setPageTitle('Welcome');
        $this->setViewTemplate('welcome.tpl');

        $logged_in_user = Session::getLoggedInUser();
        $this->addToView('logged_in_user', $logged_in_user);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail($logged_in_user);
        $this->addToView('subscriber', $subscriber);

        return $this->generateView();
    }
}
