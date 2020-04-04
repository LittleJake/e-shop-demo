<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/4/5
 * Time: 0:53
 */

namespace app\index\validate;


use app\common\validate\Common;

class Comment extends Common
{
    protected $rule = [
        'comment' => 'require|max:100',
        'captcha' => 'require|length:4|captcha',
    ];

    protected $message  =   [
        'comment.require' => '请输入评论',
        'comment.max' => '评论最多100字',
        'captcha.length' => '验证码长度不正确',
        'captcha.captcha' => '验证码不正确',
        'captcha.require' => '请输入验证码',
    ];

}