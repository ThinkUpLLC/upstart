<?php
class ChooseUsernameController extends AuthController {

    public function authControl() {
        $this->setPageTitle('Choose your ThinkUp username');
        $this->setViewTemplate('user.settings.tpl');

        $logged_in_user = Session::getLoggedInUser();
        $this->addToView('logged_in_user', $logged_in_user);

        $subscriber_dao = new SubscriberMySQLDAO();
        $subscriber = $subscriber_dao->getByEmail($logged_in_user);

        if (isset($_POST['username'])) {
            $username = strtolower($_POST['username']);
            // Throw an error if there's an invalid character
            if (!UpstartHelper::isUsernameValid($username)) {
                $this->addErrorMessage("Your username must be 3-15 characters and contain only numbers and ".
                    "letters.");
            } else {
                try {
                    $set_username_result = $subscriber_dao->setUsername($subscriber->id, $username);
                    if ( $set_username_result ) {
                        $this->addSuccessMessage("Saved username $username.");
                        $subscriber->thinkup_username = $username;
                    } else {
                        $this->addErrorMessage("Sorry, that username is already taken. Please try again.");
                    }
                } catch (DuplicateSubscriberUsernameException $e) {
                    $this->addErrorMessage("Sorry, that username is already taken. Please try again.");
                }
            }
        }
        $this->addToView('subscriber', $subscriber);
        return $this->generateView();
	}
}
