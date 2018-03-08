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

//init config
include_once "../application/library/DbClass.php";
$conf = parse_ini_file('../conf/application.ini');

/**
 * Class class_swoole_websocket_server
 */
class class_swoole_websocket_server {
    private $server;
    private $db;
    public static $instance;

    public function __construct($conf)
    {
        $this->conf = $conf;
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

        // init redis
        $redis = new Redis;
        $redis->connect('127.0.0.1', 6379);
        $serv->redis = $redis;
        // init db
        $serv->db = array(
            'user' => new DbClass($this->conf['user.dsn'], $this->conf['user.username'], $this->conf['user.password']),
            'app' => new DbClass($this->conf['app.dsn'], $this->conf['app.username'], $this->conf['app.password']),
        );
        //var_dump($serv);
    }

    public function onStart(swoole_websocket_server $serv) {
        //print_r($this->db->getAll("SHOW TABLES"));
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
        print_r($data);
        // 聊天主逻辑

        if ($data['type'] == CHAT_INIT) {
            // check token
            $data['action'] = 'chat_init';
            //$serv->task($data);
            $sql = "SELECT id FROM session WHERE user_id=? AND token=? AND expire_time>unix_timestamp()";
            $session = $serv->db['user']->getRow($sql, array($data['user_id'], $data['token']));
            if ($session) {
                $serv->redis->hSet('user_fd', $data['user_id'], $frame->fd);
            } else {
                $serv->push($frame->fd, $this->packMsg($data['user_id'], CHAT_CLOSE, 201, 'Invalid token'));
                $serv->close($frame->fd);
                return FALSE;
            }
        } else {
            // check token
            $user_fd = $serv->redis->hGet('user_fd', $data['user_id']);
            if (!$user_fd) {
                $serv->push($frame->fd, $this->packMsg($data['user_id'], CHAT_CLOSE, 201, 'Invalid user'));
                $serv->close($user_fd['fd']);
                return FALSE;
            }
            switch ($data['type']) {

                case CHAT_CREATE:
                    $user_id = $data['user_id'];
                    $this->server->task(array(
                        'fd' => $frame->fd,
                        'action' => 'create_chat',
                        'user_id' => $user_id,
                        'users' => array($user_id, $data['chat_user']),
                    ));
                    $this->server->push($frame->fd, $this->packMsg($user_id, CHAT_MESSAGE, 300, 'ok'));
                    break;
                case CHAT_ADDUSER:
                    // 加入群聊
                    $chat_id = $data['chat_id'];
                    $user_append = $data['user_append'];
                    // update redis chat group
                    $group_user = json_decode($serv->redis->hGet('chat_group', $chat_id), TRUE);
                    array_push($group_user['users'], $user_append);
                    $serv->redis->hSet('chat_group', $chat_id, json_encode($group_user));
                    // update mysql db
                    $serv->task(array(
                        'action' => 'user_append',
                        'user_id' => $data['user_id'],
                        'append_user' => $user_append,
                        'chat_id' => $chat_id,
                    ));
                    break;
                // history message
                case CHAT_HISTORY:
                    $user_id = $data['user_id'];
                    $chat_id = $data['chat_id'];
                    $messages = $serv->db['app']->query("SELECT * FROM message WHERE send_status=0 AND user_id=? AND chat_id=? ", array($user_id, $chat_id));
                    $this->server->push($frame->fd, $this->packMsg($user_id, CHAT_HISTORY, 300, $messages));
                    break;
                case CHAT_MESSAGE:
                    // try to send message if user is online
                    $chat_id = $data['chat_id'];
                    $chat_info = json_decode($serv->redis->hGet('chat_group', $chat_id), TRUE);
                    $messages = array();
                    foreach ($chat_info['users'] as $user) {
                        if ($user != $data['user_id']) {
                            $fd = $serv->redis->hGet('user_fd', $user);
                            $msg_data = array(
                                'user_id' => $data['user_id'],
                                'receive' => $user,
                                'chat_id' => $data['chat_id'],
                                'type' => CHAT_MESSAGE,
                                'content' => $data['content'],
                                'content_type' => $data['content_type'],
                            );
                            if ($fd) {
                                try {
                                    $serv->push($fd, json_encode($msg_data));
                                    $msg_data['status'] = 1;
                                } catch (Exception $e) {
                                    $msg_data['status'] = 0;
                                }
                            } else {
                                $msg_data['status'] = 0;
                            }
                            array_push($messages, $msg_data);
                        }
                    }
                    $serv->task(array(
                        'action' => 'save_message',
                        'user_id' => $data['user_id'],
                        'messages' => $messages,
                    ));
                    break;
                default:
                    // discard
            }
        }
        print_r($serv->redis->hGetAll('user_fd'));
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
        return $data;
    }

    private function packMsg($uid, $type, $code, $data) {
        return json_encode(array(
            'user_id' => $uid,
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

    /*
     * 异步的db操作
     */
    public function onTask(swoole_websocket_server $serv, $task_id, $from_id, $data) {
        $user_id = $data['user_id'];
        $result = 'ok';
        switch ($data['action']) {
            case 'add_user':

                break;
            case 'create_chat':
                $chat_id = $serv->db['app']->getColumn("SELECT id FROM chat_group WHERE create_user_id=? AND users=?", 0, array(
                    $user_id,
                    implode(',', $data['users']),
                ));
                if (!$chat_id) {
                    $chat_id = $serv->db['app']->insert('chat_group', array(
                        'type' => 1,
                        'topic' => '',
                        'create_user_id' => $user_id,
                        'users' => implode(',', $data['users']),
                    ));
                    foreach ($data['users'] as $u) {
                        $serv->db['app']->insert('chat_group_user', array('group_id'=>$chat_id, 'user_id'=>$u));
                    }
                }
                $serv->redis->hSet('chat_group', $chat_id, json_encode(array(
                    'type' => 1,
                    'users' => $data['users'],
                )));
                $serv->push($data['fd'], $this->packMsg($user_id, CHAT_CREATE, 300, $chat_id));
                break;
            case 'user_append':
                $serv->db['app']->insert('chat_group_user', array('group_id'=>$data['chat_id'], 'user_id'=>$data['user_append']));
                break;
            case 'save_message':
                $result = [];
                foreach ($data['messages'] as $msg_data) {
                    $msg = array(
                        'user_id' => $msg_data['user_id'],
                        'group_id' => $msg_data['chat_id'],
                        'status' => $msg_data['status'],
                        'send_user' => $msg_data['user_id'],
                        'send_timestamp' => microtime(TRUE),
                        'msg_content' => json_encode(array(
                            'content' => $msg_data['content'],
                            'content_type' => $msg_data['content_type'],
                        )),
                    );
                    $result[] = $serv->db['app']->insert('chat_messages', $msg);
                }
                break;
        }

        return array(
            'action' => $data['action'],
            'result' => $result,
        );
    }

    public function onFinish($serv, $task_id, $data) {
        print_r($data);
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

    static public function getInstance($conf) {
        if (!self::$instance) {
            self::$instance = new class_swoole_websocket_server($conf);
        }
        return self::$instance;
    }
}

class_swoole_websocket_server::getInstance($conf);

?>