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

    public function exec($sql,$data=[])
    {
        // var_dump($sql);
        $stmt = self::$pdo->prepare($sql);
        //通过execute 绑定数据
        // var_dump();
        return $stmt->execute($data);

    }
    public function findAll($sql,$data=[])
    {
        $stmt = self::$pdo->prepare($sql);
        $arr = array();
        if(!$stmt->execute($data) === FALSE)
        {
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $arr;
    }
    public function findRow($sql,$data=[])
    {
        $stmt = self::$pdo->prepare($sql);
        $arr = array();
        if(!$stmt->execute($data) === FALSE)
        {
            $arr = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $arr;
    }
    public function getFirstFeild($sql,$data=[])
    {
        $stmt = self::$pdo->prepare($sql);
        $arr = array();
        if($stmt->execute($data))
        {
            $arr = $stmt->fetch(PDO::FETCH_NUM);
        }
        return $arr[0];
    }
}