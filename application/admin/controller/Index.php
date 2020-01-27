<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/1/31
 * Time: 22:14
 */

namespace app\admin\controller;


use app\common\model\Image;
use think\facade\Log;

class Index extends Base
{
    /** 首页 */
    public function indexAction()
    {
        return $this->fetch();
    }

    public function consoleAction(){
        return $this->fetch();
    }

    public function uploadAction(){
        // 获取表单上传文件 例如上传了001.jpg
        try{
            $path = './uploads/';
            $file = $this->request->file('upload');


            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file && $file->checkImg()){
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

}