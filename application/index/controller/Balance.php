<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/20
 * Time: 14:18
 */

namespace app\index\controller;

use app\common\library\Hkrt;
use think\facade\Cache;

class Balance extends Base
{

    public function indexAction(){

        $balance = model('BalanceChange');

        $change = $balance-> where([
            'user_id' => $this->userid()
        ])-> order('update_time','desc')->paginate(PAGE);

        $this->assign('change', $change);

        $this->assign('page_title', '余额明细');
        return $this->fetch();
    }

    public function chargeAction(){
        if($this->request->isPost()){
            $pay = new Hkrt();
            $amount = input('post.amount');
            if(!(is_int($amount) || is_double($amount)) || $amount > 1000 || $amount < 0)
                $this->error('金额必须大于0，小于1000');

            $qr = $pay->createUnionQR($amount);

            if($qr['code'] != 1)
                return false;


            $qr['qrcode'] = "https://tool.oschina.net/action/qrcode/generate?output=image%2Fpng&error=L&type=0&margin=0&size=4&data=". urlencode($qr['payUrl']);

            $this->assign('qr', $qr);
            return $this->fetch('balance/checkout');
        }

        $this->assign('page_title', '余额充值');
        return $this->fetch();
    }

    public function BalanceChange($amount = 0, $dec = true){
        $modelBalanceChange = model('BalanceChange');

        if ($dec){
            if(Cache::dec('balance:'.session('user_id'), $amount*100) > 0){
                $modelBalanceChange->where(['id' => session('user_id')]) -> insert([
                    'user_id' => session('user_id'),
                    'change' => -$amount,
                    'update_time' => time()
                ]);
                return true;
            }
            Cache::inc('balance:'.session('user_id'), $amount*100);
            return false;
        }else{
            if(Cache::inc('balance:'.session('user_id'), $amount*100)){
                $modelBalanceChange->where(['id' => session('user_id')]) -> insert([
                    'user_id' => session('user_id'),
                    'change' => $amount,
                    'update_time' => time()
                ]);
                return true;
            }
        }
        return false;
    }
}