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


use think\exception\HttpException;
use think\exception\HttpResponseException;

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
                try{
                    $this->assign('cate_name', $modelCategory->getOrfail($id)['name']);
                } catch (\Exception $e){
                    throw new HttpException(404);
                }

                $order = input('get.order',0);
                switch ($order){
                    case 2:$order = 'good_cat_min aesc';break;
                    case 3:$order = 'good_cat_min desc';break;
                    case 4:$order = 'order_goods_count aesc';break;
                    case 5:$order = 'order_goods_count desc';break;
                    default:$order = 'id desc';break;
                }
                $goods = $modelGood ->withCount('OrderGoods')
                    -> where([
                    'cate_id' => $id
                ])
                    ->withMin('GoodCat', 'price')
                    ->order($order)
                    -> paginate(PAGE);

                $this->assign('goods', $goods);
            }
        }
        else{
            if(empty($category)){
                $this->assign('goods', []);
                $this->assign('cate_name', '');
            } else{
                $order = input('get.order',0);
                switch ($order){
                    case 2:$order = 'good_cat_min aesc';break;
                    case 3:$order = 'good_cat_min desc';break;
                    case 4:$order = 'order_goods_count aesc';break;
                    case 5:$order = 'order_goods_count desc';break;
                    default:$order = 'id desc';break;
                }
                $goods = $modelGood->withCount('OrderGoods')
                    -> where([
                    'cate_id' => $category[0]['id']
                ])
                    ->withMin('GoodCat', 'price')
                    ->order($order)
                    ->paginate(15);

                $this->assign('cate_name', $category[0]['name']);
                $this->assign('goods', $goods);
            }

        }

        $this->assign('page_title', "分类");
        $this->assign('id', $id);
        return $this->fetch();
    }
}