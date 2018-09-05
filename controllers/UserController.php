<?php
namespace controllers;

use models\User;
class UserController
{
    public function login()
    {
        view('users.login');
    }
    public function doLogin()
    {
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $user = new User;
        if($user->login($email, $password))
        {
            message('登陆成功！', 2, '/blog/index');
        }
        else
        {
            message('账号或者密码错误！', 1, '/user/login');
        }
    }
    public function logout()
    {
        $_SESSION = [];
        message('退出成功！', 2, '/');
    }
    public function register()
    {
        view('users.add');
    }
    public function store()
    {
        //1. 接收表单
        $email = $_POST['email'];
        $password = md5($_POST['password']);
      
        // 2. 生成激活码(32位的随机的字符串)
        $code = md5( rand(1,99999) );
        $redis = \libs\Redis::getInstance();

        //序列化
        $value = json_encode([
            'email' =>  $email,
            'password' => $password,
        ]);
        $key = "temp_user:{$code}";
        $redis->setex($key, 300, $value);

        // 从邮箱地址中取出姓名
        $name = explode('@', $email);
        //构造收件人地址【 blc0927@163.com, blc0927 】
        $from = [$email, $name[0]];
        // var_dump($name);

        //构造消息数组
        $message = [
            'title' => '账号激活',
            'content' => "点击以下链接进行激活：<br> 
            <a href='http://localhost:9000/user/active_user?code={$code}'>http://localhost:9000/user/active_user?code={$code}</a>
            <p>如果无法跳转，请复制链接用浏览器进行访问。。</p> ",
            'from' => $from,
        ];
        //把消息转成字符串（JSON ==> 序列化）
        $message = json_encode($message);
        //放到队列中
        $redis = \libs\Redis::getInstance();
        $redis->lpush('email', $message);
        echo 'ok';

    }
    public function active_user()
    {
        // 1.  接收激活码
        $code = $_GET['code'];
        // 2. 到 Redis取出账号
        $redis = \libs\Redis::getInstance();
        // 拼出名字
        $key = 'temp_user:'.$code;
        //取出数据
        $data = $redis->get($key);
        if($data)
        {
            // 从redis 中删除激活码
            $redis->del($key);
            //反序列化
            $data = json_decode($data, true);
            //插入到数据库中
            $user = new \models\User;
            $user->add($data['email'], $data['password']);
            
            echo '账号激活成功！请登录。。。';

        }
        else
        {
            die('激活码无效！');
        }
        
    }
}