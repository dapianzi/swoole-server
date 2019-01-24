<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/22
 * Time: 19:08
 */

class Event
{
    public static $events = [];

    public static function addListener($event, $func) {
        self::$events[$event][] = $func;
    }

    public static function emit() {
        $argv = func_get_args();
        $event = array_shift($argv);
        if (!isset(self::$events[$event])) {
            throw new EventException('Unknow event: '. $event);
        }
        foreach (self::$events[$event] as $e) {

            call_user_func_array($e, $argv);
        }
    }
}

class EventException extends Exception {

}