<?php
namespace app\index\controller;

use think\Db;
use think\Exception;

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
        $comment = Db::table('comment')
                -> where('good_id', $id)
            -> paginate(10);
        $page = $comment ->render();
        $total = $comment->total();

        $this->assign('comments', ($total == 0) ?null:$comment);
        $this->assign('page', $page);

        if($this->request->isAjax())
            return $this->fetch('index/ajaxComment');

        $good = Db::table('good')
            ->where('good_id', $id)
            ->find();

        if(!isset($good))
            return $this->error('参数错误');

        $price = Db::query("select p from `good` as G left join (SELECT good_id, min(price) as p from `category` group by good_id) as C on G.good_id = C.good_id where G.good_id = $id")[0]['p'];

        if(!isset($price))
            return $this->error('参数错误');

        $cat = Db::table('category')
            ->where('good_id', $id)
            ->select();

        if(!isset($cat))
            return $this->error('参数错误');



        $this->assign('comment_total', $total);
        $this->assign('cat', $cat);
        $this->assign('price', $price);
        $this->assign('good', $good);
		$this->assign('page_title', $good['title']);
        return $this->fetch();
	}

	public function checkoutAction()
    {
        dump(input());
        exit();
        return;
    }

    public function addCartAction()
    {
        if(!$this->isLogin()){
            return json(['status' => -1, 'msg' => 'need login']);
        }


        if($this->request->isAjax()) {
            $cat = input('cat');
            $num = input('num');
            $user = session('user_id');

            Db::startTrans();
            try{
                $query = Db::table('category')
                    ->where('cat_id', $cat)
                    ->where('sku', '>=', $num)
                    ->find();

                if(isset($query)) {
                    $query = Db::table('cart')->where([
                        'user_id' => $user,
                        'cat_id' => $cat
                    ])->find();

                    if(isset($query))
                        Db::table('cart')-> query("update `cart` set num = num + $num where cat_id = $cat and user_id = $user");
                    else
                        Db::table('cart')->insert([
                            'cat_id' => $cat,
                            'num' => $num,
                            'user_id' => $user
                        ]);
                }
                else
                    throw new \Exception;

                Db::commit();

            } catch (\Exception $e) {
                Db::rollback();
                return json(['status' => -2, 'msg' => 'failed']);
            }


            return json(['status' => 0, 'msg' => 'success']);
        }
        else
            return $this->error('参数有误');
    }

    public function helloAction($a = '')
    {
        return secret('123');
    }
}
