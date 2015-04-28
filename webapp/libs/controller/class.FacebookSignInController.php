<?php

class FacebookSignInController extends Controller {

    public function control() {
        $redirect_location = (isset($_GET['redir']))?$_GET['redir']:'register.php?n=facebook&level=member';
        $fbconnect_link = null;
        $cfg = Config::getInstance();
        $facebook_app_id = $cfg->getValue('facebook_app_id');

        //Plant unique token for CSRF protection during auth
        //per https://developers.facebook.com/docs/authentication/
        if (SessionCache::get('facebook_auth_csrf') == null) {
            SessionCache::put('facebook_auth_csrf', md5(uniqid(rand(), true)));
        }

        $scope = 'user_posts,email';
        $state = SessionCache::get('facebook_auth_csrf');
        $redirect_url = UpstartHelper::getApplicationURL(false, false).$redirect_location;

        $fbconnect_link = FacebookGraphAPIAccessor::getLoginURL($facebook_app_id, $scope, $state, $redirect_url);
        $this->redirect($fbconnect_link);
    }
}
