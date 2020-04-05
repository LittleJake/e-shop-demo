<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 12:57
 */

namespace app\index\controller;


use app\common\library\Enumcode\UserStatus;
use app\common\library\GithubOAuth;
use app\common\model\Account;
use app\common\model\Balance;
use PHPMailer\PHPMailer\PHPMailer;
use think\Exception;
use think\exception\HttpException;
use \think\facade\Cache;
use think\facade\Config;
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

            if($query['status'] != UserStatus::USER_ACTIVE)
                $this->error('账户被禁用，联系管理员');

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

            $validator = validate('User');
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
                $this->error('注册失败');
            }

            $this->success('注册成功', url('index/login/login'), 1, 3);

        }

        $this->assign('page_title', '注册');
        return $this->fetch();
    }

    public function forgetAction(){
        if ($this->request->isPost()){
            $data = $this->request->post('a');
            $valid = validate('Mail');

            $user = model('Account');

            if( !$valid-> check($data))
                $this->error($valid->getError());

            if(!empty($query =  $user->where('email',$data['email'])->find())){
                $token = random_str(32);
                try{
                    $mail = new PHPMailer(true);
                    $mail->CharSet ="UTF-8";
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = Config::get('mail.smtp_host');
                    $mail->SMTPAuth = true;
                    $mail->Username = Config::get('mail.username');
                    $mail->Password = Config::get('mail.password');
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port = 465;

                    $mail->setFrom(Config::get('mail.username'));
                    $mail->addAddress($data['email']);
                    //Content
                    $mail->isHTML(true);
                    $mail->Subject = '请求重置密码';
                    $mail->Body    = '<h1>请求重置密码</h1>'.
                        '<p><a href="'.url('index/login/reset', ['token' => $token]).'">点此重置密码</a></p>'
                        .'<p>连接有效期一小时</p>'
                        . '<p>'.date('Y-m-d H:i:s').'</p>';
                    $mail->AltBody = '复制'.url('index/login/reset', ['token' => $token]).'到浏览器重置密码';

                    $mail->send();
                    Cache::set('reset:'.$token,$query['id'],60*60);
                } catch (\Exception $e){
                    Log::error($e->getMessage());
                }
            }
            else
                sleep(random_int(4,6));

            $this->success('如果电子邮箱正确，你会收到一封重置密码链接的邮件', url('index/login/login'));
        }
        $this->assign('page_title', '忘记密码');
        return $this -> fetch();
    }

    public function resetAction($token = ''){
        if(!Cache::has("reset:".$token))
            throw new HttpException(404);

        if ($this->request->isPost()){
            $user_id = Cache::get("reset:".$token);

            $data = $this->request->post('a');
            $valid = validate('User');
            if(!$valid->scene('reset')->check($data))
                $this->error($valid->getError());

            Cache::rm("reset:".$token);

            $user = model('Account');

            $user -> update(['password' => secret($data['password'])], ['id' => $user_id]);

            $this->success('修改成功，请重新登录', url('index/login/login'));
        }

        $this->assign('page_title', '重置密码');
        return $this -> fetch();
    }

    public function GithubAction(){
        $query = [
            'client_id'=>config('github.client_id'),
            'scope' => config('github.scope'),
        ];

        $this->redirect(config('github.github_auth_url').'?'.http_build_query($query));
    }

    public function OAuthCallbackAction(){
        if(!input('?code'))
            $this->error('非法操作');

        $code = input('code');
        $result = GithubOAuth::getInfo($code);
        if(empty($result)){
            $this->error('OAuth API主机连接失败');
        }

        $data = ['email' => $result['id'].'@github.com',];

        $modelAccount = new Account();

        $query = $modelAccount -> where($data)->with('Balance')-> find();

        if(empty($query)){
            $data = [
                'email' => $result['id'].'@github.com',
                'password' => secret($result['node_id'].random_int(0,65535)),
                'username' => $result['login'],
                'mobile' => random_str(20,'int')
            ];

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
        }

        $query = $modelAccount -> where($data)->with('Balance')-> find();
        if(!empty($query)){
            if($query['status'] != UserStatus::USER_ACTIVE)
                $this->error('账户被禁用，联系管理员');

            cookie('email', $query['email']);
            session('user', $query['username']);
            session('user_id', $query['id']);
            cache('balance:'.$query['id'], $query->balance->money*100);
            $this->success($query['username'] . '，欢迎回来', "/");
        }

        $this->error('非法操作');
    }
}