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
            $_SESSION['money'] = $user['money'];
            return TRUE;
        }
        else
        {
            return FALSE;
        }
        
    }
    public function addMoney($money,$userId)
    {
        $sql = "UPDATE users SET money=money+? WHERE id=?";
        return $this->exec($sql,[
            $money,
            $userId
        ]);
 
    }

   // 获取余额
   public function getMoney()
   {
       $id = $_SESSION['id'];
   
        $sql = "SELECT money FROM users WHERE id = ?";
        $money = $this->getFirstFeild($sql,[$id]);
        // 保存到 Redis
        $_SESSION['money'] = $money;
        return $money;
       
   }


}