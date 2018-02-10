<?php
/**
 *
 */

class class_swoole_websocket_server {
    private $server;

    public function __construct()
    {
        $this->server = new swoole_websocket_server('0.0.0.0', 1988);
        // set run-time params
        $this->server->set(array(
            'worker_num' => 2,
            'daemonize' => FALSE,
            'backlog' => 128,
            'max_request' => 10,
            //'dispatch_mode' => 1,
        ));
    }

    public function onStart() {

    }

    public function onOpen(swoole_websocket_server $serv, $req) {
        var_dump($req);
        echo gettype($req);
        echo get_class($req);
        echo get_class($serv);

    }

    public function onMessage(swoole_websocket_server $serv, swoole_websocket_frame $frame) {
        switch ($frame->opcode) {
            case WEBSOCKET_OPCODE_TEXT:
                // text message
                $this->handleText($frame->data, $frame->fd);
                break;
            case WEBSOCKET_OPCODE_BINARY:
                // binary message
                $this->handleBinary($frame->data, $frame->fd);
                break;
            default:
                // do nothing
        };

        echo "有个sb[{$frame->fd}]在说： {$frame->data}\n";
        $serv->push($frame->fd, "Echo: [{$frame->data}]\\n--This is Dapianzi-carl, are you hanging on a tree?\\n");
    }

    public function onClose (swoole_websocket_server $serv, $fd){
        echo "client[$fd] closed\n";
    }

    public function onRequest (swoole_http_request $req, swoole_http_response $res) {
        $res->status(200);
        $res->header('Contype-type', 'text/html');
        $res->header('Cookie', 'aa=bb; cc=dd');
        $res->write('hello http');
    }

    public function start() {
        $this->server->on('start', [$this, 'onStart']);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('close', [$this, 'onClose']);

        $this->server->start();
    }
}
$serv = new class_swoole_websocket_server();
$serv->start();

?>