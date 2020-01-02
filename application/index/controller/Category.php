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
    public function indexAction($id = 0){
        $modelCategory = new \app\common\model\Category();
        $modelGood = new \app\common\model\Good();
        $category = $modelCategory->withCount('Good') ->select();

        $this->assign('category', $category);

        if($id != 0){
            if(empty($category)){
                $this->assign('goods', []);
                $this->assign('cate_name', '');
            } else{
                $goods = $modelGood -> where([
                    'cate_id' => $id
                ]) ->withMin('GoodCat', 'price') -> paginate(15);

                $this->assign('cate_name', $modelCategory->get($id)['name']);
                $this->assign('goods', $goods);
            }
        }
        else{
            if(empty($category)){
                $this->assign('goods', []);
                $this->assign('cate_name', '');
            } else{
                $goods = $modelGood -> where([
                    'cate_id' => $category[0]['id']
                ]) ->withMin('GoodCat', 'price') -> paginate(15);

                $this->assign('cate_name', $category[0]['name']);
                $this->assign('goods', $goods);
            }

        }

        $this->assign('page_title', "分类");
        $this->assign('id', $id);
        return $this->fetch();
    }
}