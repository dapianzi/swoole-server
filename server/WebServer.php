<?php
/**
 * create by Carl.
 * 2018-02-28
 */

/**
 * Class Swoole WebServer
 */
define('SWOOLE_SERVER', TRUE);

define('APPLICATION_PATH', dirname(__FILE__) . '/../');
define('BASE_EXCEPTION', 10000);

class WebServer {
    public static $instance;
    private $app;
    private $server;

    public function __construct()
    {
        $this->server = new Swoole\Http\Server("0.0.0.0", 9501);
        $this->server->set(array(
            'worker_num' => 4,
            'task_worker_num' => 16,
            'daemonize' => FALSE,
            'backlog' => 128,
            'max_request' => 10000,
            //'dispatch_mode' => 1,
            'upload_tmp_dir' => '/data/www/swoole-server/data',
            'document_root' => '/data/www/swoole-server/public',
            'enable_static_handler' => true,
            'reload_async' => true,
        ));
        $this->start();
    }


    static public function getInstance() {
        if (!self::$instance) {
            self::$instance = new WebServer();
        }
        return self::$instance;
    }


    public function start() {
        $this->server->on('request', [$this, 'onRequest']);
        $this->server->on('workerstart', [$this, 'onWorkerStart']);
        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onfinish']);
        $this->server->start();
    }

    public function onWorkerStart($serv, $worknum) {
        var_dump(get_included_files());
        cli_set_process_title('swoole_worker_'.$worknum);
        Yaf_Registry::set('swoole_serv', $serv);
        $this->app = new Yaf_Application( APPLICATION_PATH . "conf/application.ini");
        $this->app->bootstrap();
    }

    public function onTask($serv, $task_id, $worker_id, $data) {
        $data = explode('____', $data, 2);
        $resp = Swoole\Http\Response::create(intval($data[0]));
        // async task works.
        $f = fopen('task.log', 'a+');
        fwrite($f, '['.date('Y-m-d H:i:s').'] open task '. $data[0] ."\n");
        fclose($f);
        $resp->end($data[1]);
    }

    public function onfinish($data) {
        echo "task {$data} finish\n";
    }

    public function onRequest($request, $response) {
//        print_r($request->server);
        $uri = $request->server['request_uri'];
        printf("[%s]get %s\n", date('Y-m-d H:i:s'), $uri);
        if ($uri == '/favicon.ico') {
            $request->status(404);
            $request->end();
        }
        Yaf_Registry::set('swoole_req', $request);
        Yaf_Registry::set('swoole_res', $response);
        ob_start();
        $this->app->getDispatcher()->dispatch(new Yaf_Request_Http($this->rewrite($uri)));
        $data = ob_get_clean();
        $response->detach();
        $this->server->task($response->fd.'____'.$data);
    }


    /**
     * custom dispatcher
     * @param $request
     */
    public function rewrite($uri) {
//        $uri = str_replace('/proxy', '', $uri); // rewrite rule
        return $uri;
    }

}

WebServer::getInstance();

?>