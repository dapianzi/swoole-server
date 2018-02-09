<?php
/**
 *
 * @Author: Carl
 * @Since: 2017-11-28 16:32
 * Created by PhpStorm.
 */
Yaf_Loader::import( "PHPMailer/PHPMailer.php");
Yaf_Loader::import( "PHPMailer/SMTP.php");

class PHPMailer_Adapter {

    //链接qq域名邮箱的服务器地址
    public static $MAIL_HOST = 'smtp.gmail.com';
    public static $MAIL_PORT = 465;
    public static $MAIL_USER = 'oowoolf@gmail.com';
    public static $MAIL_PASS = 'Dapianzi110!';
    public static $FROM_NAME = 'Dapianzi-Carl';
    public static $MAIL_FROM = 'oowoolf@gmail.com';
    public static $NICK_NAME = 'Dapianzi-Carl';

    static public function send($to, $subject, $body) {
        //实例化PHPMailer核心类
        $mail = new PHPMailer();

        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->SMTPDebug = 0;

        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();

        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth=true;

        //设置smtp的helo消息头 这个可有可无 内容任意
        $mail->Helo = 'Hello Server';

        //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
        $mail->Hostname = 'localhost';

        //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $mail->CharSet = 'UTF-8';

        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);

        $mail->MessageDate = date('Y-m-d H:i:s');

        $mail->Host = self::$MAIL_HOST;

        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';

        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        $mail->Port = self::$MAIL_PORT;


        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = self::$FROM_NAME;

        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username = self::$MAIL_USER;

        //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
        $mail->Password = self::$MAIL_PASS;

        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From = self::$MAIL_FROM;

        //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
        $mail->addAddress($to, self::$NICK_NAME);

        //添加多个收件人 则多次调用方法即可
        // $mail->addAddress('xxx@163.com','lsgo在线通知');

        //添加该邮件的主题
        $mail->Subject = $subject;

        //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
        $mail->Body = $body;

        //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
        // $mail->addAttachment('./d.jpg','mm.jpg');
        //同样该方法可以多次调用 上传多个附件
        // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');

        return $mail->send();
    }

    /**
     * todo: test a email address is available.
     */
    static public function test($host, $port, $user, $pass, $from) {
        //实例化PHPMailer核心类
        $mail = new PHPMailer();

        //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->SMTPDebug = 0;

        //使用smtp鉴权方式发送邮件
        $mail->isSMTP();

        //smtp需要鉴权 这个必须是true
        $mail->SMTPAuth=true;

        //设置smtp的helo消息头 这个可有可无 内容任意
        $mail->Helo = 'Hello smtp.exmail.qq.com Server';

        //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
        $mail->Hostname = 'localhost';

        //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
        $mail->CharSet = 'UTF-8';

        //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
        $mail->isHTML(true);

        $mail->MessageDate = date('Y-m-d H:i:s');

        $mail->Host = $host;

        //设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';

        //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
        $mail->Port = $port;


        //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = $from;

        //smtp登录的账号 这里填入字符串格式的qq号即可
        $mail->Username = $user;

        //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
        $mail->Password = $pass;

        //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
        $mail->From = $from;

        return $mail->smtpConnect();
    }
}