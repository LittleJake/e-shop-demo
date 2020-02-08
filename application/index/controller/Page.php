<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/2/8
 * Time: 23:36
 */

namespace app\index\controller;


use think\exception\RouteNotFoundException;

class Page extends Common
{
    public function pageAction($route = ''){
        $page = model('Page');

        $query = $page
            ->whereOr('route', $route)
            ->whereOr('id', $route)->find();

        if(empty($query))
            throw new RouteNotFoundException();

        $this->assign('page_title', $query['title']);
        $this->assign('page', $query);
        return $this->fetch();
    }
}