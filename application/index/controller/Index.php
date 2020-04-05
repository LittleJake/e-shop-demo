<?php
namespace app\index\controller;

use app\common\library\Enumcode\GoodStatus;

class Index extends Common
{	
	
    public function indexAction()
    {
        $keyword = input('get.q','');
        $order = input('get.order',0);
        switch ($order){
            case 2:$order = 'good_cat_min aesc';break;
            case 3:$order = 'good_cat_min desc';break;
            case 4:$order = 'order_goods_count aesc';break;
            case 5:$order = 'order_goods_count desc';break;
            default:$order = 'id desc';break;
        }
        $modelGood = model('Good');

        $goods = $modelGood->withMin('GoodCat', 'price')
            ->withCount('OrderGoods')
            ->where(function($query) use ($keyword){
                $query -> whereOr([['title', 'like', "%$keyword%"],
                    ['keyword', 'like', "%$keyword%"]]);
            })
            ->where([['status','=', GoodStatus::GOOD_IN_STOCK]])
            ->order($order)
            ->paginate(PAGE,false,['query'=>$this->request->param()]);

		$this ->assign('goods', $goods);
		$this->assign('page_title', '首页');

        return $this->fetch();
    }

}
