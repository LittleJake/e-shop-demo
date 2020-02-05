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
use think\Db;

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
                Db::table('cart')
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
                        return $q->withField('id,title,img_url');
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
                            return $query->field('id,title,img_url');
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
    public function addressAction(){
        $modelAddress = new Address();

        if($this->request->isAjax()) {
            if(!$this->isLogin())
                return null;
            $user = session('user_id');

            $id = (int)input('id');

            $query = $modelAddress -> where([
                'user_id' => $user,
                'id' => $id,
                'status' => 1
            ]) -> find();

            $this->assign('address', $query);
            return $this->fetch('index/user/ajaxAddress');
        }

        $user = session('user_id');

        if(input('?del'))
        {
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
                return $this->error($e->getMessage()."错误",url('index/user/address'));
            }


            return $this->success("成功",'index/user/address');
        }

        $query = $modelAddress -> where([
            'user_id' => $user,
            'status' => 1
        ]) -> select();

        $this->assign('address', $query);
        $this->assign('page_title', '地址列表');
        return $this->fetch();
    }
    //增加地址
    public function addAddressAction(){
        if($this->request->isPost()){
            $modelAddress = new Address();

            $data = input('post.a');

            $validator = validate('address');

            if(!$validator->check($data))
                return $this->error($validator->getError());

            $data['user_id'] = session('user_id');

            $modelAddress -> insert($data);

            return $this->success('添加成功', url('index/user/address'));

        }



        $this->assign('page_title', '添加地址');
        return $this->fetch();
    }

    //商品评价
    public function rateAction(){
        $id = input('order', '');

        if($this->request->isPost()){
            $data = $this->request->post();

            $ids = array_keys($data['rate']);

            $rate = model('Rate');
            $order = model('Order');
            $time = time();

            Db::startTrans();
            try{
                $user = session('user_id');
                $ins = [];

                foreach ($ids as $k){
                    $ins[] = [
                        'good_id' => $k,
                        'star' => $data['rate'][$k],
                        'comment' => htmlentities($data['content'][$k]),
                        'user_id' => $user,
                        'update_time' => $time
                    ];
                }

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
                return $this->error('错误', url('index/user/order'));
            }
            return $this->success('成功', url('index/user/order'));
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

    public function payAction(){
        return $this->fetch();
    }

}