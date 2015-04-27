<?php

class TwitterSignInController extends Controller {

    public function control() {
        $redirect_url = (isset($_GET['redir']))?$_GET['redir']:'register.php?n=twitter&level=member';
        $cfg = Config::getInstance();
        $oauth_consumer_key = $cfg->getValue('oauth_consumer_key');
        $oauth_consumer_secret = $cfg->getValue('oauth_consumer_secret');

        $to = new TwitterOAuth($oauth_consumer_key, $oauth_consumer_secret);
        $tok = $to->getRequestToken(UpstartHelper::getApplicationURL(false, false)
            .$redirect_url);

        if (isset($tok['oauth_token'])) {
            $token = $tok['oauth_token'];
            SessionCache::put('oauth_request_token_secret', $tok['oauth_token_secret']);
            // Build Twitter authorization URL
            $twitter_auth_link = $to->getAuthorizeURL($token);
        } else {
            $this->addErrorMessage($generic_error_msg);
            Logger::logError('Twitter auth link failure, token not set '.htmlentities(Utils::varDumpToString($tok)),
                __FILE__,__LINE__,__METHOD__);
        }
        $this->redirect($twitter_auth_link);
    }
}
