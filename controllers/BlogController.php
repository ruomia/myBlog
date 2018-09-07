<?php
namespace controllers;

use models\Blog;
class BlogController
{
    public function edit()
    {
        $id = $_GET['id'];
        $blog = new Blog;
        $data = $blog->find($id);
        view('blogs.edit',[
            'data' => $data
        ]);
    }
    public function update()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $id = $_POST['id'];
        // var_dump($data);die;
        $blog = new Blog;
        $blog->update($title,$content,$is_show,$id);
        // 如果日志是公开的就生成静态页
        if($is_show == 1)
        {
            $blog->makeHtml($id);

        }
        else
        {
            // 如果改为私有，就要将原来的静态页删除掉
            $blog->deleteHtml($id);
        }
        message('修改成功！', 0, '/blog/index');

    }
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
        //如果日志是公开的，就生成静态页面
        if($is_show == 1)
        {
            $blog->makeHtml($id);
        }
        
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

        $blog->deleteHtml($id);
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
        $display =  $blog->getDisplay($id);

        // 返回多个数据时必须要用 JSON
        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : ''
        ]);
    }
    /**
     * 把Redis中的数据更新到数据库中..
     */
    public function displayToDb()
    {
        $blog = new Blog;
        $blog->displayToDb();
    }
    public function content()
    {
        $id = $_GET['id'];
        $blog  = new Blog;
        $data = $blog->find($id);
        view('blogs.content',[
            'blog' => $data,
        ]);
    }
    public function test()
    {
        $log =  new \libs\Log('new');
        $log->log('贵有恒，何必三更起五更眠');
        echo '发表成功';
    }
}