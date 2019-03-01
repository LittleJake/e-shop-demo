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
		
		$m = (0 == (ceil(count($query) / PAGE))?1:ceil(count($query) / PAGE));

		if($page > $m)
			return $this->error('参数错误');
		
		$goods = array_slice($query, ($page - 1) * PAGE, PAGE);
		
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
		
		$m = (0 == (ceil(count($query) / PAGE))?1:ceil(count($query) / PAGE));
		
		if($page > $m)
			return $this->error('参数错误');
		
		$goods = array_slice($query, ($page - 1) * PAGE, PAGE);
		
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
		
		$m = (0 == (ceil(count($query) / PAGE))?1:ceil(count($query) / PAGE));
		
		
		if($page > $m)
			return $this->error('参数错误');
		
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

    //下单
    public function orderAction(){
		
		$this->assign('page_title', '确认订单');
        return $this->fetch();
	}
    //商品详情
    public function goodAction($id = ''){

        $good = Db::table('good')
            ->where('good_id', $id)
            ->find();

        if(!isset($good))
            return $this->error('参数错误');

        $price = Db::query("select p from `good` as G left join (SELECT good_id, min(price) as p from `category` group by good_id) as C on G.good_id = C.good_id where G.good_id = $id")[0]['p'];

        if(!isset($price))
            return $this->error('参数错误');

        $this->assign('price', $price);
        $this->assign('good', $good);
		$this->assign('page_title', $good['title']);
        return $this->fetch();
	}

    public function helloAction($a = '')
    {
        return secret('123');
    }
}
