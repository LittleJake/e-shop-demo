<?php
namespace app\index\controller;

use think\Db;
use think\Exception;

class Index extends Base
{	
	
    public function indexAction()
    {
        $keyword = input('get.q','');
        $cat = '(SELECT good_id, min(price) as p from `category` group by good_id)';

        $goods = Db::table("good")
            ->alias('G')
            ->leftJoin("$cat C", 'G.good_id = C.good_id')
            ->where([
                ['G.title','like', "%$keyword%"]
            ])
            ->paginate(5);

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

        $cat = '(SELECT good_id, min(price) as p from `category` group by good_id)';

        $goods = Db::table("good")->alias('G')->leftJoin("$cat C", 'G.good_id = C.good_id')->where(['shop_id' => $id])->paginate(5);


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
        if(!$this->isLogin())
            return $this->redirect('user/login', ['r' => urlencode($this->request->url())]);

        if(!input('?cat') || !input('?num') || !input('?pay')|| !input('?add_id') || !input('?total') || !input('?ship'))
            return $this->redirect('/');

        $user = session('user_id');
        $cat = input('cat');
        $num = input('num');
        $ship = input('ship');
        $pay = input('pay');
        $add_id = input('add_id');
        $total = input('total');
        $time = time();
        $date = date('Ymd');

        //订单号生成
        while (true){
            $order_id =  $date. substr('00' . rand(0, 99),-2,2);
            $query = Db::query("select * from `order` where order_id = $order_id");
            if(empty($query))
                break;
        }

        Db::startTrans();
        try{
            Db::table('order')
                -> insert([
                    'order_id' => $order_id,
                    'address_id' => $add_id,
                    'payment_id' => $pay,
                    'total_price' => $total,
                    'shipping_id' => $ship,
                    'time' => $time,
                    'status' => 0,
                    'user_id' =>$user
                ]);
            Db::commit();
        }
        catch (\Exception $e) {
            Db::rollback();
            $this->error('错误', url('/'));
        }

        $goods = Db::query("select * from `category` left join `good` on category.good_id = good.good_id where category.cat_id in ($cat)");

        $cat = explode(',', $cat);
        $num = explode(',', $num);

        $order = array_combine($cat, $num);
        $no_goods = array();

        foreach ($goods as $c => $d) {
            foreach ($order as $a => $b) {
                if($d['cat_id'] == $a){
                    if($goods[$c]['sku'] < $b) {
                        //库存不足时
                        $no_goods[] = array_pop($goods[$c]);
                        break;
                    }

                    Db::startTrans();
                    try {
                        //订单物品添加
                        Db::table('order_good')
                            -> insert([
                                'order_id' => $order_id,
                                'cat_id' => $a,
                                'num' => $b
                            ]);
                        //减少库存
                        Db::table('category')
                            -> where('cat_id', $a)
                            ->update([
                                'sku' => $goods[$c]['sku'] - (int)$b
                            ]);
                        //删除购物车
                        Db::table('cart')
                            -> where([
                                'user_id' => $user,
                                'cat_id' => $a
                                ])
                            ->delete();

                        Db::commit();

                    }
                    catch (\Exception $e) {
                        Db::rollback();
                    }
                    break;
                }
            }
        }



        return $this->success('成功', url('user/order'));
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
        if(!$this->isLogin())
            return $this->redirect('user/login', ['r' =>  urlencode($this->request->url(true))]);

        if(!input('?cat') || !input('?num'))
            return $this->redirect('/');

        $cat = input('cat');
        $num = input('num');
        $this->assign('cat', $cat);
        $this->assign('num', $num);

        $user = session('user_id');

        $query = Db::query("select * from `address` where user_id = $user");

        $this->assign('address', $query);

        $query = Db::query("select * from `shipping_fee`");

        $this->assign('ships', $query);

        $goods = Db::query("select * from `category` left join `good` on category.good_id = good.good_id where category.cat_id in ($cat)");

        $cat = explode(',', $cat);
        $num = explode(',', $num);

        $order = array_combine($cat, $num);
        $total = 0;
        foreach ($goods as $c => $d) {
            foreach ($order as $a => $b) {
                if($d['cat_id'] == $a){
                    $goods[$c]['num'] = $b;
                    $total += $goods[$c]['price'] * $b;
                    break;
                }
            }
        }

        $this->assign('goods', $goods);
        $this->assign('total', $total);

        $query = Db::query("select * from `payment`");

        $this->assign('payments', $query);


        $this->assign('page_title', '结算');
        return $this->fetch();
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

            if( !isset($num)||!isset($user))
                return json(['status' => -2, 'msg' => 'failed']);

            if(!isset($cat))
                return json(['status' => -3, 'msg' => 'failed']);

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


            return json(['status' => 0, 'msg' => "success, cat: $cat, num: $num"]);
        }
        else
            return $this->error('参数有误');
    }

    public function helloAction($a = '')
    {
        return secret('123');
    }
}
