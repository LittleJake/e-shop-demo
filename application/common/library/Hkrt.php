<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 1/2/2020
 * Time: 6:06 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\common\library;

class Hkrt {
    private $version = '9.9.9';
    private $mobile = '15802033573';

    private $url = [
        'token' => 'https://posapp.icardpay.com/html5/mobile/merchant/user/createTokenAndValidateVersion',
        'login' => 'https://posapp.icardpay.com/html5/mobile/merchant/user/login',
        'getInfo' => 'https://posapp.icardpay.com/html5/mobile/merchant/user/operatorInfo',
        'check' => 'https://posapp.icardpay.com/html5/mobile/merchant/user/tranRecordDayDetail',
        'createQR' => 'https://posapp.icardpay.com/html5/mobile/merchant/pay/activePay',
    ];

    private $config = [
        'username' => '%2BxDgeqY4rhpsUeVP4C0nqw%3D%3D',
        'password' => 'RHCKllqb3250'
    ];

    private $header = [
        "User-Agent: okhttp/3.8.1"
    ];

    public function getToken(){
        $url = $this->url['token'].'?'. http_build_query(['version' => $this->version]);
        return $this -> posturl($url);
    }

    public function login(){
        $token = $this->getToken();
        if($token['code'] != 1)
            throw new \Exception($token['msg']);

        $userinfo = $this->getInfo($token['token']);
        if($userinfo['code'] != 1)
            throw new \Exception($userinfo['msg']);
        else if($userinfo['data']['phone'] != $this->mobile)
            throw new \Exception('个人信息不匹配');

        return $token['token'];
    }

    public function getInfo($token){
        $query = $this ->config;
        $query['token'] = $token;
        $query['version'] = '9.9.9';

        $url = $this->url['login'].'?'. http_build_query($query);

        return $this -> posturl($url);
    }

    public function checkOrder($token, $flowId){

    }


    public function createUnionQR($amount){
        try{
            $token = $this->login();

            $query = [
                'token' => $token,
                'amount' => $amount,
                'channelType' => 'UNION'
            ];

            $url = $this->url['createQR']. '?'.http_build_query($query);

            $rnt = $this-> posturl($url);

            return $rnt;
        } catch(\Exception $e){
            var_dump($e);
        }
        return false;
    }

    public function posturl($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, '');
        curl_setopt($curl,CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return json_decode($output,true);
    }

    public function geturl($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$this->header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
}



