<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/1/31
 * Time: 22:14
 */

namespace app\admin\controller;


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
        return json([
            "uploaded"=> 1,
            "fileName"=> "null.png",
            "url"=> "/static/img/null.png",
            "error"=>[
                "message"=> "A file with the same name already exists. The uploaded file was renamed to \"foo(2).jpg\"."

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