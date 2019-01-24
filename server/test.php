<?php
define('APPLICATION_PATH', dirname(__FILE__) . '/../');

$application = new Yaf\Application( APPLICATION_PATH . "/conf/application.ini");
$arr = [
    'username' => 'dapianzi',
    'password' => 'passwordasefa',
    'deviceid' => 'deviceid123123',
    'platform' => 1,
];

$obj = new \Message\Dapianzi\RequestLogin($arr);
$str = $obj->serializeToString();
print_r($str)."\n";
echo $obj->serializeToJsonString()."\n";
echo "\n=========\n";
$obj2 = new \Message\Dapianzi\RequestLogin();
