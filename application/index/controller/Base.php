<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/2/2
 * Time: 21:27
 */

namespace app\index\controller;

use app\common\model\Image;

class Base extends Common
{

    public function __construct(){
        parent::__construct();

        if(!$this->isLogin())
            $this->redirect('index/login/login', ['r' =>  urlencode($this->request->url(true))]);
    }

    protected function uploadAction(){
        // 获取表单上传文件 例如上传了001.jpg
        try{
            $path = './uploads/';
            $file = $this->request->file('upload');

            if($file && $file->checkImg()){
                //判断重复文件
                $md5 = md5_file($file->getRealPath());
                $size = $file->getSize();

                if($size > 1024*1024*20){
                    return [
                        "uploaded"=> 0,
                        "error"=>[
                            "message"=> 'Image is too big.'
                        ]
                    ];
                }

                $modelImage= new Image();
                $query = $modelImage ->where([
                    'md5' => $md5,
                    'size' => $size
                ])-> find();

                if(!empty($query))
                    return [
                        "uploaded"=> 1,
                        "fileName"=> $query['name'],
                        "url"=> $query['url']
                    ];

                //压缩
                $sourceExt = image_type_to_extension(getimagesize($file->getRealPath())[2],false);
                in_array($sourceExt,['jpeg', 'png', 'bmp','gif']);
                $func1 = "imagecreatefrom$sourceExt";
                $o = $func1($file->getRealPath());
                $func2 = "image$sourceExt";
                $func2($o,$file->getRealPath(),5);

                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->move($path, random_str(60));
                if($info){
                    $url = '/uploads/'.$info->getSaveName();
                    $filename = $info->getFilename();

                    $modelImage ->insert([
                        'md5' => $md5,
                        'size' => $size,
                        'name' => $filename,
                        'url'=> $url
                    ], false);

                    return [
                        "uploaded"=> 1,
                        "fileName"=> $filename,
                        "url"=> $url
                    ];
                }else{
                    // 上传失败获取错误信息
                    return [
                        "uploaded"=> 0,
                        "error"=>[
                            "message"=> $file->getError()
                        ]
                    ];
                }
            }
        } catch(\Exception $e){
            Log::error($e->getMessage());
        }

        return [
            "uploaded"=> 0,
            "error"=>[
                "message"=> 'No input file specific.'
            ]
        ];
    }
}