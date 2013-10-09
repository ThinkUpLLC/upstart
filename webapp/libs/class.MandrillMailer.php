<?php

class MandrillMailer {

    const FROM_EMAIL = 'gina@thinkup.com';

    const FROM_NAME = 'Gina and Anil at ThinkUp';

    const CONFIRMATION_TEMPLATE_NAME = 'Subscription pledge confirmation';

    const CONFIRMATION_SUBJECT = "Thanks! Confirm your email address";

    public static function sendConfirmationEmail($recipient_address, $recipient_name, $confirm_email_url) {
        $config = Config::getInstance();
        $mandrill_api_key = $config->getValue('mandrill_api_key');

        try {
            $mandrill = new Mandrill($mandrill_api_key);

            $template_name = self::CONFIRMATION_TEMPLATE_NAME;
            $template_content = array(
            array(
            'name' => self::CONFIRMATION_TEMPLATE_NAME,
            'content' => null
            )
            );

            $message = array(
        'html' => '',
        'text' => '',
        'subject' => self::CONFIRMATION_SUBJECT,
        'from_email' => self::FROM_EMAIL,
        'from_name' => self::FROM_NAME,
        'to' => array(
            array(
                'email' => $recipient_address,
                'name' => $recipient_name
            )
            ),
        'headers' => array('Reply-To' => self::FROM_EMAIL),
        'important' => false,
        'track_opens' => null,
        'track_clicks' => null,
        'auto_text' => null,
        'auto_html' => null,
        'inline_css' => null,
        'url_strip_qs' => null,
        'preserve_recipients' => null,
        'view_content_link' => null,
        'bcc_address' => null,
        'tracking_domain' => null,
        'signing_domain' => null,
        'return_path_domain' => null,
        'merge' => true,
        'global_merge_vars' => null,
        'merge_vars' => array(
            array(
                'rcpt' => $recipient_address,
                'vars' => array(
            array(
                        'name' => 'ACTIVATION_LINK',
                        'content' => $confirm_email_url
            )
            )
            )
            ),
        'tags' => array('pledge-subscription-confirmation'),
        'subaccount' => null,
        'google_analytics_domains' => null,
        'google_analytics_campaign' => null,
        'metadata' => array('website' => 'www.thinkup.com'),
        'recipient_metadata' => array(
            array(
                'rcpt' => $recipient_address,
                'values' => null
            )
            ),
        'attachments' => null,
        'images' => null
            );
            $async = false;
            $ip_pool = 'Main Pool';
            //send_at If you specify a time in the past, the message will be sent immediately.
            //$send_at = '2013-10-01 12:00:00';
            //$result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);
            //$result = $mandrill->messages->send($message, $async, $ip_pool);

            $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message, $async,
            $ip_pool);

            //DEBUG
            //print_r($result);
        } catch (Mandrill_Error $e) {
            throw new Exception('An error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
        }
    }

}