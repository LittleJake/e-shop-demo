<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/23/2019
 * Time: 1:19 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\admin\controller;


use app\common\model\Category;
use think\App;

class Good extends Base
{
    public function indexAction(){
        return $this->fetch();
    }

    public function listAction(){
        $modelCategory = new Category();

        $category = $modelCategory -> where([
            'parent_id' => 0
        ])->select();


        $this -> assign('category', $category);

        return $this->fetch();
    }

    public function addAction()
    {
        return $this->fetch();
    }
}
