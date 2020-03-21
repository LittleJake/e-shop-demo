<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 0:54
 */

namespace app\index\validate;


use app\common\validate\Common;

class Address extends Common
{
    protected $rule = [
        'name' => 'require|max:20|regex:/^[ a-zA-Z\x{4e00}-\x{9fa5}]+$/u',
        'street' => 'require|max:100|regex:/^[,\- a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u',
        'district' => 'require|max:10|regex:/^[ a-zA-Z\x{4e00}-\x{9fa5}]+$/u',
        'city' => 'require|max:10|regex:/^[ a-zA-Z\x{4e00}-\x{9fa5}]+$/u',
        'province' => 'require|max:10|regex:/^[ a-zA-Z\x{4e00}-\x{9fa5}]+$/u',
        'code' => 'require|number|regex:/^\d{6}$/',
        'contact' => 'require|number|max:11',
    ];

    protected $message  =   [
        'name.require' => '请填写收件人姓名',
        'name.regex' => '请填写姓名（不含数字、符号）',
        'name.max' => '姓名长度不超过20',
        'street.require' => '请填写详细地址',
        'street.regex' => '请填写详细地址（不含‘-’、‘,’以外的符号）',
        'street.max' => '详细地址长度不超过100',
        'district.require' => '请填写行政区',
        'district.max' => '行政区长度不超过10',
        'district.regex' => '请填写行政区（不含数字、符号）',
        'city.require' => '请填写城市',
        'city.max' => '城市长度不超过10',
        'city.regex' => '请填写城市（不含数字、符号）',
        'province.require' => '请填写省份',
        'province.max' => '省份长度不超过10',
        'province.regex' => '请填写省份（不含数字、符号）',
        'code.require' => '请输入邮编',
        'code.number' => '请输入正确的邮编',
        'code.regex' =>'邮编为六位数字',
        'contact.require' => '请填写联系电话',
        'contact.number' => '请填写正确的联系电话',
    ];
}