<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/5/2019
 * Time: 10:37 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\admin\validate;


use app\common\validate\Common;

class AdminAccount extends Common
{
    protected $rule = [
        'username' => 'require|length:5,20',
        'password' => 'require|length:1,32',
        'vercode' => 'require|length:4|captcha'
    ];

    protected $message = [
        'username.require' => '请输入用户名',
        'password.require' => '请输入密码',
        'vercode.require' => '请输入验证码',
        'username.length' => '用户名长度为5-20',
        'password.length' => '密码长度为1-32',
        'vercode.length' => '验证码长度不正确',
        'vercode.captcha' => '验证码不正确',
    ];
}