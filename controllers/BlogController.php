<?php
namespace controllers;

use models\Blog;
class BlogController
{
    public function index()
    {
        $blog = new Blog;
        $data = $blog->search();
        // echo "<pre>";
        // var_dump($data);
        view('blogs.index',$data);
    }

    public function content_to_html(){
        $blog = new Blog;
        $blog->contentHtml();
    }
    public function index_to_html(){
        $blog = new Blog;
        $blog->indexHtml();

    }
    public function update_display()
    {
        //接收日志ID
        $id = (int)$_GET['id'];

        //连接Redis
        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
        ]);

        //判断blog_display 这个hash中有没有一个键是 blog-$id
        $key = "blog-{$id}";
        //判断 hash中是否有这个值
        if($redis->hexists('blog_displays', $key))
        {
            //累加 并且 返回添加完之后的值
            $newNum = $redis->hincrby('blog_displays', $key, 1);
            echo $newNum;
        }
        else
        {
            //从数据库中取出浏览量
            $blog = new Blog;
            $display = $blog->getDisplay($id);
            $display++;
            //加到redis
            $redis->hset('blog_displays', $key, $display);
            echo $display;
        }
    }
}