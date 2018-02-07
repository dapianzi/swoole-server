<?php
/*
 * swoole server test
 */
$serv = new swoole_server('0.0.0.0', 2327, SWOOLE_BASE, SWOOLE_SOCK_TCP);
// set run-time params
$serv->set(array(
    'worker_num' => 4,
    #'daemonize' => TRUE,
    'backlog' => 128,
    'max_request' => 10,
    #'dispatch_mode' => 1,
));

function server_status($serv){
    $str = "\nmaster pid: ".$serv->master_pid;
    $str.= "\tmanager_pid: ".$serv->manager_pid;
    $str.= "\tworker_id: ".$serv->worker_id;
    $str.= "\tworker_pid: ".$serv->worker_pid;
    $str.= "\tconnections: ".count($serv->connections);
    $str.= "\n===================\n";
    return $str;
}

$serv->on('connect', function($serv, $fd) {
    echo "Client[{$fd}] connected.\n".server_status($serv);
});
$serv->on('receive', function($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Swoole echo: Hello<{$from_id}>, I\'m Dapianzi. How are you?\n".server_status($serv));
    //$serv->close($fd);
});
$serv->on('close', function($serv, $fd) {
    echo "Client[{$fd}]: close.\n";
});
$serv->start();
