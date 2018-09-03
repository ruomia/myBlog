<?php
namespace models;

use PDO;

class User
{

    public $pdo;
    public function __construct()
    {
        $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=blog', 'root', '123456');
        $this->pdo->exec('SET NAMES utf8');
    }
    public function hello()
    {
        return 'hello';
    }
    public function add($email,$password)
    {
        $sql = "INSERT INTO users (email,password) VALUES(?,?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$email,$password]);
    }

}