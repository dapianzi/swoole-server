<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 15:17
 * Created by PhpStorm.
 */

class IndexController extends BaseController {

    protected $auth = FALSE;

    public function indexAction() {
        $this->getView()->display('games/pinball.html');
        return FALSE;
    }

    public function protobufAction() {

    }

    public function postAction() {
        $aa = $this->getQuery('aa', '');


        $res = [
            'swoole_version' => SWOOLE_VERSION,
            'bb' => $this->getPost('bb', ''),
            'files' => gf_get_files(),
        ];
        if ($aa == 'ajax') {
            $in = [];
            $begin = 1025;
            $now = time();
            $i = 0;
            while ($begin <= 1314) {
//                $begin += $this->fib($i);
                $in[date('Y-m-d', $now)] = $begin;
                $begin += $this->add($i);
//                $begin += $this->mul($i);
                $i++;
                $now += 86400;
            }
            $res['line'] = $in;
            gf_ajax_success($res);
            return FALSE;
        } else if ($aa == 'post') {
            $this->getView()->assign('result', $res);
        }
        $this->getView()->assign('result', $res);
    }

    private function fib($i) {
        if ($i==0) {return 1;}
        else if ($i==1) {return 1;}
        else {return $this->fib($i-1) + $this->fib($i-2);}
    }

    public function add($i) {
        return $i*2+1;
    }

    private function mul($i) {
        return 2<<$i;
    }



    public function testAction() {
        $o = $this;
        $uid = $this->getQuery('uid', 0);
        Event::emit('test', $uid, $o);
        echo '==============================<br />Event end'.'<br/>';
        return FALSE;
    }
}