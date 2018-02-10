<?php
$serv = new swoole_websocket_server('0.0.0.0', 9501);
// set run-time params
$serv->set(array(
    'worker_num' => 2,
    'daemonize' => FALSE,
    'backlog' => 128,
    'max_request' => 10,
    //'dispatch_mode' => 1,
));

$serv->on('open', function(swoole_websocket_server $server, $request) {
    echo "server: handshake success with fd[{$request->fd}]\n";
});

$serv->on('message', function(\swoole_websocket_server $server, $frame){
    echo "有个sb[{$frame->fd}]在说： {$frame->data}\n";
    $server->push($frame->fd, "Echo: [{$frame->data}]\\n--This is Dapianzi-carl, are you hanging on a tree?\\n");
});

$serv->on('close', function($ser, $fd){
    echo "client[$fd] closed\n";
});
//
//$serv->on('request', function(swoole_http_request $request, swoole_http_response $response) {
//    global $serv;
//    $response->write('hello http');
//});

$serv->start();

?>