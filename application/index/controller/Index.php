<?php
namespace app\index\controller;

class Index extends Base
{
    public function indexAction()
    {
		$query = \think\Db::query("select * from `good` as G left join (SELECT good_id, min(price) as p from `category` group by good_id) as C on G.good_id = C.good_id");
		
		$this ->assign('goods', $query);
		$this->assign('page_title', '首页');
        return $this->fetch();
    }

    //店铺商品
    public function shopInfoAction(){
		$this->assign('page_title', '店铺商品');
        return $this->fetch();
    }

    //店铺列表
    public function shopListAction(){
		
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
