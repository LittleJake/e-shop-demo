<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 12:57
 */

namespace app\index\controller;


use app\common\library\GithubOAuth;
use app\common\model\Account;
use app\common\model\Balance;
use think\Config;
use think\facade\Log;


class Login extends Common
{
    //登录
    public function loginAction(){
        if($this->isLogin()){
            $url = urldecode($this->request->param('r'));
            $this->redirect(empty($url)?"/":$url);
        }


        if($this->request->isPost()){
            $data = input('post.a');

            $validator = validate('user');

            if(!$validator -> scene('login') -> check($data))
                $this->error($validator->getError());

            $modelAccount = new Account();
            $query = $modelAccount -> where([
                'email' => $data['email']
            ])->with('Balance')-> find();

            if(!isset($query))
                $this->error('邮箱不存在');

            if(!check_secret($data['password'], $query['password']))
                $this->error('密码错误');

            cookie('email', $query['email']);
            session('user', $query['username']);
            session('user_id', $query['id']);
            cache('balance:'.$query['id'], $query->balance->money*100);
            $url = urldecode($this->request->param('r'));

            $this->success($query['username'] . '，欢迎回来', empty($url)?"/":$url);
        }

        $this->assign('email', cookie('email'));
        $this->assign('page_title', '登录');
        return $this->fetch();
    }
    //登出
    public function logoutAction(){
        session(null);
        $url = urldecode($this->request->param('r'));

        $this->success('登出成功', empty($url)?"/":$url);
    }

    //注册
    public function regAction(){
        if($this->isLogin()){
            $url = urldecode($this->request->param('r'));
            $this->redirect(empty($url)?"/":$url);
        }

        if($this->request->isPost()){
            $data = input('post.a');

            $validator = validate('user');

            if(!$validator->scene('reg')-> check($data))
                $this->error($validator->getError());
            unset($data['repassword']);
            $data['password'] = secret($data['password']);

            $modelAccount = new Account();
            $modelAccount -> startTrans();
            $modelBalance = new Balance();
            try{
                $modelAccount -> insert($data);
                $id = $modelAccount ->getLastInsID();
                $modelAccount -> commit();
                $modelBalance->save([
                    'user_id' => $id,
                    'update_time' => time(),
                    'money' => 0
                ]);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                $modelAccount -> rollback();
                $this->error('注册失败，事务处理失败');
            }

            $this->success('注册成功', url('index/login/login'), 1, 3);

        }

        $this->assign('page_title', '注册');
        return $this->fetch();
    }

    public function vercodeAction(){

        return $this -> fetch();
    }

    public function GithubAction(){
        var_dump(config('github.client_id'));

        $query = [
            'client_id'=>config('github.client_id'),
            'scope' => config('github.scope'),
        ];

        $this->redirect(config('github.github_auth_url').'?'.http_build_query($query));
    }

    public function OAuthCallbackAction(){

        if(!input('?code')){
            $this->error('非法操作');
        }
        $code = input('code');
        $result = GithubOAuth::getInfo($code);

        if(empty($result)){
            $this->error('非法操作');
        }

        $data = [
            'email' => $result['id'].'@github',
        ];

        $modelAccount = new Account();

        $query = $modelAccount -> where($data)->with('Balance')-> find();
        if(!empty($query)){
            cookie('email', $query['email']);
            session('user', $query['username']);
            session('user_id', $query['id']);
            cache('balance:'.$query['id'], $query->balance->money*100);
            $this->success($query['username'] . '，欢迎回来', "/");
        }

        $data = [
            'email' => $result['id'].'@github',
            'password' => secret($result['node_id'].random_int(0,65535)),
            'username' => $result['login'],
            'mobile' => random_str(20,'int')
        ];

        $modelAccount = new Account();
        $modelAccount -> startTrans();
        $modelBalance = new Balance();
        try{
            $modelAccount -> insert($data);
            $id = $modelAccount ->getLastInsID();
            $modelAccount -> commit();
            $modelBalance->save([
                'user_id' => $id,
                'update_time' => time(),
                'money' => 0
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $modelAccount -> rollback();
            $this->error('注册失败，事务处理失败');
        }

        $modelAccount = new Account();

        $query = $modelAccount -> where($data)->with('Balance')-> find();
        if(!empty($query)){
            cookie('email', $query['email']);
            session('user', $query['username']);
            session('user_id', $query['id']);
            cache('balance:'.$query['id'], $query->balance->money*100);
            $this->success($query['username'] . '，欢迎注册', "/");
        }

        $this->error('非法操作');
    }
}