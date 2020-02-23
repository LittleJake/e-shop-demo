<?php
namespace app\index\controller;

use app\common\library\Enumcode\GoodStatus;
use app\common\model\Good;

class Index extends Common
{	
	
    public function indexAction()
    {
        $keyword = input('get.q','');
        $modelGood = new Good();

        $goods = $modelGood->withMin('GoodCat', 'price')
            ->where(function($query) use ($keyword){
                $query -> whereOr([['title', 'like', "%$keyword%"],
                    ['keyword', 'like', "%$keyword%"]]);
            })
            ->where([['status','=', GoodStatus::GOOD_IN_STOCK]])
            ->paginate(PAGE);

		$this ->assign('goods', $goods);
		$this->assign('page_title', '首页');

        return $this->fetch();
    }

}
