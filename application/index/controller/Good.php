<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 0:22
 */

namespace app\index\controller;

use app\common\library\Enumcode\GoodStatus;
use app\common\library\Enumcode\OrderStatus;
use app\common\library\Enumcode\ShippingStatus;
use app\common\model\Address;
use app\common\model\GoodCat;
use app\common\model\OrderGoods;
use app\common\model\Shipping;
use think\Db;

class Good extends Common
{
    //下单
    public function orderAction(){
        if(!$this->isLogin())
            $this->redirect('index/login/login', ['r' => urlencode($this->request->url())]);

        if($this->request->isPost()){
            $data = $this->request->post('a');
            $orderValidate = validate('Order');
            if(!$orderValidate->check($data))
                $this->error($orderValidate->getError());

            $user = session('user_id');
            $time = time();
            $date = date('YmdHis');

            $cat = explode(',', $data['cat']);
            $num = explode(',', $data['num']);

            //订单号生成

            $order_no =  $date. substr('0000' . rand(0, 9999),-4,4);

            $modelOrder = new \app\common\model\Order();
            $modelGoodCat = new GoodCat();
            $modelOrderGoods = new OrderGoods();

            Db::startTrans();
            try{
                $goods = $modelGoodCat
                    ->with([
                        'Good' => function($q){return $q->where('status', '=',1);}
                    ])
                    ->where([
                        'id' => $cat
                    ])
                    ->select();
                $modelGoodCat->update();

                //物品计算
                $order = array_combine($cat, $num);
                $total = 0;
                $ins = [];
                foreach ($goods as $good) {
                    if(!isset($good['good']))
                        continue;

                    if($modelGoodCat
                        ->where(['id' => $good['id']])
                        ->where('sku', '>=', $order[$good['id']])
                        -> setDec('sku',$order[$good['id']])){
                        $ins[] = [
                            'name' => $good['good']['title'],
                            'price' => $good['price'],
                            'good_id'=> $good['good']['id'],
                            'num' => $order[$good['id']],
                            'order_id' => 0
                        ];
                        $total += $order[$good['id']] * $good['price'];
                    }
                }
                //运费计算
                $ship = model('Shipping');
                $query = $ship->where([
                    'status' => 1,
                    'id' => $data["ship"]
                ])->find();

                $total += $query['price'];

                $modelOrder
                    -> insert([
                        'order_no' => $order_no,
                        'address_id' => $data["add_id"],
                        'total_price' => $total,
                        'shipping_type' => $data["ship"],
                        'update_time' => $time,
                        'status' => OrderStatus::ORDER_UNPAID,
                        'user_id' =>$user
                    ]);
                $order_id=Db::getLastInsID();

                for ($i = 0; $i< count($ins); $i++)
                    $ins[$i]['order_id'] = $order_id;

                $modelOrderGoods ->insertAll($ins);

                $Balance = new Balance();
                if($Balance -> BalanceChange($total)){
                    $modelOrder
                        -> update([
                            'status' => OrderStatus::ORDER_PAID
                        ],['id' => $order_id]);
                }
                Db::commit();
            }
            catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage(), url('/'));
            }

            $this->success('成功', url('index/user/order'));
        }

        $this->redirect('/');
    }
    //商品详情
    public function goodAction($id = ''){
        $modelRate = model('Rate');
        $comment = $modelRate->where([
            'good_id' => $id
        ])->with([
            'Account' => function($e){return $e->withField('id,username');}
        ])->paginate(PAGE);

        $page = $comment ->render();
        $total = $comment->total();

        $this->assign('comments', ($total == 0) ?null:$comment);
        $this->assign('page', $page);

        if($this->request->isAjax())
            return $this->fetch('good/ajaxComment');

        $modelGood = model('Good');
        $good = $modelGood
            ->where([
                'id' => $id
            ])
            ->with([
                'GoodCat' => function($query){
                    $query -> order('price aesc')-> select();
                },
            ])
            ->find();

        if(!isset($good))
            $this->error('参数错误');

        $this->assign('comment_total', $total);
        $this->assign('cat', $good->good_cat);
        $this->assign('good', $good);
        $this->assign('page_title', $good['title']);
        return $this->fetch();
    }

    public function checkoutAction()
    {
        if(!$this->isLogin())
            $this->redirect('index/login/login', ['r' =>  urlencode($this->request->url(true))]);

        if($this->request->isPost()){
            $data = $this->request->post();
            $validate = validate('Order');
            if(!$validate->scene('checkout')->check($data))
                $this->error($validate->getError());

            $this->assign('data', $data);

            $user = session('user_id');

            $modelAddress =  model('Address');
            $query = $modelAddress -> where([
                'status' => 1,
                'user_id' => $user
            ])->select();

            $this->assign('address', $query);

            $modelShipping = model('Shipping');
            $query = $modelShipping
                ->where('status', '=', ShippingStatus::SHIPPING_SHOW)
                ->select();

            $this->assign('ships', $query);

            /** 处理商品，显示总额 */
            $modelGoodCat = new GoodCat();

            $cat = explode(',', $data['cat']);
            $num = explode(',', $data['num']);

            $goods = $modelGoodCat
                ->with([
                    'Good' => function($q){return $q->where('status', '=',GoodStatus::GOOD_IN_STOCK);}
                ])
                ->where([
                    'id' => $cat
                ])
                ->select();

            $order = array_combine($cat, $num);
            $total = 0;
            foreach ($goods as $c => $d) {
                if(isset($d['good'])){
                    $goods[$c]['num'] = $order[$d['id']];
                    $total += $goods[$c]['price'] * $goods[$c]['num'];
                } else
                    unset($goods[$c]);
            }

            $this->assign('goods', $goods);
            $this->assign('total', $total);


            $this->assign('page_title', '结算');
            return $this->fetch();
        }

        $this->redirect('/');
    }


}