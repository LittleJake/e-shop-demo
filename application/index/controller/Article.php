<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/26/2019
 * Time: 8:52 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\index\controller;


class Article extends Common
{
    public function indexAction(){
        $this->assign('page_title', '文章列表');
        return $this->fetch();
    }
}