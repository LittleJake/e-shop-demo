<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/9
 * Time: 13:43
 */

namespace app\admin\controller;


class Category extends Base
{
    /** 商品分类操作 */
    public function indexAction(){

        return $this->fetch();
    }

    public function addAction(){
        return $this->fetch();
    }

    public function editAction(){
        return $this->fetch();
    }

    public function categoryListAction(){
        $modelCategory = model('Category');
        $query = $modelCategory ->withCount('Good')->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelCategory->getCategoryCount(),
            'data' => $query
        ]);
    }
}