<?php
namespace app\index\controller;

class Index extends Base
{
    public function indexAction()
    {
        return secret(123);
    }

    //店铺商品
    public function shopInfoAction(){
        return $this->fetch();
    }

    //店铺列表
    public function shopListAction(){}

    //下单
    public function orderAction(){}
    //商品详情
    public function goodAction(){}

    public function helloAction($name = 'ThinkPHP5')
    {
        session('user', '123');
        return session('user');
        return 'hello,' . $name;
    }
}
