<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/8/2019
 * Time: 8:45 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\index\controller;


class Category extends Common
{
    public function indexAction(){
        $this->assign('page_title', "分类");
        return $this->fetch();
    }
}