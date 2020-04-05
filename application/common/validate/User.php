<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 1:20
 */

namespace app\common\validate;

class User extends Common
{
    protected $rule = [
        'email' => 'require|email',
        'username' => 'require|regex:[0-9a-zA-Z]*|max:20|unique:Account,username',
        'password' => 'require|max:20',
        'mobile' => 'require|regex:1[3-9]{1}[0-9]{9}|unique:Account,mobile',
        'repassword' => 'require|confirm:password',
        'old_password' => 'require|max:20',
    ];

    protected $message = [
        'email.require' => '请输入邮箱',
        'email.email' => '邮箱格式不正确',
        'username.require' => '请输入用户名',
        'username.max' => '用户名长度小于20',
        'password.max' => '密码长度小于20',
        'old_password.max' => '原密码长度小于20',
        'username.regex' => '请输入用户名（数字及字母）',
        'repassword.require' => '请重复输入密码',
        'repassword.confirm' => '请重复输入相同的密码',
        'password.require' => '请输入密码',
        'mobile.require' => '请输入手机号',
        'mobile.regex' => '请输入正确的手机号',
        'mobile.unique' => '手机号已存在',
        'email.unique' => '邮箱已存在',
        'username.unique' => '用户名已存在',
        'mobile.number' => '请输入正确的手机号',
    ];

    protected $scene = [
        'reg' => ['email', 'username', 'password', 'mobile', 'repassword'],
        'login' => ['email', 'password'],
        'change' => ['password', 'repassword', 'old_password'],
        'reset' => ['password', 'repassword'],
    ];

}