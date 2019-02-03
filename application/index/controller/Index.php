<?php
namespace app\index\controller;

class Index extends Base
{
    public function indexAction()
    {
        return secret(123);
    }


    public function helloAction($name = 'ThinkPHP5')
    {
        session('user', '123');
        return session('user');
        return 'hello,' . $name;
    }
}
