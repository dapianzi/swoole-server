<?php
$http = new swoole_http_server('127.0.0.1', 8089);

$http->on('request', function($request, $response){

    $response->end("<h1> Hello Swoole. # </h1>");
});

$http->start();

?>