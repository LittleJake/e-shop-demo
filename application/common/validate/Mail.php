<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/4/5
 * Time: 16:38
 */

namespace app\common\validate;


class Mail extends Common
{
    protected $rule = [
        'email' => 'require|email',
        'captcha' => 'require|captcha',
    ];

    protected $message = [
        'email.require' => '请输入电子邮件',
        'email.email' => '请输入正确的电子邮件',
        'captcha.captcha' => '请输入正确的验证码',
        'captcha.require' => '请输入验证码',
    ];
}