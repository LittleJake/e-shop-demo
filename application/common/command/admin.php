<?php
/**
 * Created by IntelliJ IDEA.
 * User: Jake
 * Date: 12/8/2019
 * Time: 4:20 PM
 *
 * Stay simple, stay naive.
 *
 */

namespace app\common\command;


use think\console\Output;
use think\console\Input;
use think\console\Command;
use think\console\input\Argument;
use think\console\input\Option;

class admin extends Command
{
    protected function configure()
    {

        //设置参数
        $this->addArgument('email', Argument::REQUIRED); //必传参数
        $this->addArgument('mobile', Argument::OPTIONAL);//可选参数
        //选项定义
        $this->addOption('message', 'm', Option::VALUE_REQUIRED, 'test'); //选项值必填
        $this->addOption('status', 's', Option::VALUE_OPTIONAL, 'test'); //选项值选填

        $this->setName('admin')->setDescription('Here is the remark ');
    }

    protected function execute(Input $input, Output $output)
    {
        //获取参数值
        $args = $input->getArguments();
        $output->writeln('The args value is:');
        print_r($args);
        //获取选项值
        $options = $input->getOptions();
        $output->writeln('The options value is:');
        print_r($options);


        $output->writeln("TestCommand:");
        $output->writeln("End..");
    }
}