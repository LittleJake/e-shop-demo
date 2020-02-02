<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2020/2/2
 * Time: 17:33
 */

namespace app\common\library;

class Kuai100
{
    private static $cookie = '';
    private static $auto_url = 'https://www.kuaidi100.com/autonumber/autoComNum';
    private static $check_url = 'https://www.kuaidi100.com/query';

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
        if(!empty(self::$cookie))
            curl_setopt($ch, CURLOPT_COOKIE, self::$cookie);//设置cookie

        $output = curl_exec($ch); //执行并获取HTML文档内容
        curl_close($ch); //释放curl句柄
        return $output;
    }

    private static function curlRaw($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不从证书中检查SSL加密算法是否存在
        curl_setopt( $ch, CURLOPT_HEADER, true);
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );

        curl_setopt( $ch , CURLOPT_URL , $url );

        $response = curl_exec( $ch );
        if ($response === FALSE) {
            return false;
        }

        curl_close( $ch );
        return $response;

    }


    private static function getCookie(){
        $re = self::curlRaw("https://www.kuaidi100.com/");
        // 解析HTTP数据流
        list($header, $body) = explode("\r\n\r\n", $re);
        // 解析COOKIE
        preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);
        //请求的时候headers 带上cookie就可以了
        $cookie = '';
        foreach ($matches[0] as $match) {
            $cookie .= explode(';', str_replace('Set-Cookie: ', '', $match))[0] . '; ';
        }
        self::$cookie = trim($cookie);
    }

    public static function getCom($track_no){
        return json_decode(self::curlData(self::$auto_url,['text'=>$track_no,'resultv2' => 1]), true)['auto'][0]['comCode'];
    }

    public static function getAutoData($track_no){
        self::getCookie();
        $track = json_decode(self::curlData(self::$auto_url,['text'=>$track_no,'resultv2' => 1]), true);

        try{
            $trackName = $track['auto'][0]['comCode'];
        } catch (\Exception $e){
            return ['code' => 0, 'msg'=> '获取不到默认快递公司'];
        }

        $data = json_decode(self::curlData(self::$check_url,['postid'=>$track_no,'type' => $trackName, 'phone'=> '', ]), true);
        return ['code' => 1, 'msg'=> '成功', 'data' => ['type' => $trackName, 'track'=> $data['data']]];
    }

    public static function getImg($type){
        return "https://cdn.kuaidi100.com/images/all/56/$type.png";
    }
}