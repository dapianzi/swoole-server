<?php
/**
 * Created by PhpStorm.
 * User: Carl
 * Date: 2018/2/09
 * Time: 15:31
 */


function gf_dump($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function gf_ajax_error($code=0, $err='') {
    return gf_ajax_return(-1, $err, $code);
}

function gf_ajax_success($data='') {
    return gf_ajax_return(0, $data);
}

function gf_ajax_return($status, $content, $code=0) {
    Yaf_Registry::get('swoole_res')->header('Content-Type', 'application/json; charset=utf-8');
    echo (json_encode(array(
        'code' => $code,
        'status' => $status,
        'content' => $content
    )));
}
function gf_shell_color($str, $color='') {
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

function gf_shell_echo($str) {
    echo date('[Y-m-d H:i:s] ') . $str . "\n";
}

/**
 * 安全的获取客户端IP
 * @return string
 */
function gf_get_remote_addr() {
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

function gf_uuid($str='') {
    return md5(uniqid().$str);
}

function gf_valid_inputs($input, $args) {
    if (!is_array($args) || empty($args)) {
        return is_null($input) ? $args : $input;
    }
    // default
    $args = array_merge([
        'filter' => FALSE,
    ], $args);

    if (isset($args['default']) && is_null($input)) {
        $input = $args['default'];
    }
    if (isset($args['null']) && is_null($input)) {
        throw new Exception(sprintf('[%s] can not be null.', $args['null']));
    }
    if (isset($args['empty']) && empty($input)) {
        throw new Exception(sprintf('[%s] can not be empty.', $args['empty']));
    }
    if (isset($args['in']) && !in_array($input, explode(',', $args['in']))) {
        throw new Exception('Invalid Params.');
    }
    if (isset($args['not']) && in_array($input, explode(',', $args['not']))) {
        throw new Exception('Invalid Params.');
    }
    if (isset($args['expr']) && !preg_match($args['expr'], $input)) {
        throw new Exception('Invalid Params.');
    }
    if (isset($args['type'])) {
        switch (strtolower($args['type'])) {
            case 'int':
                $input = is_array($input) ? array_map('intval', $input) : intval($input);
                break;
            case 'array':
                $input = is_array($input) ? $input : [$input];
                break;
            default:
        }
    }
    if (isset($args['filter']) && $args['filter']) {
        $input = $args['filter']($input);
    }
    return $input;
}

function gf_get_query() {
    if (defined('SWOOLE_SERVER')) {
        return Yaf_Registry::get('swoole_req')->get;
    } else {
        return $_GET;
    }
}

function gf_get_post() {
    if (defined('SWOOLE_SERVER')) {
        return Yaf_Registry::get('swoole_req')->post;
    } else {
        return $_POST;
    }
}

function gf_get_files() {
    if (defined('SWOOLE_SERVER')) {
        return Yaf_Registry::get('swoole_req')->files;
    } else {
        return $_FILES;
    }
}

function gf_get_raw() {
    if (defined('SWOOLE_SERVER')) {
        return Yaf_Registry::get('swoole_req')->rawContent();
    } else {
        try {
            $raw_content = file_get_contents('php://input');
        } catch (Exception $e) {
            $raw_content = '';
        }
        return $raw_content;
    }
}

function gf_get_cookie() {
    if (defined('SWOOLE_SERVER')) {
        return Yaf_Registry::get('swoole_req')->cookie;
    } else {
        return $_COOKIE;
    }
}

function gf_get_server() {
    if (defined('SWOOLE_SERVER')) {
        return Yaf_Registry::get('swoole_req')->server;
    } else {
        return $_SERVER;
    }
}

$f = fopen('fn.log', 'a+');
fwrite($f, '['.date('Y-m-d H:i:s').'] read fn.php'."\n");
fclose($f);
