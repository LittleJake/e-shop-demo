<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 0:22
 */

namespace app\index\controller;

use think\Db;

class Shop extends Base
{
    //店铺商品
    public function shopInfoAction($page = 1, $id){
        if($page < 1)
            $this->error('参数错误');

        $query = Db::table('shop') -> where('shop_id', $id) -> find();

        $this -> assign('shop_name', $query['shop_name']);

        $cat = '(SELECT good_id, min(price) as p from `category` group by good_id)';

        $goods = Db::table("good")->alias('G')->leftJoin("$cat C", 'G.good_id = C.good_id')->where(['shop_id' => $id])->paginate(5);


        $this ->assign('goods', $goods);
        $this->assign('page_title', '店铺商品');
        return $this->fetch();
    }

    //店铺列表
    public function shopListAction($page = 1){
        if($page < 1)
            $this->error('参数错误');

        $query = Db::query("select * from `shop`");

        $m = (0 == (ceil(count($query) / PAGE))?1:ceil(count($query) / PAGE));


        if($page > $m)
            $this->error('参数错误');

        $shops = array_slice($query, ($page - 1) * PAGE, PAGE);
        $goods = array();

        foreach($shops as $k)
            $goods[$k['shop_id']] = Db::query("select * from `good` as G left join (SELECT good_id, min(price) as p from `category` group by good_id) as C on G.good_id = C.good_id where G.shop_id = $k[shop_id] limit 3");


        $page = ['min' => 1,
            'max' => $m,
            'cur' => $page];

        $this->assign('goods', $goods);
        $this->assign('page', $page);
        $this ->assign('shops', $shops);

        $this->assign('page_title', '店铺列表');
        return $this->fetch();
    }
}