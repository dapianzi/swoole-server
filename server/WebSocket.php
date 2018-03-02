<?php
/**
 * create by Carl.
 * 2018-02-28
 */

/*
 * message structure
 */
//{
//    "type": CHAT_INIT,
//    "token": "",
//    "time": "",
//    "msg_type": "",
//    "send_user": "",
//    "chat_id": "",
//    "content": "",
//    }
//
//}


define('CHAT_INIT',             1);
define('CHAT_CREATE',           2);
define('CHAT_ADDUSER',          4);
define('CHAT_HISTORY',          3);
define('CHAT_MESSAGE',          5);
define('CHAT_CLOSE',            6);

/**
 * Class class_swoole_websocket_server
 */
class class_swoole_websocket_server {
    private $server;
    private $app;
    public static $instance;

    public function __construct()
    {
        $this->app = new Yaf_Application('../conf/application.ini');
        $this->server = new swoole_websocket_server('0.0.0.0', 1988);
        // set run-time params
        $this->server->set(array(
            'worker_num' => 2,
            'task_worker_num' => 4,
            'daemonize' => FALSE,
            'backlog' => 128,
            'max_request' => 10,
            //'dispatch_mode' => 1,
        ));
        $this->start();
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

        var_dump($this->app->execute('index'));
    }

    public function onOpen(swoole_websocket_server $serv, $req) {
        /*
         * object request:
         * fd
         * header
         * server
         * ...
         */
        //var_dump($serv->redis);

//        $this->server->task('begin.', -1, function ($serv, $task_id, $data){
//            while (TRUE) {
//                foreach ($this->server->connections as $fd) {
//                    $conn = $this->server->connection_info($fd);
//                    if ($conn['websocket_status'] == WEBSOCKET_STATUS_FRAME) {
//                        $res = $this->server->push($fd, $this->packMsg('server', 'msg', 0, 'heartbeat'));
//                        if (!$res) {
//                            echo 'Push failed.';
//                        }
//                    }
//                }
//                sleep(3);
//            }
//            return 'end ';
//        });
    }

    public function onMessage(swoole_websocket_server $serv, swoole_websocket_frame $frame) {
        $data = $this->unpackMsg($frame->data, $frame->opcode);
        if ($data) {
            $serv->task($data);
        }
        echo "有个sb[{$frame->fd}]在说： {$frame->data}\n";
    }

    private function unpackMsg($data, $op) {
        switch ($op) {
            case WEBSOCKET_OPCODE_TEXT:
                // text message
                $data = json_decode($data, TRUE);
//                if (isset($data['sender']) && isset($data['type']) && isset($data['content'])) {
//                    return $data;
//                }
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
            'sender' => $uid,
            'type' => $type,
            'code' => $code,
            'content' => $data,
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

    public function onTask(swoole_websocket_server $serv, $worder_id, $fd, $data) {
        $user_id = $data['user_id'];
        $token = $data['token'];
        if ($data['type'] == CHAT_INIT) {

        } else {
            // check token
            if (!$serv->redis->hGet('user_fd', $data['send_user'])) {
                $user_fd = json_decode($user_fd, TRUE);
                $serv->close($user_fd['fd']);
            }
        }
        switch ($data['type']) {
            // handshake
            case CHAT_INIT:
                if ($this->db->query('SELECT id FROM session WHERE user_id=? AND token=?', array($user_id, $token))) {
                    // fd already existaasf
                    if ($user_fd = $serv->redis->hGet('user_fd', $data['send_user'])) {
                        $user_fd = json_decode($user_fd, TRUE);
                        $serv->close($user_fd['fd']);
                    }
                    $serv->redis->hSet('user_fd', $data['send_user'], json_encode(array('fd'=>$fd, 'token'=> $data['token'])));
                }
                break;
            case CHAT_CREATE:

                break;
            case CHAT_ADDUSER:

                break;
            // history message
            case CHAT_HISTORY:

                break;
            case CHAT_MESSAGE:
                // try to send message if user is online
                $chat_id = $data['chat_id'];
                $chat_type = $data['chat_type'];
                $users = $serv->redis->hGet('chat_group:'.$chat_type, $chat_id);
                break;
            default:
                // discard
        }

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

    static public function getInstance() {
        if (!self::$instance) {
            self::$instance = new class_swoole_websocket_server;
        }
        return self::$instance;
    }
}

class_swoole_websocket_server::getInstance();

?>