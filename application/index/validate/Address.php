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
        'name' => 'require|max:20',
        'street' => 'require|max:100',
        'district' => 'require|max:10',
        'city' => 'require|max:10',
        'province' => 'require|max:10',
        'code' => 'require|number|length:6',
        'contact' => 'require|number|max:11',
    ];

    protected $message  =   [
        'name.require' => '请填写收件人姓名',
        'name.max' => '姓名长度不超过20',
        'street.require' => '请填写详细地址',
        'street.max' => '详细地址长度不超过100',
        'district.require' => '请填写行政区',
        'district.max' => '行政区长度不超过10',
        'city.require' => '请填写城市',
        'city.max' => '城市长度不超过10',
        'province.require' => '请填写省份',
        'province.max' => '省份长度不超过10',
        'code.require' => '请输入邮编',
        'code.number' => '请输入正确的邮编',
        'code.length' =>'邮编为六位数字',
        'contact.require' => '请填写联系电话',
        'contact.number' => '请填写正确的联系电话',
    ];
}