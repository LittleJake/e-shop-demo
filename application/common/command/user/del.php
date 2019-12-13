<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/11
 * Time: 22:28
 */

namespace app\common\command\user;

use app\common\model\Account;
use think\console\Output;
use think\console\Input;
use think\console\Command;
use think\console\input\Argument;
use think\console\input\Option;

class del extends Command
{
    protected function configure()
    {

        //设置参数
//        $this->addArgument('email', Argument::REQUIRED); //必传参数
//        $this->addArgument('mobile', Argument::OPTIONAL);//可选参数
//        //选项定义
//        $this->addOption('message', 'm', Option::VALUE_REQUIRED, 'test'); //选项值必填
//        $this->addOption('status', 's', Option::VALUE_OPTIONAL, 'test'); //选项值选填


        $this->setName('user:del')
            -> addOption('email', 'e', Option::VALUE_OPTIONAL, 'User Email')
            -> addOption('id', 'u', Option::VALUE_OPTIONAL, 'Username')
            ->setDescription('Del e-shop user.');


    }

    protected function execute(Input $input, Output $output)
    {
        //获取选项值
        $email = $input->hasOption('email')?trim($input->getOption('email')):'';
        $id = $input->hasOption('id')?trim($input->getOption('id')):'';

        $modelAccount = new Account();

        try{
            if(empty($modelAccount->get(1)))
                $modelAccount ->save([
                    'menu_id' => '*',
                    'status' => 1
                ]);

            $modelAccount ->startTrans();

            $result = $modelAccount
                ->save([
                    'email' => $email,
                    'username' => $id,
                ]);

            $modelAccount ->commit();
            $output->writeln("$result account added.");
        }catch (\Exception $e){
            $output->writeln($e->getMessage());
        }

        $output->writeln("End..");
    }
}