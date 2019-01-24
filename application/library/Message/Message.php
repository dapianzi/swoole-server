<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/22
 * Time: 19:08
 */
namespace Message;

use Yaf\Exception;

class Message
{
    public $version = 1;
    public $msg_id;
    public $msg_obj;
    public $bufString;
    public $err;

    public function __construct($msg_id=0, $msg_obj=null) {
        $this->msg_id = $msg_id;
        $this->msg_obj = $msg_obj;
    }

    public function pack($req_id, $code, $msg_json) {
        list($msg_id, $protoClass) = Proto::GetResponseMessageProto($req_id);
        if (!$protoClass) {
            $this->err = 'No msg id matched.';
            return FALSE;
        }
        try {
            $msg_obj = new $protoClass();
            $msg_obj->mergeFromJsonString($msg_json);
            $body = $msg_obj->serializeToString();
            $this->bufString = pack('N3c*', $this->version, $msg_id, strlen($body)). $body;
            return TRUE;
        } catch (\Exception $e){
            $this->err = $e->getMessage();
            return FALSE;
        }
    }

    public function unpack($msg) {
        $data = unpack('Nversion/Nmsgid/Nlen', $msg);
        $version = $data['version'];
        $msg_id = $data['msgid'];
        $len = $data['len'];
        $body = unpack('a*', $msg, 12);
        $protoClass = Proto::GetRequestMessageProto($msg_id);
        if (!$protoClass) {
            $this->err = 'No msg id matched.';
            return FALSE;
            // handle error.
        }
        try {
            $msg_obj = new $protoClass();
            $msg_obj->mergeFromString(pack('a*', $body[1]));
            print_r($msg_obj->serializeToJsonString());
        } catch (\Exception $e) {
            $this->err = $e->getMessage();
            return FALSE;
            // handle invalid msg
//            throw new MessageParseException('Invalid message');
        }
        $this->msg_obj = $msg_obj->serializeToJsonString();
        $this->msg_id = $msg_id;
        return TRUE;
    }

}

class MessageParseException extends \Exception
{

}