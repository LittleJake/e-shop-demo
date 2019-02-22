<?php
namespace app\index\controller;

use think\Db;

class Index extends Base
{
    public function indexAction($page = 1)
    {
		if($page < 1)
			return $this->error('参数错误');
		
		$query = Db::query("select * from `good` as G left join (SELECT good_id, min(price) as p from `category` group by good_id) as C on G.good_id = C.good_id");
		
		$m = floor(count($query) / 5);
		
		if($page > $m)
			return $this->error('参数错误');
		
		$goods = array_slice($query, ($page - 1) * 5, 5);
		
		$page = ['min' => 1,
				'max' => $m,
				'cur' => $page];
		
		$this->assign('page', $page);
		$this ->assign('goods', $goods);
		$this->assign('page_title', '首页');
        return $this->fetch();
    }

    //店铺商品
    public function shopInfoAction($page = 1, $id){
		if($page < 1)
			return $this->error('参数错误');
		
		$query = Db::table('shop') -> where('shop_id', $id) -> find();
		
		$this -> assign('shop_name', $query['shop_name']);
		
		$query = Db::query("select * from `good` as G left join (SELECT good_id, min(price) as p from `category` group by good_id) as C on G.good_id = C.good_id where G.shop_id = $id");
		
		$m = floor(count($query) / 5);
		
		if($page > $m)
			return $this->error('参数错误');
		
		$goods = array_slice($query, ($page - 1) * 5, 5);
		
		$page = ['min' => 1,
				'max' => $m,
				'cur' => $page];
		
		$this->assign('page', $page);
		$this ->assign('goods', $goods);
		$this->assign('page_title', '店铺商品');
        return $this->fetch();
    }

    //店铺列表
    public function shopListAction($page = 1){
		if($page < 1)
			return $this->error('参数错误');
		
		$query = Db::query("select * from `shop`");
		
		$m = floor(count($query) / 5);
		
		if($page > $m)
			return $this->error('参数错误');
		
		$shops = array_slice($query, ($page - 1) * 5, 5);
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

    //下单
    public function orderAction(){
		
		$this->assign('page_title', '确认订单');
        return $this->fetch();
	}
    //商品详情
    public function goodAction(){
		
		$this->assign('page_title', '详情');
        return $this->fetch();
	}

    public function helloAction($a = '')
    {
        return secret('123');
    }
}
