<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/8/2019
 * Time: 4:19 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;

class shop extends Command
{
    protected function configure()
    {
        $this->setName('shop')->setDescription('Here is the remark ');
    }

    protected function execute(Input $input, Output $output)
    {

    }
}