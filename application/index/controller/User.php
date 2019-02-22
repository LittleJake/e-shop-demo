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
    //登录
    public function loginAction(){
		if($this->request->isPost()){
			$email = input('post.email');
			$pass = input('post.pass');
			
			if(!isset($email) || !isset($pass))
				return $this->error('参数错误');
			
			$query = db('user') -> where('email', $email)-> find();
			
			if(!isset($query))
				return $this->error('参数错误');
			
			if($query['password'] != secret($pass))
				return $this->error('密码错误');
			
			session('user', $query['user_name']);
			session('user_id', $query['user_id']);
			
			return $this->success($query['user_name'] . '，欢迎回来', url('/'));
				
		}
			
		
		$this->assign('page_title', '登录');
		return $this->fetch();
    }
    //登出
    public function logoutAction(){
		session(null);
		
		return $this->success('登出成功', url('/'));
	}
    //购物车
    public function cartAction(){
		
		
		$this->assign('page_title', '购物车');
		return $this->fetch();
		
	}
    //个人订单
    public function orderAction($page = 1){
		if($page < 1)
			return $this->error('参数错误');
		
		$query = \think\Db::query("select * from `order` where user_id = ". session('user_id'));
		
		if(count($query) != 0)
			$m = floor(count($query) / 5);
		else {
			$this->assign('page_title', '个人订单');
			return $this->fetch();
		}
		
		if($page > $m)
			return $this->error('参数错误');
		
		$orders = array_slice($query, ($page - 1) * 5, 5);
		$goods = array();
		$total = 0;
		foreach($orders as $k){
			$goods[$k['order_id']] = \think\Db::query("select * from `order_good` as A where order_id = $k.order_id left join `category` as B on A.cat_id = B.cat_id left join `good` as C on B.good_id = C.good_id");
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
		
		$query = Db::query("select * from `address` where user_id = " . session('user_id'));
		
		$this->assign('address', $query);
		$this->assign('page_title', '地址列表');
		return $this->fetch();
	}
    //增加地址
    public function addAddressAction(){
		if($this->request->isPost()){
			$name = input('post.name');
			$region = input('post.region');
			$province = input('post.province');
			$city = input('post.city');
			$district = input('post.district');
			$street = input('post.street');
			$code = input('post.code');
			$contact = input('post.contact');
			
			if(!isset($name) || !isset($region)||!isset($province)|| !isset($city)|| !isset($district)|| !isset($street)||!isset($code) || !isset($contact))
				return $this->error('参数错误');
			
			
			Db::startTrans();
			try{
				Db::table('address')->insert([
					'name' => $name,
					'region' => $region,
					'province' => $province,
					'city' => $city,
					'district' => $district,
					'street' => $street,
					'code' => $code,
					'contact' => $contact,
					'user_id' => session('user_id')
				
				]);
				Db::commit();
			} catch(\Exception $e) {
				Db::rollback();
				return $this->error('参数错误');
			}
			
			return $this->success('添加成功', url('user/address'));
			
		}
			
		
		
		$this->assign('page_title', '添加地址');
		return $this->fetch();
	}
	//注册
	public function regAction(){
		if($this->request->isPost()){
			$email = input('post.email');
			$pass = input('post.pass');
			$user = input('post.user');
			$mobile = input('post.mobile');
			
			if(!isset($email) || !isset($pass) || !isset($user) || !isset($mobile))
				return $this->error('参数错误');
			
			$db = db('user');
			
			$query =  $db -> where('email', $email)-> find();
			
			if(isset($query))
				return $this->error('参数错误');
			
			$query = $db -> where('user_name', $user)-> find();
			
			if(isset($query))
				return $this->error('参数错误');
			
			$query = $db -> where('mobile', $mobile)-> find();
			
			if(isset($query))
				return $this->error('参数错误');
			
			Db::startTrans();
			try{
				Db::table('user') -> insert([
					'user_name' => $user,
					'password' => secret($pass),
					'email' => $email,
					'mobile' => $mobile
				]);
				Db::commit();
			} catch (\Exception $e) {
				Db::rollback();
				return $this->error('注册失败');
			
			}
			

			
			return $this->success('注册成功', url('user/login'));
				
		}
		
		$this->assign('page_title', '注册');
		return $this->fetch();
	}
	
}