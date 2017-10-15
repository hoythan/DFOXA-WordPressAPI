<?php
namespace account\sign;

use gateway\mothod as Gateway;

class in
{
    private static $namespace = '\account\sign\\';

    /*
     * 对外开放的接口所需函数
     */
    public function run()
    {
        $account_login = get_option('dfoxa_account_login');

        // 判断后台是否设置登陆
        if(empty($account_login))
            dfoxaError('account.empty-login');

        // 判断是否关闭登陆
        if($account_login != 'open')
            dfoxaError('account.close-login');

        // 判断允许的登陆方式
        $type = get_option('dfoxa_account_type');
        if(empty($type))
            dfoxaError('account.empty-type');

        //自定义注册方式检查
        if($type == 'custom'){
            $query = bizContentFilter(array('type'));
            if(empty($query->type))
                dfoxaError('account.empty-type-custom');

            $type = $query->type;
        }

        $account_regtype = self::$namespace . $type;

        if(!class_exists($account_regtype))
            dfoxaError('account.error-type');

        $run = new $account_regtype();
        Gateway::responseSuccessJSON($run->get_login());
    }
}
?>