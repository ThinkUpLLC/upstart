<?php

abstract class UpstartController extends Controller {
    /**
     * Redirect destination.
     * @var str
     */
    var $redirect_destination;

    /**
     * Generic application error message.
     * @var str
     */
    var $generic_error_msg;

    public function __construct($session_started=false) {
        parent::__construct($session_started);
        $this->generic_error_msg = 'Oops! Something went wrong and our team is looking into it. '.
        'Please try again or contact us at help@thinkup.com to get help.';
    }
    /**
     * Send Location header
     * @param str $destination
     * @return bool Whether or not redirect header was sent
     */
    protected function redirect($destination=null) {
        if (!isset($destination)) {
            $destination = Utils::getSiteRootPathFromFileSystem();
        }
        $this->redirect_destination = $destination; //for validation
        if ( !headers_sent() ) {
            header('Location: '.$destination);
            return true;
        } else {
            return false;
        }
    }
    /**
     * Log application error.
     * @param  str $title
     * @param  str $filename
     * @param  str $line_number
     * @param  str $method
     * @return void
     */
    protected function logError($title, $filename, $line_number, $method) {
        exec('git rev-parse --verify HEAD 2> /dev/null', $output);
        $commit_hash = $output[0];
        $debug = $title.'

';
        if (sizeof($_SESSION) > 0) {
            $debug .= 'SESSION:
'.Utils::varDumpToString($_SESSION);
        }
        if (sizeof($_GET) > 0) {
            $debug .= 'GET:
'.Utils::varDumpToString($_GET);
        }
        if (sizeof($_POST) > 0) {
            $debug .= 'POST:
'.Utils::varDumpToString($_POST);
        }
        $error_dao = new ErrorLogMySQLDAO();
        $error_dao->insert($commit_hash, $filename, $line_number, $method, $debug);
    }
}