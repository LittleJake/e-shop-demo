<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/8
 * Time: 22:57
 */

namespace app\index\controller;


class User extends Base
{
    //登录
    public function loginAction(){
		if($this->request->isPost())
			return $this->success();
		
		$this->assign('page_title', '登录');
		return $this->fetch();
    }
    //登出
    public function logoutAction(){
		
		return $this->success('登出成功', url('/'));
	}
    //购物车
    public function cartAction(){
		
		
		$this->assign('page_title', '购物车');
		return $this->fetch();
		
	}
    //个人订单
    public function orderAction(){
		
		$this->assign('page_title', '个人订单');
		return $this->fetch();
	}
    //地址
    public function addressAction(){
		
		$this->assign('page_title', '地址列表');
		return $this->fetch();
	}
    //增加地址
    public function addAddressAction(){
		
		$this->assign('page_title', '添加地址');
		return $this->fetch();
	}
	//注册
	public function regAction(){
		
		$this->assign('page_title', '注册');
		return $this->fetch();
	}
	
}