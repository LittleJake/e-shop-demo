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
        if($this->request->isPost()){
            $modelGood = new \app\common\model\Good();
            $g = $this->request->post('g');
            $c = $this->request->post('c');
            $c['name'] = 'default';
            try{
                $id = $modelGood->insertGetId($g);
                $modelGood ->GoodCat()->insert(array_merge(['good_id'=> $id], $c));
            } catch (\Exception $e){
                return json(['code' => 0, 'msg' => $e->getMessage()]);
            }


            return json(['code' => 1, 'msg' => 'success']);
        }



        $modelCategory = new Category();
        $cate = $modelCategory -> select();


        $this->assign('cate', $cate);

        return $this->fetch();
    }

    public function editAction()
    {
        if($this->request->isPost()){

        }



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

    public function delAction($id = 0){
        if(empty($id))
            return json(['code' => 0, 'msg' => 'failed']);

        $modelGood = new \app\common\model\Good();
        try{
            $modelGood->where(['id' => $id])->delete();
        } catch (\Exception $e){
            return json(['code' => 0, 'msg' => 'failed']);
        }


        return json(['code' => 1, 'msg' => 'success']);
    }
}
