<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 1:20
 */

namespace app\index\validate;


use think\Validate;

class User extends Validate
{
    protected $rule = [
        'email' => 'require|email',
        'user_name' => 'require',
        'password' => 'require',
        'mobile' => 'require|number',
    ];

    protected $message = [
        'email.require' => '请输入邮箱',
        'email.email' => '邮箱格式不正确',
        'user_name.require' => '请输入用户名',
        'password.require' => '请输入密码',
        'mobile.require' => '请输入手机号',
        'mobile.number' => '请输入正确的手机号',
    ];

    protected $scene = [
        'reg' => ['email', 'user_name', 'password', 'mobile'],
        'login' => ['email', 'password'],
    ];

}