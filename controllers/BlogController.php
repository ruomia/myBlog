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

}