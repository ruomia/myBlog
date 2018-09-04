<?php
namespace models;

class User extends Base
{

    public function add($email,$password)
    {
        $sql = "INSERT INTO users (email,password) VALUES(?,?)";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([$email,$password]);
    }

}