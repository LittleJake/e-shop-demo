<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/11/18
 * Time: 12:38
 */

namespace app\common\library\Enumcode;


class OrderStatus
{
    const ORDER_CLOSE = 0;
    const ORDER_PAID = 1;
    const ORDER_UNPAID = 2;
    const ORDER_SHIPPING = 3;
    const ORDER_PAY_AFTER_SHIPPING = 4;
    const ORDER_NEED_COMMENT = 5;
    const ORDER_FINISH = 6;
}