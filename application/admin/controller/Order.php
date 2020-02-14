<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/29
 * Time: 23:45
 */

namespace app\admin\controller;


use app\common\library\Enumcode\OrderStatus;
use app\common\library\Kuai100;

class Order extends Base
{
    /** 订单管理 */
    public function indexAction(){
        return $this->fetch();
    }

    public function orderListAction(){
        $where = [];

        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('order_no')) && $where[] = ['order_no', 'like','%'.input('order_no').'%'];
        (input('status') != '') && $where[] = ['status', '=',input('status')];


        $modelOrder = new \app\common\model\Order();
        $query = $modelOrder->p() -> with([
            'Account',
            'OrderGoods',
            'Shipping'
        ])->where($where) ->select();
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $modelOrder->getOrderCount($where),
            'data' => $query
        ]);
    }

    public function ordergoodListAction(){
        $id = input('get.id', 0);
        $order_good = model('OrderGoods');
        $query = $order_good->p() ->select();

        return json([
            'code' => 0,
            'msg' => '',
            'count' => $order_good->getGoodCount(['order_id' => $id]),
            'data' => $query
        ]);
    }

    public function infoAction(){
        return $this->fetch();
    }

    /** 快递100API 失效 */
    public function shipinfoAction(){
        $track_no = input('get.track_no',0);
        //$phone = input('get.phone',0);

        $track = Kuai100::getAutoData($track_no);
        $Com = Kuai100::getCom($track_no);
        $type = '';
        $data = [];
        if($track['code'] == 1){
            $data = $track['data']['track'];
            $type = $track['data']['type'];
        }
        $this->assign('msg', $track['msg']);
        $this->assign('track_no', $track_no);
        $this->assign('img', Kuai100::getImg($type));
        $this->assign('track', $data);
        $this->assign('Com', $Com);
        return $this->fetch();

    }

    public function shipAction(){
        if($this->request->isPost()){
            $data = $this->request->post();

            $order = model('Order');

            //全款
            $order->update([
                'status' => OrderStatus::ORDER_SHIPPING,
                'track_no' => $data['track_no']
            ],[
                'id' => $data['id'],
                'status' => OrderStatus::ORDER_PAID
            ]);
            //货到付款
            $order->update([
                'status' => OrderStatus::ORDER_PAY_AFTER_SHIPPING,
                'track_no' => $data['track_no']
            ],[
                'id' => $data['id'],
                'status' => OrderStatus::ORDER_PAY_AFTER_SHIPPING
            ]);

            return json([
                'code' => 1,
                'msg' => '发货成功'
            ]);
        }
        return json([
            'code' => 0,
            'msg' => '错误'
        ]);
    }

}