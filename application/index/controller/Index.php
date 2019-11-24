<?php
namespace app\index\controller;

use think\Db;
use think\Exception;

class Index extends Common
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


    public function helloAction($a = '')
    {
        return secret('123');
    }
}
