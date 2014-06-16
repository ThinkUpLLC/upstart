<?php

/**
 * This controller sends payment reminders via email to ThinkUp members who are currently in free trial mode.
 *
 * TODO: Fill in logic similar to RemindAbandonsAboutPaymentController for 4 reminders over 14-day trial period.
 */
class RemindTrialersAboutPaymentController extends Controller {
    public function control() {
        $subscriber_dao = new SubscriberMySQLDAO();
        $email_view_mgr = new ViewManager();
        $email_view_mgr->caching=false;
        $template_name = "Upstart System Messages";
        $api_key = Config::getInstance()->getValue('mandrill_api_key_for_payment_reminders');

        //TODO Fill in body with logic here
    }
}