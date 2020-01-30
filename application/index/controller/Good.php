<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 0:22
 */

namespace app\index\controller;

use app\common\model\Address;
use app\common\model\GoodCat;
use app\common\model\OrderGoods;
use app\common\model\Shipping;
use app\index\validate\Order;
use think\Db;

class Good extends Common
{
    //下单
    public function orderAction(){
        if(!$this->isLogin())
            return $this->redirect('index/login/login', ['r' => urlencode($this->request->url())]);

        if($this->request->isPost()){
            $data = $this->request->post('a');
            $orderValidate = validate('Order');
            if(!$orderValidate->check($data))
                return $this->error($orderValidate->getError());

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
                var_dump($total);exit;

                $modelOrder
                    -> insert([
                        'order_no' => $order_no,
                        'address_id' => $data["add_id"],
                        'payment_id' => $data["pay"],
                        'total_price' => $total,
                        'shipping_id' => $data["ship"],
                        'time' => $time,
                        'status' => 0,
                        'user_id' =>$user
                    ]);
                $order_id=Db::getLastInsID();

                for ($i = 0; $i< count($ins); $i++){
                    $ins[$i]['order_id'] = $order_id;
                }

                $modelOrderGoods ->insertAll($ins);
                Db::commit();
            }
            catch (\Exception $e) {
                Db::rollback();
                $this->error($e->getMessage(), url('/'));
            }



            $goods = Db::query("select * from `category` left join `good` on category.good_id = good.good_id where category.cat_id in (${data['cat']})");

            $cat = explode(',', $data['cat']);
            $num = explode(',', $data['num']);

            $order = array_combine($cat, $num);
            $no_goods = array();

            foreach ($goods as $c => $d) {
                foreach ($order as $a => $b) {
                    if($d['cat_id'] == $a){
                        if($goods[$c]['sku'] < $b) {
                            //库存不足时
                            $no_goods[] = array_pop($goods[$c]);
                            break;
                        }

                        Db::startTrans();
                        try {
                            //订单物品添加
                            Db::table('order_good')
                                -> insert([
                                    'order_id' => $order_id,
                                    'cat_id' => $a,
                                    'num' => $b
                                ]);
                            //减少库存
                            Db::table('category')
                                -> where('cat_id', $a)
                                ->update([
                                    'sku' => $goods[$c]['sku'] - (int)$b
                                ]);
                            //删除购物车
                            Db::table('cart')
                                -> where([
                                    'user_id' => $user,
                                    'cat_id' => $a
                                ])
                                ->delete();

                            Db::commit();

                        }
                        catch (\Exception $e) {
                            Db::rollback();
                        }
                        break;
                    }
                }
            }

            return $this->success('成功', url('index/user/order'));
        }

        return $this->redirect('/');
    }
    //商品详情
    public function goodAction($id = ''){
        $modelRate = model('Rate');
        $comment = $modelRate->where([
            'good_id' => $id
        ])->paginate(10);

        $page = $comment ->render();
        $total = $comment->total();

        $this->assign('comments', ($total == 0) ?null:$comment);
        $this->assign('page', $page);

        if($this->request->isAjax())
            return $this->fetch('index/ajaxComment');

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
            return $this->error('参数错误');

        $this->assign('comment_total', $total);
        $this->assign('cat', $good->good_cat);
        $this->assign('good', $good);
        $this->assign('page_title', $good['title']);
        return $this->fetch();
    }

    public function checkoutAction()
    {
        if(!$this->isLogin())
            return $this->redirect('index/login/login', ['r' =>  urlencode($this->request->url(true))]);

        if($this->request->isPost()){
            $data = $this->request->post();
            $validate = validate('Order');
            if(!$validate->scene('checkout')->check($data))
                return $this->error($validate->getError());

            $this->assign('data', $data);

            $user = session('user_id');

            $modelAddress = new Address();
            $query = $modelAddress -> where([
                'status' => 1,
                'user_id' => $user
            ])->select();

            $this->assign('address', $query);

            $modelShipping = new Shipping();
            $query = $modelShipping->select();

            $this->assign('ships', $query);

            /** 处理商品，显示总额 */
            $modelGoodCat = new GoodCat();

            $cat = explode(',', $data['cat']);
            $num = explode(',', $data['num']);

            $goods = $modelGoodCat
                ->with([
                    'Good' => function($q){return $q->where('status', '=',0);}
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

        return $this->redirect('/');
    }


}