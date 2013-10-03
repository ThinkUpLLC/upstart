<?php
putenv("MODE=TESTS");
require_once 'config.tests.inc.php';

//set up 3 required constants
if ( !defined('ROOT_PATH') ) {
    define('ROOT_PATH', str_replace("\\",'/', dirname(dirname(__FILE__))) .'/');
}

if ( !defined('WEBAPP_PATH') ) {
    define('WEBAPP_PATH', ROOT_PATH . 'webapp/');
}

if ( !defined('TESTS_RUNNING') ) {
    define('TESTS_RUNNING', true);
}

//Register our lazy class loader
require_once ROOT_PATH.'webapp/extlibs/isosceles/libs/model/class.Loader.php';


//echo 'path to DAO: ' . ROOT_PATH . 'webapp/libs/dao/
//';

Loader::register(array(
ROOT_PATH . 'tests/',
ROOT_PATH . 'tests/classes/',
ROOT_PATH . 'webapp/libs/model/',
ROOT_PATH . 'webapp/libs/dao/',
ROOT_PATH . 'webapp/libs/controller/',
ROOT_PATH . 'webapp/libs/exceptions/'
));
