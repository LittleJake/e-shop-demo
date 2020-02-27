<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/19
 * Time: 3:02
 */

namespace app\admin\controller;


use app\common\library\Enumcode\LayuiJsonCode;

class Rate extends Base
{
    /** 评价管理（嵌套在商品列表） */
    public function indexAction(){
        $this->assign('good_id', input('good_id'));
        return $this->fetch();
    }

    public function rateListAction(){
        $where = [];

        !empty(input('good_id')) && $where[] = ['good_id', '=', input('good_id')];

        $rate = model('Rate');
        $query = $rate->p()->with('Account')->where($where)->select();
        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $rate->getRateCount($where),
            'data' => $query
        ]);
    }

    public function delAction($id = 0){
        $rate = model('Rate');

        $rate->where('id', $id)->delete() && $this->log("删除评价，ID：$id");
        return json(['code' => 1, 'msg' => '删除成功']);
    }

}