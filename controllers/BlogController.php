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
    public function display()
    {
        //接收日志ID
        $id = (int)$_GET['id'];
        $blog = new Blog;
        echo $blog->getDisplay($id);
    }
    /**
     * 把Redis中的数据更新到数据库中..
     */
    public function displayToDb()
    {
        $blog = new Blog;
        $blog->displayToDb();
    }

    public function test()
    {
        $log =  new \libs\Log('new');
        $log->log('贵有恒，何必三更起五更眠');
        echo '发表成功';
    }
}