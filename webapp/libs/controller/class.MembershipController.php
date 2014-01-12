<?php
class MembershipController extends AuthController {

    public function authControl() {
        $this->setPageTitle('Subscription Info');
        $this->setViewTemplate('user.membership.tpl');

        $logged_in_user = Session::getLoggedInUser();
        $this->addToView('logged_in_user', $logged_in_user);
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail($logged_in_user);
        $this->addToView('subscriber', $subscriber);
        $config = Config::getInstance();
        $subscription_end_date = new DateTime(substr($subscriber->creation_time,8,2).'-'.
            substr($subscriber->creation_time,5,2).
            '-'.substr($subscriber->creation_time,0,4));
        date_modify($subscription_end_date,'+1 year');
        $this->addToView('subscription_end_date', $subscription_end_date);
        $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
        $config->getValue('user_installation_url'));
        $this->addToView('thinkup_url', $user_installation_url);

        return $this->generateView();
	}
}
