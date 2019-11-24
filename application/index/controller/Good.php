<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 0:22
 */

namespace app\index\controller;

use think\Db;

class Good extends Common
{
    //下单
    public function orderAction(){
        if(!$this->isLogin())
            return $this->redirect('user/login', ['r' => urlencode($this->request->url())]);

        $data = input('post.a');
        $orderValidate = validate('order');
        if(!$orderValidate->check($data))
            return $this->error($orderValidate->getError());


        $user = session('user_id');
        $time = time();
        $date = date('YmdHis');

        //订单号生成

        $order_no =  $date. substr('0000' . rand(0, 9999),-4,4);


        Db::startTrans();
        try{
            Db::table('order')
                -> insert([
                    'order_no' => $order_no,
                    'address_id' => $data["add_id"],
                    'payment_id' => $data["pay"],
                    'total_price' => $data["total"],
                    'shipping_id' => $data["ship"],
                    'time' => $time,
                    'status' => 0,
                    'user_id' =>$user
                ]);
            $order_id=Db::getLastInsID();
            Db::commit();
        }
        catch (\Exception $e) {
            Db::rollback();
            $this->error('错误', url('/'));
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
    //商品详情
    public function goodAction($id = ''){
        $comment = Db::table('comment')
            -> where('good_id', $id)
            -> paginate(10);
        $page = $comment ->render();
        $total = $comment->total();

        $this->assign('comments', ($total == 0) ?null:$comment);
        $this->assign('page', $page);

        if($this->request->isAjax())
            return $this->fetch('index/ajaxComment');

        $good = Db::table('good')
            ->where('good_id', $id)
            ->find();

        $price = Db::query("select p from `good` as G left join (SELECT good_id, min(price) as p from `category` group by good_id) as C on G.good_id = C.good_id where G.good_id = $id")[0]['p'];

        $cat = Db::table('category')
            ->where('good_id', $id)
            ->select();

        if(!isset($cat) ||!isset($price)||!isset($good))
            return $this->error('参数错误');

        $this->assign('comment_total', $total);
        $this->assign('cat', $cat);
        $this->assign('price', $price);
        $this->assign('good', $good);
        $this->assign('page_title', $good['title']);
        return $this->fetch();
    }

    public function checkoutAction()
    {
        if(!$this->isLogin())
            return $this->redirect('user/login', ['r' =>  urlencode($this->request->url(true))]);

        if(!input('?cat') || !input('?num'))
            return $this->redirect('/');

        $cat = input('cat');
        $num = input('num');
        $this->assign('cat', $cat);
        $this->assign('num', $num);

        $user = session('user_id');

        $query = Db::query("select * from `address` where user_id = $user");

        $this->assign('address', $query);

        $query = Db::query("select * from `shipping_fee`");

        $this->assign('ships', $query);

        $goods = Db::query("select * from `category` left join `good` on category.good_id = good.good_id where category.cat_id in ($cat)");

        $cat = explode(',', $cat);
        $num = explode(',', $num);

        $order = array_combine($cat, $num);
        $total = 0;
        foreach ($goods as $c => $d) {
            foreach ($order as $a => $b) {
                if($d['cat_id'] == $a){
                    $goods[$c]['num'] = $b;
                    $total += $goods[$c]['price'] * $b;
                    break;
                }
            }
        }

        $this->assign('goods', $goods);
        $this->assign('total', $total);

        $query = Db::query("select * from `payment`");

        $this->assign('payments', $query);


        $this->assign('page_title', '结算');
        return $this->fetch();
    }


}