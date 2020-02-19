<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 1:20
 */

namespace app\index\validate;

use app\common\validate\Common;

class User extends Common
{
    protected $rule = [
        'email' => 'require|email',
        'username' => 'require',
        'password' => 'require',
        'mobile' => 'require|number|unique:Account,mobile',
        'repassword' => 'require|confirm:password',
        'old_password' => 'require',
    ];

    protected $message = [
        'email.require' => '请输入邮箱',
        'email.email' => '邮箱格式不正确',
        'username.require' => '请输入用户名',
        'repassword.require' => '请重复输入密码',
        'repassword.confirm' => '请重复输入相同的密码',
        'password.require' => '请输入密码',
        'mobile.require' => '请输入手机号',
        'mobile.unique' => '手机号已存在',
        'mobile.number' => '请输入正确的手机号',
    ];

    protected $scene = [
        'reg' => ['email', 'username', 'password', 'mobile', 'repassword'],
        'login' => ['email', 'password'],
        'change' => ['password', 'repassword', 'old_password']
    ];

}