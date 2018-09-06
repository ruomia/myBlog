<?php
namespace controllers;

use models\Blog;
class BlogController
{
    public function create()
    {
        view('blogs.create');
    }
    public function store()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];

        $blog = new Blog;
        $blog->add($title,$content,$is_show);
        
        message('发表成功！', 2, '/blog/index');
    }

    public function index()
    {
        $blog = new Blog;
        $data = $blog->search();
        // echo "<pre>";
        // var_dump($data);
        view('blogs.index',$data);
    }
    public function del()
    {
        $id = $_POST['id'];
        $blog = new Blog;
        $blog->delete($id);
        message('删除成功', 2, '/blog/index');
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