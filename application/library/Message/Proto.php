<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/22
 * Time: 21:27
 */

namespace Message;

define('Request_Login', 100001);
define('Response_Login', 100002);
define('Request_GetList', 100003);
define('Response_GetList', 100004);

class Proto
{

    public static $MSG_PROTO = [
        Request_Login => ['RequestLogin', Response_Login, 'ResponseLogin'],
        Request_GetList => ['RequestGetList', Response_GetList, 'ResponseGetList'],
    ];

    public static function GetRequestMessageProto ($msg_id) {
        if (isset(self::$MSG_PROTO[$msg_id])) {
            return '\\Message\\Dapianzi\\'.self::$MSG_PROTO[$msg_id][0];
        } else {
            return FALSE;
        }
    }
    public static function GetResponseMessageProto ($msg_id) {
        if (isset(self::$MSG_PROTO[$msg_id])) {
            return [self::$MSG_PROTO[$msg_id][1], '\\Message\\Dapianzi\\'.self::$MSG_PROTO[$msg_id][2]];
        } else {
            return FALSE;
        }
    }
}