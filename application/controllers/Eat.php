<?php

/**
 *
 * @Author: Carl
 * @Since: 2018-02-09 15:17
 * Created by PhpStorm.
 */

class EatController extends BaseController {

    protected $auth = FALSE;

    public $default_owner = ['小健健','小斌斌','小敏敏','小文文','小昆昆','小楠楠','小灰灰'];
    public $owner;
    public $uuid;

    public function init() {
        if (!isset($_COOKIE['uuid'])) {
            $this->uuid = md5(uniqid(time()));
            setcookie('uuid', $this->uuid, time()+365*86400);
        } else {
            $this->uuid = $_COOKIE['uuid'];
        }
        $this->owner = (new DbModel('eat_owner'))->getRow('SELECT id,name FROM eat_owner WHERE ua_hash=?', [$this->uuid]);
    }

    public function indexAction() {
        $restaurants = (new DbModel('eat_restaurant'))->getKeyValue('SELECT id,name FROM eat_restaurant');
        $this->getView()->assign('restaurant', $restaurants);
        // todoy's choices
        $choice = (new DbModel('eat_choice'))->getAll('SELECT b.id,b.name,a.restaurant FROM eat_choice a LEFT JOIN eat_owner b ON a.owner=b.id WHERE day=?', [date('Y-m-d')]);
        $choices = [];
        $isChoice = 'false';
        foreach ($choice as $r) {
            if ($this->owner && $r['id'] == $this->owner['id']) {
                $isChoice = 'true';
            }
            if (isset($choices[$r['restaurant']])) {
                $choices[$r['restaurant']] ++;
            } else {
                $choices[$r['restaurant']] = 1;
            }
        }
        arsort($choices);
        $this->getView()->assign('choices', $choices);
        $this->getView()->assign('owner', $this->owner);
        $this->getView()->assign('default_owner', $this->default_owner);
        $this->getView()->assign('isChoice', $isChoice);
        $this->getView()->assign('af', date('H')>=14 ? '晚上':'中午');
        $tips_arr = [
            '给亲戚看见我一个人食吉野家',
            '有女朋友别忘了请我吃饭',
            '我在人民广场吃炸鸡',
            '回到中学的暑假',
            '去动物园散步才是正经事',
            '寂寞的星期五',
            '明天不要赖床',
            '易燃易爆炸',
        ];
        $tips = $tips_arr[rand(0, count($tips_arr)-1)];
        $this->getView()->assign('tips', $tips);
    }

    public function ownerAction() {
        $owner = $this->getPost('owner');
        $model = new DbModel('eat_owner');
        if ($this->owner) {
            Fn::ajaxSuccess('已经设置过名字啦');
        }
        if ($owner == '-1') {
            $owner = $this->default_owner[rand(0, count($this->default_owner)-1)];
        }
        $id = $model->insert('eat_owner', [
            'name' => $owner,
            'ua_hash' => $this->uuid
        ]);
        Fn::ajaxSuccess(['id'=>$id,'name'=>$owner]);
    }

    public function choiceAction() {
        $choice = $this->getPost('choice');
        $owner = $this->owner['id'];
        $model = new DbModel('eat_choice');
        $id = $model->insert('eat_choice', [
            'day' => date('Y-m-d'),
            'owner' => $owner,
            'restaurant' => $choice
        ]);
        Fn::ajaxSuccess(['id'=>$owner, 'name' => $this->owner['name'], 'restaurant' => $choice]);
    }

}