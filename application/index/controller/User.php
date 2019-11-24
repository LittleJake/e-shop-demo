<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/8
 * Time: 22:57
 */

namespace app\index\controller;

use think\Db;

class User extends Base
{
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
    //购物车
    public function cartAction(){

        $user = session('user_id');

        if($this->request->isAjax()){
            $del = input('del');

            Db::startTrans();
            try{
                Db::table('cart')
                    ->where([
                        'user_id' => $user,
                        'cat_id' => $del
                    ])
                    ->delete();
                Db::commit();
            }catch (\Exception $e){
                Db::rollback();
            }
        }

        $goods = Db::query("select * from `cart` left join `category` on cart.cat_id = category.cat_id left join `good` on category.good_id = good.good_id where cart.user_id = $user");


        $this->assign('goods', $goods);
		$this->assign('page_title', '购物车');

		if($this->request->isAjax())
            return $this->fetch('user/ajaxCart');

		return $this->fetch();
		
	}
    //个人订单
    public function orderAction($page = 1){

		if($page < 1)
			return $this->error('参数错误');
		
		$query = Db::query("select * from `order` where user_id = ". session('user_id') . "  order by `time` desc");
		
		if(count($query) != 0)
            $m = (0 == (ceil(count($query) / PAGE))?1:ceil(count($query) / PAGE));
		else {
			$this->assign('page_title', '个人订单');
			return $this->fetch();
		}
		
		if($page > $m)
			return $this->error('参数错误');
		
		$orders = array_slice($query, ($page - 1) * PAGE, PAGE);
		$goods = array();
		$total = 0;
		foreach($orders as $k){

			$goods[$k['order_id']] = Db::query("select * from `order_good` as A left join `category` as B on A.cat_id = B.cat_id left join `good` as C on B.good_id = C.good_id where A.order_id = $k[order_id]");

			foreach($goods[$k['order_id']] as $v)
			    $total += ($v['price'] * $v['num']);
		}
		
		
		$page = ['min' => 1,
				'max' => $m,
				'cur' => $page];
		
		$this->assign('page', $page);
		$this ->assign('orders', $orders);
		$this ->assign('goods', $goods);
		$this->assign('total', $total);
		$this->assign('page_title', '个人订单');
		return $this->fetch();
	}
    //地址
    public function addressAction(){
        if($this->request->isAjax()) {
            if(!$this->isLogin())
                return null;
            $user = session('user_id');

            $id = (int)input('id');

            $query = Db::query("select * from `address` where user_id = $user and address_id = $id");

            $this->assign('address', $query);
            return $this->fetch('index/user/ajaxAddress');
        }

        $user = session('user_id');

        if(input('?del'))
        {
            $del = input('del');

            Db::startTrans();

            try{
                Db::table('address')
                    ->where([
                        'user_id' =>$user,
                        'address_id' => $del
                    ])
                    ->delete();
                Db::commit();
            }
            catch (\Exception $e) {
                Db::rollback();
                return $this->error($e->getMessage()."错误",url('index/user/address'));
            }


            return $this->success("成功",'index/user/address');
        }

		$query = Db::query("select * from `address` where user_id = $user");
		
		$this->assign('address', $query);
		$this->assign('page_title', '地址列表');
		return $this->fetch();
	}
    //增加地址
    public function addAddressAction(){
		if($this->request->isPost()){
		    $data = input('post.a');

            $validator = validate('address');

			if(!$validator->check($data))
				return $this->error($validator->getError());
			
			$data['user_id'] = session('user_id');

			Db::startTrans();
			try{
				Db::table('address')->insert($data);
				Db::commit();
			} catch(\Exception $e) {
				Db::rollback();
				return $this->error($e->getMessage().'参数错误');
			}
			
			return $this->success('添加成功', url('index/user/address'));
			
		}
			
		
		
		$this->assign('page_title', '添加地址');
		return $this->fetch();
	}

	//商品评价
    public function commentAction(){

        $user = session('user_id');
        $id = (int)input('id');
        $query = Db::query("select * from `order` left join `order_good` on `order`.order_id = order_good.order_id left join `category` on order_good.cat_id = category.cat_id left join `good` on category.good_id = good.good_id where order_good.order_id = $id and user_id = $user");

        if($this->request->isPost()){
            $post = input();
            foreach ($query as $k) {
                $star = array_shift($post);
                $content = array_shift($post);

                if(empty($content))
                    $content = '暂无文字评论';

                Db::startTrans();
                try{
                    Db::table('comment')
                        -> insert([
                            'good_id' => $k['good_id'],
                            'rate' => $star,
                            'comment_content' => htmlspecialchars($content)
                        ]);

                    Db::commit();

                } catch (\Exception $e) {
                    Db::rollback();
                    return $this->error('错误', url('user/order'));
                }

            }

            Db::startTrans();
            try{
                Db::table('order')
                    -> where('order_id', $id)
                    -> update([
                        'status' => 50
                    ]);

                Db::commit();

            } catch (\Exception $e) {
                Db::rollback();
                return $this->error('错误', url('user/order'));
            }

            return $this->success('成功', url('user/order'));
        }

        $this->assign('good', $query);


        $this->assign('page_title', '评价商品');
        return $this->fetch();
    }
}