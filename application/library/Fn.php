<?php
/**
 * Created by PhpStorm.
 * User: Carl
 * Date: 2018/2/09
 * Time: 15:31
 */


class Fn {

    public static function dump($var) {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }

    public static function ajaxError($err='') {
        return self::ajaxReturn(-1, $err);
    }

    public static function ajaxSuccess($data='') {
        return self::ajaxReturn(0, $data);
    }

    public static function ajaxReturn($status, $content) {
        header('Content-Type:application/json; charset=utf-8');
        return exit(json_encode(array(
            'status' => $status,
            'content' => $content
        )));
    }
    public static function shellColor($str, $color='') {
        switch(strtolower($color)) {
            case 'red': {

                break;
            }
            case 'green': {

                break;
            }
            case 'blue': {

                break;
            }
            default:
                return $str;
        }
    }

    public static function shellEcho($str) {
        echo date('[Y-m-d H:i:s] ') . $str . "\n";
    }

    /**
     * 安全的获取客户端IP
     * @return string
     */
    public static function getIp() {
        if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if(!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        } else {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }

}
