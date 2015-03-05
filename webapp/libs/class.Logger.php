<?php

class Logger {
    /**
     * Log application error.
     * @param  str $title
     * @param  str $filename
     * @param  str $line_number
     * @param  str $method
     * @return void
     */
    public static function logError($title, $filename, $line_number, $method) {
        exec('git rev-parse --verify HEAD 2> /dev/null', $output);
        $commit_hash = $output[0];
        $debug = $title.'

';
        if (isset($_SESSION) && sizeof($_SESSION) > 0) {
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