<?php
/**
 * Created by IntelliJ IDEA.
 * User: LittleJake
 * Date: 2019/12/11
 * Time: 21:47
 */

namespace app\common\command\admin;


use app\admin\model\AdminAccount;
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

        $this->setName('admin:del')
            -> addOption('id', 'i', Option::VALUE_OPTIONAL, 'User ID')
            -> addOption('email', 'e', Option::VALUE_REQUIRED, 'User Email')
            ->setDescription('Del e-shop admin user.');


    }

    protected function execute(Input $input, Output $output)
    {
        //获取选项值
        $email = $input->hasOption('email')?trim($input->getOption('email')):'';
        $id = $input->hasOption('id')?trim($input->getOption('id')):'';

        if(empty($email) && empty($id))
            throw new \InvalidArgumentException('At least one option is specified.');


        $modelAdminAccount = new AdminAccount();

        try{

            $modelAdminAccount ->startTrans();
            $result = $modelAdminAccount ->whereOr([
                'email' => $email,
                'id' => $id
            ])->delete();

            $modelAdminAccount ->commit();
            $output->writeln("$result account deleted.");
        }catch (\Exception $e){
            $modelAdminAccount->rollback();
            $output->writeln($e->getMessage());
        }

        $output->writeln("End..");
    }
}