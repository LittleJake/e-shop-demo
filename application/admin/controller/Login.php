<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/4/2019
 * Time: 8:45 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\admin\controller;


class Login extends Common
{
    public function loginAction(){
        if($this->request->isAjax()) {
            $data = [
                'username' => input('get.username'),
                'password' => input('get.password'),
                'vercode' => input('get.vercode')
            ];

            $validate = validate('AdminAccount');

            if(!$validate->check($data))
                return json([
                    'code' => 0,
                    'msg' => $validate->getError()
                ]);




            return json(['code'=> 1, 'msg'=>'登陆成功']);
        }

        return $this -> fetch();
    }

    public function forgetAction(){

        return $this -> fetch();
    }

    public function vercodeAction(){

        return $this -> fetch();
    }
}