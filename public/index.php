<?php
//定义常量
define('ROOT', dirname(__FILE__) . '/../');

//实现类的自动加载
function autoload($class)
{
    $path = str_replace('\\', '/', $class);
    require_once(ROOT . $path . '.php');
}
spl_autoload_register('autoload');

//添加路由：解析 URL 上的路径：控制器/方法
// 获取 URL上的路径
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
    require_once(ROOT . 'views/' . $path);
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