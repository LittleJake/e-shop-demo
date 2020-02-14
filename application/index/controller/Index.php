<?php
namespace app\index\controller;

use app\common\model\Good;
use think\Db;
use think\Exception;

class Index extends Common
{	
	
    public function indexAction()
    {
        $keyword = input('get.q','');
        $modelGood = new Good();

        $goods = $modelGood->withMin('GoodCat', 'price')->whereOr([
            ['title', 'like', "%$keyword%"],
            ['keyword', 'like', "%$keyword%"]
        ])->paginate(PAGE);

		$this ->assign('goods', $goods);
		$this->assign('page_title', '首页');

        return $this->fetch();
    }

}
