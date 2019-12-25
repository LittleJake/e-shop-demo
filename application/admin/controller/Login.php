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


use app\admin\model\AdminAccount;

class Login extends Common
{
    public function loginAction(){
        if($this->isLogin())
            return $this->redirect('admin/index/index');

        if($this->request->isAjax()) {
            $data = input('a');

            $validate = validate('AdminAccount');

            if(!$validate->check($data))
                return json([
                    'code' => 3,
                    'msg' => $validate->getError()
                ]);

            $modelAdminAccount = new AdminAccount();
            $query = $modelAdminAccount->where([
                'username' => $data['username']
            ]) -> find();

            if(empty($query))
                return json(['code' => 1, 'msg'=>'用户不存在']);

            if(check_secret($query['password'], $data['password']))
                return json(['code' => 2, 'msg'=>'密码错误']);

            session('admin_user_id', $query['id']);
            session('admin_user_name', $query['username']);

            return json(['code'=> 0, 'msg'=>'登陆成功']);
        }

        return $this -> fetch();
    }

    public function forgetAction(){

        return $this -> fetch();
    }

    public function vercodeAction(){

        return $this -> fetch();
    }

    public function logoutAction(){

        session('admin_user_name',null);
        session('admin_user_id',null);

        return json(['code' => 0, 'msg'=>'登出成功']);
    }
}