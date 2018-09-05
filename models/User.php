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
    public function login($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email=? AND password=?";
        $data = [$email,$password];
        $user = $this->findRow($sql, $data);
        // var_dump($user);die;
        if($user)
        {
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            return TRUE;
        }
        else
        {
            return FALSE;
        }
        
    }

}