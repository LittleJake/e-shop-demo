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
        $modelCategory = new Category();
        $cate = $modelCategory -> select();


        $this->assign('cate', $cate);

        return $this->fetch();
    }

    public function editAction()
    {
        $data = input();
        $modelGood = new \app\common\model\Good();
        $good = $modelGood -> where([
            'id' =>$data['id']
        ]) -> find();

        $modelCategory = new Category();
        $cate = $modelCategory -> select();

        $this->assign('good', $good);
        $this->assign('cate', $cate);

        return $this->fetch();
    }

    public function goodlistAction(){
        $modelGood = new \app\common\model\Good();
        $query = $modelGood -> with('Category') ->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelGood->getGoodCount(),
            'data' => $query
        ]);
    }
}
