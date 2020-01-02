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


class GithubOAuth
{
    private static function curlData($url,$data=null,$method = 'GET',$type='json', $header = [])
    {
        //初始化
        $ch = curl_init();
        $headers = [
            'form-data' => ['Content-Type: multipart/form-data', 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36'],
            'json'      => ['Content-Type: application/json', 'Accept: application/json', 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36'],
        ];
        if($method == 'GET'){
            if($data){
                $querystring = http_build_query($data);
                $url = $url.'?'.$querystring;
            }
        }
        // 请求头，可以传数组
        if(sizeof($header)>0)
            $headers[$type] = array_merge($headers[$type], $header);

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers[$type]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);         // 执行后不直接打印出来
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');     // 请求方式
            curl_setopt($ch, CURLOPT_POST, true);               // post提交
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);              // post的变量
        }
        if($method == 'PUT'){
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        if($method == 'DELETE'){
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不从证书中检查SSL加密算法是否存在
        $output = curl_exec($ch); //执行并获取HTML文档内容
        curl_close($ch); //释放curl句柄
        return $output;
    }

    public static function getInfo($code = ''){
        $config = [
            'client_id' => config('github.client_id'),
            'client_secret' => config('github.client_secret'),
            'code' => $code
        ];

        $token_url = config('github.github_token_url');
        $info_url = config('github.github_info_url');

        $github = json_decode(self::curlData($token_url, json_encode($config), 'POST'), true);

        $header = [
            'Authorization: token '.$github['access_token']
        ];

        $result = json_decode(self::curlData($info_url, '', 'GET','json', $header),true);

        return $result;
    }
//var_dump($_GET['code']);
//
//$client_id = 'e5cfbd9fd8aeecca9d69';
//$secret = '59bd2d0bba3955fea63b68f9b0f1a12d5c47ea19';
//$code = $_GET['code'];
//$config = [
//'client_id' => $client_id,
//'client_secret' => $secret,
//'code' => $code
//];
//
//$github = json_decode(curlData('https://github.com/login/oauth/access_token', json_encode($config), 'POST'),true);
//
//var_dump($github);
//$header = [
//'Authorization: token '.$github['access_token']
//];
//$result = curlData('https://api.github.com/user', '', 'GET','json', $header);
//var_dump($result);
}