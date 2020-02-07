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
        if ($this->request->isPost()){
            $modelCategory = model('Category');

            $data = $this->request->post();

            $modelCategory -> insert($data, false);
            return json(['code' => 1, 'msg' => '添加成功']);
        }


        return $this->fetch();
    }

    public function editAction(){
        $modelCategory = model('Category');
        if ($this->request->isPost()){

            $data = $this->request->post();

            $modelCategory -> update($data, ['id' => $data['id']]);
            return json(['code' => 1, 'msg' => '修改成功']);
        }


        $this->assign('cate', $modelCategory->get(input('id')));
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