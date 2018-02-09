<?php
$serv = new swoole_websocket_server('0.0.0.0', 9501);

$serv->on('open', function(swoole_websocket_server $server, $request) {
    echo "server: handshake success with fd[{$request->fd}]\n";
});

$serv->on('message', function(\swoole_websocket_server $server, $frame){
    echo "receive from fd[{$frame->fd}], opcode: {$frame->opcode}, fin: {$frame->finish}\n";
    $server->push($frame->fd, "This is Dapianzi-carl, are you hanging on a tree?");
});

$serv->on('colse', function($ser, $fd){
    echo "client[$fd] closed\n";
});

$serv->start();

?>