<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/11
 * Time: 22:28
 */

namespace app\common\command\user;

use think\console\Output;
use think\console\Input;
use think\console\Command;
use think\console\input\Argument;
use app\common\model\AdminAccount;
use think\console\input\Option;

class reset extends Command
{
    protected function configure()
    {

        //设置参数
//        $this->addArgument('email', Argument::REQUIRED); //必传参数
//        $this->addArgument('mobile', Argument::OPTIONAL);//可选参数
//        //选项定义
//        $this->addOption('message', 'm', Option::VALUE_REQUIRED, 'test'); //选项值必填
//        $this->addOption('status', 's', Option::VALUE_OPTIONAL, 'test'); //选项值选填


        $this->setName('user:reset')
            -> addOption('email', 'e', Option::VALUE_REQUIRED, 'User Email')
            -> addOption('username', 'u', Option::VALUE_REQUIRED, 'Username')
            -> addOption('password', 'p', Option::VALUE_REQUIRED, 'Password')
            ->setDescription('Reset e-shop user password.');


    }

    protected function execute(Input $input, Output $output)
    {
        //获取选项值
        $email = $input->hasOption('email')?trim($input->getOption('email')):'';
        $username = $input->hasOption('username')?trim($input->getOption('username')):'';
        $password = $input->hasOption('username')?trim($input->getOption('password')):'';

        $modelAdminAccount = new AdminAccount();
        $modelAdminRole = new AdminRole();


        try{
            if(empty($modelAdminRole->get(1)))
                $modelAdminRole ->save([
                    'menu_id' => '*',
                    'status' => 1
                ]);

            $modelAdminAccount ->startTrans();

            $result = $modelAdminAccount
                ->save([
                    'email' => $email,
                    'username' => $username,
                    'password' => secret($password),
                    'status' => 1,
                    'role_id' => 1,
                ]);

            $modelAdminAccount ->commit();
            $output->writeln("$result account added.");
        }catch (\Exception $e){
            $modelAdminAccount->rollback();
            $output->writeln($e->getMessage());
        }

        $output->writeln("End..");
    }
}