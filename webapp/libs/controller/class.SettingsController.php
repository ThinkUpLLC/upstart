<?php
class SettingsController extends AuthController {

    public function authControl() {
        $this->setPageTitle('Settings');
        $this->setViewTemplate('user.settings.tpl');
        $this->addHeaderJavaScript('assets/js/vendor/jstz-1.0.4.min.js');
        $logged_in_user = Session::getLoggedInUser();
        $this->addToView('logged_in_user', $logged_in_user);
        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail($logged_in_user);
        $this->addToView('subscriber', $subscriber);
        $config = Config::getInstance();
        $user_installation_url = str_replace('{user}', $subscriber->thinkup_username,
        $config->getValue('user_installation_url'));
        $this->addToView('thinkup_url', $user_installation_url);

        return $this->generateView();
	}
}
