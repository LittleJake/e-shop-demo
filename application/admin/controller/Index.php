<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/1/31
 * Time: 22:14
 */

namespace app\admin\controller;


use think\facade\Log;

class Index extends Base
{
    public function indexAction()
    {
        return $this->fetch();
    }

    public function adminAction()
    {
        return $this->fetch();
    }

    public function homeAction()
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
                $info = $file->move($path);
                if($info){
                    return json([
                        "uploaded"=> 1,
                        "fileName"=> $info->getFilename(),
                        "url"=> DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$info->getSaveName()

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

    public function browseAction(){
        return json([
            "uploaded"=> 1,
            "fileName"=> "null.png",
            "url"=> "/static/img/null.png",
            "error"=>[
                "message"=> "A file with the same name already exists. The uploaded file was renamed to \"foo(2).jpg\"."

            ]
        ]);
    }
}