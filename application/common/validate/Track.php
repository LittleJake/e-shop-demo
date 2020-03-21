<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 1:20
 */

namespace app\common\validate;

class Track extends Common
{
    protected $rule = [
        'track_no' => 'require|regex:[\d]{20}',
    ];

    protected $message = [
        'track_no.require' => '请输入20位溯源码',
        'track_no.regex' => '请输入正确的溯源码',
    ];

}