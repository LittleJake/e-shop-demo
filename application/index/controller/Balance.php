<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/20
 * Time: 14:18
 */

namespace app\index\controller;

use app\common\library\Hkrt;
use app\common\model\BalanceChange;
use think\facade\Cache;
use app\common\model\Account;

class Balance extends Base
{

    public function indexAction(){

        $modelAccount = new Account();
        $modelAccount -> where([
            'id' => session('user_id')
        ])->with('BalanceChange')->find();

        $change = $modelAccount->BalanceChange()-> order('update_time','desc')->paginate(10);

        $this->assign('change', $change);

        $this->assign('page_title', '余额明细');
        return $this->fetch();
    }

    public function chargeAction(){
        if($this->request->isPost()){
            $pay = new Hkrt();
            $qr = $pay->createUnionQR(input('post.amount'));

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
        $modelBalanceChange = new BalanceChange();

        if ($dec){
            if(Cache::dec('balance:'.session('user_id'), $amount*100) > 0){
                $modelBalanceChange->where(['id' => session('user_id')]) -> insert([
                    'user_id' => session('user_id'),
                    'charge' => -$amount,
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
                    'charge' => $amount,
                    'update_time' => time()
                ]);
                return true;
            }
        }
        return false;
    }
}