<?php

class httpClient {

    public $serv;

    public function __construct()
    {
        $idx = 0;
        while($idx<5) {
            echo 'new loop begin'."\n";
            for ($i=0; $i<8; $i++) {
                $serv = new Swoole\Http\Client('127.0.0.1', '80');
                $serv->set([
                    'timeout' => 30,
                    'keep_alive' => false,
                ]);
                $serv->get('/index/sleep/', [$this, 'callback']);
//            $serv->close();
            }
            sleep(5);
            $idx++;
        }
    }

    public function callback($cli) {
        echo "==========================\n";
        echo "Length: " . strlen($cli->body) . "\n";
        echo $cli->body."\n";
    }

}

$http_client = new httpClient();
