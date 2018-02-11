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
            'task_worker_num' => 2,
            'daemonize' => FALSE,
            'backlog' => 128,
            'max_request' => 10,
            //'dispatch_mode' => 1,
        ));
    }

    /**
     * worker
     * @param $serv
     * @param $worker_id
     */
    public function onWorkerStart($serv, $worker_id) {
//        var_dump($this->server);
//        var_dump($serv);
        // init redis
        $redis = new Redis;
        $redis->connect('127.0.0.1', 6379);
        $serv->redis = $redis;
        //var_dump($serv);
    }

    public function onStart(swoole_websocket_server $serv) {

        //var_dump($server);
    }

    public function onOpen(swoole_websocket_server $serv, $req) {
        /*
         * object request:
         * fd
         * header
         * server
         * ...
         */
        //var_dump($req->fd);

        $this->server->task('begin.', -1, function ($serv, $task_id, $data){
            while (TRUE) {
                foreach ($this->server->connections as $fd) {
                    $conn = $this->server->connection_info($fd);
                    if ($conn['websocket_status'] == WEBSOCKET_STATUS_FRAME) {
                        $res = $this->server->push($fd, $this->packMsg('server', 'msg', 0, 'heartbeat'));
                        if (!$res) {
                            echo 'Push failed.';
                        }
                    }
                }
                sleep(3);
            }
            return 'end ';
        });
    }

    public function onMessage(swoole_websocket_server $serv, swoole_websocket_frame $frame) {
        $data = $this->unpackMsg($frame->data, $frame->opcode);
        if ($data) {
            switch ($data['type']) {
                case 'init':
                    $serv->redis->hset('user_fd', array($data['fd'] => $data['uid']));
                    break;
                case 'msg':
                    $serv->redis->lpush('msg', $data['body']);
                    break;
                default:
                    // discard
            }
        }
        echo "有个sb[{$frame->fd}]在说： {$frame->data}\n";
        $serv->push($frame->fd, "Echo: [{$frame->data}]\\n--This is Dapianzi-carl, are you hanging on a tree?\\n");
    }

    private function unpackMsg($data, $op) {
        switch ($op) {
            case WEBSOCKET_OPCODE_TEXT:
                // text message
                $data = json_decode($data);
                if (isset($data['uid']) && isset($data['type']) && isset($data['body'])) {
                    return $data;
                }
                break;
            case WEBSOCKET_OPCODE_BINARY:
                // binary message
                //$this->handleBinary($data);
                break;
            default:
                // do nothing
        };
        return null;
    }

    private function packMsg($uid, $type, $code, $data) {
        return json_encode(array(
            'uid' => $uid,
            'type' => $type,
            'code' => $code,
            'data' => $data,
        ));
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

    public function onTask() {
        return 'haha';
    }

    public function onFinish($serv, $task_id, $data) {
        echo $data."\n";
    }

    public function start() {
        $this->server->on('workerstart', [$this, 'onWorkerStart']);
        $this->server->on('start', [$this, 'onStart']);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('close', [$this, 'onClose']);
        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onFinish']);
        $this->server->start();
    }
}

$serv = new class_swoole_websocket_server();
$serv->start();

?>