<?php
// 所有子模型的父模型
namespace models;
use PDO;
class Base
{
    public static $pdo = null;
    public function __construct()
    {
        if(self::$pdo === null)
        {
            $config = config('db');
            extract($config);
            self::$pdo = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);
            self::$pdo->exec('SET NAMES utf8');
        }
        
    }
}