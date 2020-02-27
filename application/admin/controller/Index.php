<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/1/31
 * Time: 22:14
 */

namespace app\admin\controller;


use app\common\library\Enumcode\ArticleStatus;
use app\common\library\Enumcode\GoodStatus;
use app\common\library\Enumcode\OrderStatus;
use app\common\model\Account;
use app\common\model\Image;
use think\facade\Log;

class Index extends Base
{
    /** 首页 */
    public function indexAction(){
        return $this->fetch();
    }

    public function consoleAction(){
        $account = model('Account');
        $good = model('Good');
        $order = model('Order');
        $article = model('Article');

        /** 右侧数量挂件 */
        $this->assign('goodCount', $good ->getGoodCount());
        $this->assign('userCount', $account ->getAccountCount());
        $this->assign('inStockCount', $good ->getGoodCount(['status' => GoodStatus::GOOD_IN_STOCK]));
        $this->assign('waitShippingCount', $order ->getOrderCount(['status' => OrderStatus::ORDER_PAID])+$order ->getOrderCount([['status','=',OrderStatus::ORDER_PAY_AFTER_SHIPPING],['track_no' ,'NULL','']]));
        $this->assign('articleCount', $article ->getArticleCount());

        return $this->fetch();
    }

    public function uploadAction(){
        // 获取表单上传文件 例如上传了001.jpg
        try{
            $path = './uploads/';
            $file = $this->request->file('upload');

            if($file && $file->checkImg()){
                //判断重复文件
                $md5 = md5_file($file->getRealPath());
                $size = $file->getSize();

                $modelImage= new Image();
                $query = $modelImage ->where([
                    'md5' => $md5,
                    'size' => $size
                ])-> find();

                if(!empty($query))
                    return json([
                        "uploaded"=> 1,
                        "fileName"=> $query['name'],
                        "url"=> $query['url']
                    ]);

                //压缩
                $sourceExt = image_type_to_extension(getimagesize($file->getRealPath())[2],false);
                in_array($sourceExt,['jpeg', 'png', 'bmp','gif']);
                $func1 = "imagecreatefrom$sourceExt";
                $o = $func1($file->getRealPath());
                $func2 = "image$sourceExt";
                $func2($o,$file->getRealPath(),5);

                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move($path);
                if($info){
                    $url = '/uploads/'.$info->getSaveName();
                    $filename = $info->getFilename();

                    $modelImage ->insert([
                        'md5' => $md5,
                        'size' => $size,
                        'name' => $filename,
                        'url'=> $url
                    ], false);

                    return json([
                        "uploaded"=> 1,
                        "fileName"=> $filename,
                        "url"=> $url

                    ]);
                }else{
                    // 上传失败获取错误信息
                    return json([
                        "uploaded"=> 0,
                        "error"=>[
                            "message"=> $file->getError()
                        ]
                    ]);
                }
            }
        } catch(\Exception $e){
            Log::error($e->getMessage());
        }

        return json([
            "uploaded"=> 0,
            "error"=>[
                "message"=> 'No input file specific.'
            ]
        ]);
    }

    //月统计
    public function getMonthStatusAction(){
        $order = model('Order');

        $month = date('m');
        $date = [];
        $amount = [];
        $count = [];

        for($i = 0; $i < 12; $i++){
            $start = mktime(23,59,59,$month - $i, 0);
            $stop = mktime(23,59,59,$month - $i+1, 0);

            array_unshift($date, date('Ym',$stop));
            $data = $order -> whereBetween('update_time',"$start,$stop")->field('ifnull(sum(total_price), 0) as s, count(*) as c')->cache(true,600)->find();
            array_unshift($amount, $data['s']);
            array_unshift($count, $data['c']);
        }

        return json(['code' => 1, 'data'=>['count' => $count, 'amount' => $amount, 'date' => $date]]);
    }
}