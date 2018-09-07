<?php
// 使用 redis 保存 SESSION
ini_set('session.save_handler', 'redis');
// 设置 redis 服务器的地址、端
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=3');

session_start();

// 如果用户以 POST 方式访问网站时，需要验证令牌
// if($_SERVER['REQUEST_METHOD'] == 'POST')
// {
//     if(!isset($_POST['_token']))
//     {
//         die('违法操作！');
//     }
//     if($_POST['_token'] != $_SESSION['token'])
//     {
//         die('违法操作！');
//     }
// }
// var_dump($_SESSION['token']);
//定义常量
define('ROOT', dirname(__FILE__) . '/../');

//引入 composer 自动加载文件
require(ROOT . 'vendor/autoload.php');
//实现类的自动加载
function autoload($class)
{
    $path = str_replace('\\', '/', $class);
    require(ROOT . $path . '.php');
}
spl_autoload_register('autoload');

//添加路由：解析 URL 上的路径：控制器/方法
// 获取 URL上的路径

if(php_sapi_name() == 'cli')
{
    $controller = ucfirst($argv[1]) . 'Controller';
    $action = $argv[2];
}
else
{
    if( isset($_SERVER['PATH_INFO']) )
    {
        $pathInfo = $_SERVER['PATH_INFO'];
        // 根据 / 转成数组
        $pathInfo = explode('/', $pathInfo);

        // 得到控制器名和方法：
        $controller = ucfirst($pathInfo[1]) . 'Controller';
        $action = $pathInfo[2];
    }
    else
    {
        $controller = 'IndexController';
        $action = 'index';
    }
}
//为控制添加命名空间
$fullController = 'controllers\\'.$controller;

$_C = new $fullController;
$_C->$action();

//加载视图
// 参数一、加载的视图的文件名
// 参数二、向视图中传的数据
function view($viewFileName, $data = [])
{
    //解压数组成变量
    extract($data);

    $path = str_replace('.', '/', $viewFileName) . '.html';

    //加载视图
    require(ROOT . 'views/' . $path);
}

// 获取当前URL 上所有的参数，并且还能排除掉某些参数
// 参数：要排除的变量
function getUrlParams($except = [])
{
    foreach($except as $v)
    {
        unset($_GET[$v]);
        //unset($_GET['odby])
        //unset($_GET[odway])
    }
    //拼出：keyword=abc&is_show=1
    $str = '';
    foreach($_GET as $k => $v)
    {
        $str .= "$k=$v&";
    }

    return $str;
}
// 加载配置文件
function config($name)
{
    //静态局部变量
    static $config = null;
    if($config === null)
    {
        $config = require(ROOT . 'config.php');
    }
    
    return $config[$name];
}

function redirect($url)
{
    header("Location:".$url);
    exit;
}
//跳回上一个页面
function back()
{
    redirect( $_SERVER['HTTP_REFERER']);
}

function message($message, $type, $url, $seconds = 5)
{
    if($type == 0)
    {
        echo "<script>alert('{$message}');location.href='{$url}'</script>";
    }
    else if($type == 1)
    {
        view('common.success',[
            'message' => $message,
            'url' => $url,
            'seconds' => $seconds
        ]);
    }
    else if($type == 2)
    {
        // 把消息保存到 SESSION
        $_SESSION['_MESS_'] = $message;

        // 跳转到下一个页面
        redirect($url);
    }
}
// 过滤 XSS （在线编辑器填写的内容不能使用该函数过滤）
function e($content)
{
    return htmlspecialchars($content);
}
// 使用 htmlpurifer 过滤 （因为性能慢，这个函数只能
// 使用在线编辑器填写的内容的字段上，其他字段使用上面的e函数过滤）
function hpe($content)
{
    // 一直保存在内存中（直到脚本执行结束）
    static $purifier = null;
    // 只有第一次调用时才会创建新的对象
    if($purifier === null)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Core.Encoding', 'utf-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('Cache.SerializerPath', ROOT.'storage/cache');
        $config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,ol[start],li,p[style],br,span[style],img[width|height|alt|src],*[style|class],pre,hr,code,h2,h3,h4,h5,h6,blockquote,del,table,thead,tbody,tr,th,td');
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,margin,width,height,font-family,text-decoration,padding-left,color,background-color,text-align');
        $config->set('AutoFormat.AutoParagraph', TRUE);
        $config->set('AutoFormat.RemoveEmpty', TRUE);
        $purifier = new \HTMLPurifier($config);
    }
    return $purifier->purify($content);
}
function csrf()
{
    if(!isset($_SESSION['token']))
    {
        // 生成一个随机字符串
        $token = md5( rand(1,99999). microtime() );
        $_SESSION['token'] = $token;
    }
    return $_SESSION['token'];
}
//生成令牌隐藏域
function csrf_field()
{
    $csrf = isset($_SESSION['token']) ? $_SESSION['token'] : csrf();
    echo "<input type='hidden' name='_token' value='{$csrf}' />";
}