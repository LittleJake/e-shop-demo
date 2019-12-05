<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 12:57
 */

namespace app\index\controller;


class Login extends Common
{
    //登录
    public function loginAction(){
        if($this->isLogin()){
            $url = urldecode($this->request->param('r'));
            return $this->redirect(empty($url)?"/":$url);
        }


        if($this->request->isPost()){
            $data = input('post.a');

            $validator = validate('user');

            if(!$validator -> scene('login') -> check($data))
                return $this->error($validator->getError());

            $data['password'] = secret($data['password']);

            $query = db('user') -> where($data)-> find();

            if(!isset($query))
                return $this->error('用户名或密码错误');

            session('user', $query['user_name']);
            session('user_id', $query['user_id']);
            $url = urldecode($this->request->param('r'));
            return $this->success($query['user_name'] . '，欢迎回来', empty($url)?"/":$url);
        }

        $this->assign('page_title', '登录');
        return $this->fetch();
    }
    //登出
    public function logoutAction(){
        session(null);
        $url = urldecode($this->request->param('r'));

        return $this->success('登出成功', empty($url)?"/":$url);
    }

    //注册
    public function regAction(){
        if($this->isLogin()){
            $url = urldecode($this->request->param('r'));
            return $this->redirect(empty($url)?"/":$url);
        }

        if($this->request->isPost()){
            $data = input('post.a');

            $validator = validate('user');

            if(!$validator->scene('reg')-> check($data))
                return $this->error($validator->getError());

            $data['password'] = secret($data['password']);

            Db::startTrans();
            try{
                Db::table('user') -> insert($data);
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                return $this->error('注册失败');
            }

            return $this->success('注册成功', url('index/user/login'));

        }

        $this->assign('page_title', '注册');
        return $this->fetch();
    }

    public function vercodeAction(){

        return $this -> fetch();
    }

    public function OAuthCallbackAction(){
        return $this -> fetch();
    }
}