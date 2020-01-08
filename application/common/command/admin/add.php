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

namespace app\common\command\admin;


use app\common\model\AdminAccount;
use app\admin\model\AdminRole;
use think\console\Output;
use think\console\Input;
use think\console\Command;
use think\console\input\Argument;
use think\console\input\Option;

class add extends Command
{
    protected function configure()
    {

        //设置参数
//        $this->addArgument('email', Argument::REQUIRED); //必传参数
//        $this->addArgument('mobile', Argument::OPTIONAL);//可选参数
//        //选项定义
//        $this->addOption('message', 'm', Option::VALUE_REQUIRED, 'test'); //选项值必填
//        $this->addOption('status', 's', Option::VALUE_OPTIONAL, 'test'); //选项值选填


        $this->setName('admin:add')
            -> addOption('email', 'e', Option::VALUE_REQUIRED, 'User Email')
            -> addOption('username', 'u', Option::VALUE_REQUIRED, 'Username')
            -> addOption('password', 'p', Option::VALUE_REQUIRED, 'Password')
            ->setDescription('Add e-shop admin user.');


    }

    protected function execute(Input $input, Output $output)
    {
        //获取选项值
        $email = $input->hasOption('email')?trim($input->getOption('email')):'';
        $username = $input->hasOption('username')?trim($input->getOption('username')):'';
        $password = $input->hasOption('password')?trim($input->getOption('password')):'';

        if(empty($email) || empty($password) || empty($username)){

            $output->writeln("-e Email");
            $output->writeln("-u Admin Username");
            $output->writeln("-p Admin Password");
            $output->writeln("End..");
            return;
        }


        $modelAdminAccount = new AdminAccount();
        $modelAdminRole = new AdminRole();


        try{
            if(empty($modelAdminRole->get(1)))
                $modelAdminRole ->save([
                    'menu_id' => '*',
                    'status' => 1,
                    'name'=>'超级管理员'
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
            $output->writeln($e->getMessage());
        }

        $output->writeln("End..");
    }
}