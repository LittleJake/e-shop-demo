<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/16
 * Time: 0:54
 */

namespace app\index\validate;


use think\Validate;

class Address extends Validate
{
    protected $rule = [
        'name' => 'require',
        'street' => 'require',
        'district' => 'require',
        'city' => 'require',
        'province' => 'require',
        'code' => 'require|number|length:6',
        'contact' => 'require|number',
    ];

    protected $message  =   [
        'name.require' => '请填写收件人姓名',
        'street.require' => '请填写详细地址',
        'district.require' => '请填写行政区',
        'city.require' => '请填写城市',
        'province.require' => '请填写省份',
        'code.require' => '请输入邮编',
        'code.number' => '请输入正确的邮编',
        'code.length' =>'邮编为六位数字',
        'contact.require' => '请填写手机号',
        'contact.number' => '请填写正确的手机号',
    ];
}