<?php
namespace models;

class Blog extends Base
{

    public function search()
    {

        $where = 1;
        //放预处理对应的值
        $value = [];
        if(isset($_GET['keyword']) && $_GET['keyword']){
            $where .= " AND (title LIKE ? OR content LIKE  ?) ";
            $value[] = '%'.$_GET['keyword'].'%';
            $value[] = '%'.$_GET['keyword'].'%';
        }
        if(isset($_GET['start_date']) && $_GET['start_date'])
        {
            $where .= " AND created_at >= ? ";
            $value[] = $_GET['start_date'];
        }
        if(isset($_GET['end_date']) && $_GET['end_date']){
            $where .= " AND created_at <= ? ";
            $value[] = $_GET['end_date'];
        }
        if(isset($_GET['is_show']) && ($_GET['is_show']==1 || $_GET['is_show']==='0'))
        {
            $where .= " AND is_show = ? ";
            $value[] = $_GET['is_show'];
        }
        // var_dump($value);die;

        /***************排序 */
        //默认排序
        $odby = 'created_at';
        $odway = 'desc';
        
        if(isset($_GET['odby']) && $_GET['odby'] == 'display')
        {
            $odby = 'display';
        }

        if(isset($_GET['odway']) && $_GET['odway'] == 'asc')
        {
            $odway = 'asc';
        }
        
        /***************翻页 */
        $perpage = 15; //每页15
        // 接收当前页码（大于等于1的整数），max：参数中最大的值
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;    
        //计算开始的下标
        $offset = ($page-1)*$perpage;
        //取出总的记录数
        $sql = "SELECT COUNT(*) FROM blogs WHERE $where";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($value);
        $count = $stmt->fetch( PDO::FETCH_COLUMN);
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
        $sql = "SELECT * FROM blogs WHERE $where ORDER BY $odby $odway LIMIT $offset,$perpage";
        // echo $sql;die;
        //预处理sql
        $stmt = self::$pdo->prepare($sql);
        // 执行sql
        $stmt->execute($value);
        // 取数据
        // $data = $stmt->errorInfo();
        // var_dump($data);die;
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'btns'=>$btns,
            'data'=>$data
        ];
    }
    public function contentHtml()
    {
        $stmt = self::$pdo->query('SELECT * FROM blogs');
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 开启缓冲区
        ob_start();

        // 生成静态页
        foreach($blogs as $v)
        {
            // 加载视图
            view('blogs.content', [
                'blog' => $v,
            ]);
            // 取出缓冲区的内容
            $str = ob_get_contents();
            // 生成静态页
            file_put_contents(ROOT . 'public/contents/'.$v['id'].'.html', $str);
            // 清空缓冲区
            ob_clean();
        }

    }

    public function indexHtml()
    {
        $sql = "SELECT * FROM blogs WHERE is_show=1 ORDER BY id desc LIMIT 20";
        $stmt = self::$pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();

        view('blogs.main',[
            'blog' => $data
        ]);
        $str = ob_get_contents();
        file_put_contents(ROOT . 'public/index.html', $str);
        ob_clean();
    }

    //从数据库中取出日志的浏览量
    public function getDisplay($id)
    {

        
        //连接Redis
        $redis = \libs\Reids::getInstance();

        //判断blog_display 这个hash中有没有一个键是 blog-$id
        $key = "blog-{$id}";
        //判断 hash中是否有这个值
        if($redis->hexists('blog_displays', $key))
        {
            //累加 并且 返回添加完之后的值
            $newNum = $redis->hincrby('blog_displays', $key, 1);
            return $newNum;
        }
        else
        {
            //从数据库中取出浏览量
            $sql = "SELECT display FROM blogs WHERE id=?";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([$id]);
            $display = $stmt->fetch( PDO::FETCH_COLUMN );
            $display++;
            //加到redis
            $redis->hset('blog_displays', $key, $display);
            return $display;
        }
        
    }
    
    public function displayToDb()
    {
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
        ]);
        //从redis中取出blog_displays 的所有数据
        $data = $redis->hgetall('blog_displays');
        // echo '<pre>';
        // var_dump($data);
        foreach($data as $k=>$v)
        {
            $id = substr($k, 5);
            // var_dump($id,$display);
            // echo '<br>';
            $sql = "UPDATE blogs set display= {$v} WHERE id = {$id}";
            self::$pdo->exec($sql);
        }
    }
}