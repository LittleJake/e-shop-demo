<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/8
 * Time: 22:57
 */

namespace app\index\controller;

use app\common\library\Enumcode\OrderStatus;
use app\common\model\Address;
use app\common\model\GoodCat;
use app\common\model\Order;
use app\common\model\Cart;
use app\common\model\OrderGoods;
use think\Db;
use think\Exception;
use think\exception\HttpException;
use think\exception\HttpResponseException;


class User extends Base
{
    public function addCartAction()
    {
        if($this->request->isAjax()) {
            $data = $this->request->post();
            $validate = validate('Cart');

            if(!$validate->check($data))
                return json(['status' => -3, 'msg' => $validate->getError()]);

            $user = $this->userid();

            $modelGoodCat = new GoodCat();
            $modelCart = new Cart();

            try{
                $query = $modelGoodCat
                    ->where('id' , $data['cat_id'])
                    ->where('sku' ,'>=', $data['num'])
                    ->find();

                if(!empty($query)) {
                    $query = $modelCart->where([
                        'user_id' => $user,
                        'cat_id' => $data['cat_id']
                    ])->find();

                    if(!isset($query))
                        $modelCart-> insert(array_merge(
                            ['user_id' => $user], $data
                        ),false);
                    else
                        $modelCart->where([
                            'user_id' => $user,
                            'cat_id' => $data['cat_id']
                        ])-> setInc('num', $data['num']);
                }
            } catch (\Exception $e){
                return json(['status' => -2, 'msg' => $e->getMessage()]);
            }
            return json(['status' => 0, 'msg' => "success, cat: $data[cat_id], num: $data[num]"]);
        }

        return json(['status' => -1, 'msg' => "error"]);
    }

    //购物车
    public function cartAction(){
        $user = $this->userid();

        if($this->request->isAjax()){
            $del = input('del');

            Db::startTrans();
            try{
                Db::name('cart')
                    ->where([
                        'user_id' => $user,
                        'cat_id' => $del
                    ])
                    ->delete();
                Db::commit();
            }catch (\Exception $e){
                Db::rollback();
            }
        }

        $modelCart = new Cart();
        $goods = $modelCart->where([
            'user_id' => $user
        ])->with([
            'GoodCat' => function($q){
                return $q -> with([
                    'Good'=>function($q){
                        return $q->withField('id,title,img_url,is_prescription');
                    }
                ]);
            }
        ])->select();

        $this->assign('goods', $goods);
        $this->assign('page_title', '购物车');

        if($this->request->isAjax())
            return $this->fetch('user/ajaxCart');

        return $this->fetch();

    }
    //个人订单
    public function orderAction(){
        $modelOrder = new Order();
        $orders = $modelOrder
            -> where([
                'user_id' => session('user_id')
            ])
            ->with([
                'OrderGoods' => function($query){
                    return $query -> with([
                        'Good' => function($query){
                            return $query->field('id,title,img_url,is_prescription');
                        }
                    ]);
                }
            ])
            ->order('update_time', 'desc')
            ->paginate(PAGE);

        if(empty($orders)) {
            $this->assign('page_title', '个人订单');
            return $this->fetch();
        }

        $this ->assign('orders', $orders);
        $this->assign('page_title', '个人订单');
        return $this->fetch();
    }
    //地址
    public function addressAction($id = 0){
        $modelAddress = new Address();
        $user = $this->userid();

        if($this->request->isAjax()) {
            if(!$this->isLogin())
                return null;

            $query = $modelAddress -> where([
                'user_id' => $user,
                'id' => $id,
                'status' => 1
            ]) -> find();

            $this->assign('address', $query);
            return $this->fetch('user/ajaxAddress');
        }

        if(input('?del')) {
            //删除地址
            $del = input('del');

            $modelAddress ->startTrans();

            try{
                $modelAddress
                    ->where([
                        'user_id' =>$user,
                        'id' => $del
                    ])
                    ->update([
                        'status' => 0
                    ]);
                $modelAddress->commit();
            }
            catch (\Exception $e) {
                $modelAddress->rollback();
                $this->error($e->getMessage()."错误",url('index/user/address'));
            }

            $this->success("成功",'index/user/address');
        } else if(input('?add')) {
            //添加地址
            if($this->request->isPost()){
                $modelAddress = new Address();

                $data = input('post.a');

                $validator = validate('address');

                if(!$validator->check($data))
                    $this->error($validator->getError());

                $data['user_id'] = $this->userid();

                $modelAddress -> insert($data);

                $this->success('添加成功', url('index/user/address'));
            }

            $this->assign('page_title', '添加地址');
            return $this->fetch('user/add_address');
        }

        $query = $modelAddress -> where([
            'user_id' => $user,
            'status' => 1
        ]) -> select();

        $this->assign('address', $query);
        $this->assign('page_title', '地址列表');
        return $this->fetch();
    }

    //商品评价
    public function rateAction(){
        $id = input('order', '');

        if($this->request->isPost()){
            $data = $this->request->post();
            $valid = validate('Rate');
            if(!$valid->check($data))
                $this->error($valid->getError());


            $rate = model('Rate');
            $order = model('Order');
            try{
                $goods = $order->where('order_no',$id)->where('user_id',$this->userid())->with('OrderGoods')->findOrFail();
            }catch (\Exception $e){
                throw new HttpException(404);
            }

            $time = time();

            Db::startTrans();

            $user = $this->userid();
            $ins = [];

            foreach ($goods['order_goods'] as $k){
                if(!is_numeric($data['star'][$k['good_id']])||intval($data['star'][$k['good_id']]) > 5 || intval($data['star'][$k['good_id']]) < 0)
                    $data['star'][$k['good_id']] = 5;

                $ins[] = [
                    'good_id' => $k['good_id'],
                    'star' => $data['star'][$k['good_id']],
                    'comment' => empty($data['comment'][$k['good_id']])?'暂无评论':htmlentities(mb_substr($data['comment'][$k['good_id']],0,100)),
                    'user_id' => $user,
                    'update_time' => $time
                ];
            }

            try{
                $rate->insertAll($ins);
                $order ->update([
                    'status' => OrderStatus::ORDER_FINISH,
                ],[
                    'status' => OrderStatus::ORDER_NEED_COMMENT,
                    'order_no' => $id
                ]);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                $this->error('错误', url('index/user/order'));
            }
            $this->success('成功', url('index/user/order'));
        }

        $order = model('Order');
        $query = $order -> where(['order_no' => $id]) ->with([
            'OrderGoods' => function($e){
                return $e-> with([
                    'Good'=> function($e){
                        return $e-> withField('id,img_url,title');
                    }
                ]);
            }
        ])-> find();

        $this->assign('order', $query);

        $this->assign('page_title', '评价商品');
        return $this->fetch();
    }


    public function payAction($id = ''){
        $modelOrder = model('Order');
        Db::startTrans();
        try{
            $query = $modelOrder->where('order_no', $id)->find();
            $Balance = new Balance();
            if($Balance -> BalanceChange($query['total_price'])){
                $modelOrder
                    -> update([
                        'status' => OrderStatus::ORDER_PAID
                    ],['order_no' => $id, 'user_id' => $this->userid()]);
                Db::commit();
                $this->success('支付成功');
            }else {
                Db::commit();
                $this->error('支付失败，余额不足');
            }
        } catch (HttpResponseException $e){
            throw $e;
        } catch (\Exception $e){
            Db::rollback();
        }
        $this-> error('未知错误');
    }

    public function afterPayAction($id = ''){

        $modelOrder = model('Order');
        try{
            $modelOrder
                -> update([
                    'status' => OrderStatus::ORDER_PAY_AFTER_SHIPPING
                ],['order_no' => $id, 'status' => OrderStatus::ORDER_UNPAID, 'user_id' => $this->userid()]);
            $this->success('处理成功');
        } catch (HttpResponseException $e){
            throw $e;
        } catch (\Exception $e){
            $this-> error('处理失败');
        }

    }

    public function shippedAction($id = ''){
        $modelOrder = model('Order');

        $modelOrder
            -> update([
                'status' => OrderStatus::ORDER_NEED_COMMENT
            ],['order_no' => $id, 'user_id' => $this->userid()]);

        $this->success('收货成功');
    }
    public function cancelAction($id){
        $modelOrder = model('Order');
        $balance = new Balance();
        //货到付款（未发货）
        $modelOrder
            -> where(['order_no' => $id, 'user_id' => $this->userid(), 'status' => OrderStatus::ORDER_PAY_AFTER_SHIPPING])->where('track_no' ,'NULL','') ->setField('status', OrderStatus::ORDER_CLOSE) && $this->success('取消成功');
        //未付款
        $modelOrder
            -> where(['order_no' => $id, 'user_id' => $this->userid(), 'status' => OrderStatus::ORDER_UNPAID]) ->setField('status', OrderStatus::ORDER_CLOSE) && $this->success('取消成功');
        //已付款，未发货
        $modelOrder
            -> where(['order_no' => $id, 'user_id' => $this->userid(), 'status' => OrderStatus::ORDER_PAID])->setField('status', OrderStatus::ORDER_CLOSE)
        && ($query = $modelOrder
                -> where(['order_no' => $id, 'user_id' => $this->userid()])->find())
        && $balance->BalanceChange($query['total_price'], false)
        && $this->success('取消成功');

        $this->error('取消失败，请联系客服');
    }

    public function changepwdAction(){
        if($this->request->isPost()){
            $data = $this->request->post('a');
            $valid = validate('user');
            if(!$valid->scene('change')->check($data))
                $this->error($valid->getError());

            $account = model('Account');

            $query = $account->getOrFail($this->userid());

            if(check_secret($query['password'], $data['old_password']))
                $this->error('原密码错误');

            $account -> update(['password' => secret($data['password'])],['id'=> $this->userid()]);

            $this->success('修改成功');
        }


        $this->assign('page_title','修改密码');
        return $this->fetch();
    }

    public function uploadPrescriptionAction(){
        try{
            $u = $this->uploadAction();
            if($u['uploaded'] == 0)
                throw new Exception();

            $id = input('post.id');
            $order_goods = new OrderGoods();
            if(!empty($order_goods->withJoin('Order')->where('order.user_id', $this->userid())->where('order_goods.id', $id) -> cache(true, 600)->findOrEmpty()))
                $order_goods->where('id', $id)->update(['prescription' => $u['url']]);
            else
                return json(['code' => 0, 'msg' => '失败']);

            return json(['code' => 1, 'msg' => '成功']);
        } catch (\Exception $e){
        }

        return json($u);
    }
}