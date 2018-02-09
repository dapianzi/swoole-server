<?php

define('APPLICATION_PATH', dirname(__FILE__) . '/../');

define('BASE_EXCEPTION', 10000);

$application = new Yaf_Application( APPLICATION_PATH . "/conf/application.ini");

$application->bootstrap()->run();

?>
