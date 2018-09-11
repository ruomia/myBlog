<?php
namespace models;
use PDO;
class Order extends Base
{
    public function create($money)
    {
        $flake = new \libs\Snowflake(1023);
        $sql = "INSERT INTO orders (user_id, money, sn) VALUES(?, ?, ?)";
        $this->exec($sql,[
            $_SESSION['id'],
            $money,
            $flake->nextId()
        ]);
    }
    public function search()
    {
        // 取出当前用户的日志
        $where = 'user_id='.$_SESSION['id'];

        /***************排序 */
        //默认排序
        $odby = 'created_at';
        $odway = 'desc';
        
        
        /***************翻页 */
        $perpage = 15; //每页15
        // 接收当前页码（大于等于1的整数），max：参数中最大的值
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;    
        //计算开始的下标
        $offset = ($page-1)*$perpage;
        //取出总的记录数
        $sql = "SELECT COUNT(*) FROM orders WHERE $where";
        $count = $this->getFirstFeild($sql,$value);
        //计算总的页数 
        $pageCount = ceil( $count / $perpage );
        $btns = '';
        for($i=1;$i<=$pageCount;$i++)
        {
            //先获取之前的参数
            $params = getUrlParams(['page']);
            $class = $page==$i ? 'active' : '';
            $btns .= "<a class='$class' href='?{$params}page=$i'> $i </a>";

        }
       
       
        /***************执行SQL */
        $sql = "SELECT * FROM orders WHERE $where ORDER BY $odby $odway LIMIT $offset,$perpage";
        $data = $this->findAll($sql,$value);

        return [
            'btns'=>$btns,
            'data'=>$data
        ];
    }
    public function findBySn($sn)
    {
        $sql = "SELECT * FROM orders WHERE sn=?";
        return $this->findRow($sql,[$sn]);
    }
    public function setPaid($sn)
    {
        $sql = "UPDATE orders SET status=1,pay_time=now() WHERE sn=?";
        return $this->exec($sql,[$sn]);
    }
}