<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/1/29
 * Time: 23:45
 */

namespace app\admin\controller;


use app\common\library\Enumcode\LayuiJsonCode;
use app\common\library\Enumcode\OrderStatus;
use app\common\library\Kuai100;
use app\common\model\OrderGoods;

class Order extends Base
{
    /** 订单管理 */
    public function indexAction(){
        return $this->fetch();
    }

    public function needShipAction(){
        return $this->fetch();
    }

    public function orderListAction(){
        $where = [];

        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('order_no')) && $where[] = ['order_no', 'like','%'.input('order_no').'%'];
        (input('status') != '') && $where[] = ['status', '=',input('status')];


        $modelOrder = model('Order');
        $query = $modelOrder->p() -> with([
            'OrderGoods' => function($query){
                return $query->withCount([
                    'Good'=>function($q){
                        return $q->where('is_prescription', 1);
                    }
                ]);
            },
            'Shipping'
        ])->where($where) ->order('id desc')->select();

        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $modelOrder->getOrderCount($where),
            'data' => $query
        ]);
    }

    public function shipListAction(){
        $where = [];

        !empty(input('id')) && $where[] = ['id', '=', input('id')];
        !empty(input('order_no')) && $where[] = ['order_no', 'like','%'.input('order_no').'%'];
        $where[] = ['status', 'in', OrderStatus::ORDER_PAY_AFTER_SHIPPING.','.OrderStatus::ORDER_PAID];
        $where[] = ['track_no' ,'NULL',''];


        $modelOrder = model('Order');
        $query = $modelOrder->p() -> with([
            'Account',
            'OrderGoods',
            'Shipping'
        ])->where($where) ->select();
        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $modelOrder->getOrderCount($where),
            'data' => $query
        ]);
    }

    public function ordergoodListAction($id = 0){
        $order_good = model('OrderGoods');
        $where[] = ['order_id', '=', $id];
        $query = $order_good->p()->with([
            'Good' => function($query){
                return $query->withField('id,is_prescription');
            }
        ])->where($where)->select();

        return json([
            'code' => LayuiJsonCode::SUCCESS,
            'msg' => 'success',
            'count' => $order_good->getGoodCount($where),
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
            (
                //全款
                $order->update([
                    'status' => OrderStatus::ORDER_SHIPPING,
                    'track_no' => $data['track_no']
                ],[
                    'id' => $data['id'],
                    'status' => OrderStatus::ORDER_PAID
                ]) ||
                //货到付款
                $order->update([
                    'status' => OrderStatus::ORDER_PAY_AFTER_SHIPPING,
                    'track_no' => $data['track_no']
                ],[
                    'id' => $data['id'],
                    'status' => OrderStatus::ORDER_PAY_AFTER_SHIPPING
                ])
            )&& $this->log("订单发货，ID：$data[id]");

            return json(['code' => 1, 'msg' => '发货成功']);
        }

        return json(['code' => 0, 'msg' => '错误']);
    }

    public function reviewAction(){
        $order_good = new OrderGoods();
        $id = input('id');

        if($this->request->isPost()){
            $status = input('status');
            $status = in_array($status, ['true', 'false'])?$status:'false';

            $order_good -> where('id',$id)->update([
                'prescription' => $status,
            ]);


            return json(['code' => 1, 'msg' => '成功']);
        }

        $query = $order_good->get($id);
        $this->assign('img', $query['prescription']);
        return $this->fetch();
    }

}