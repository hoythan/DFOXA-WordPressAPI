<?php
    namespace account\sign;

    class up
    {
        private static $namespace = '\account\sign\\';

        /*
         * 对外开放的接口所需函数
         */
        public function run()
        {
            $account_reg = get_option('dfoxa_account_reg');

            // 判断后台是否设置注册
            if(empty($account_reg))
                throw new \Exception('account.empty-register');

            // 判断是否关闭注册
            if($account_reg != 'open')
                throw new \Exception('account.close-register');

            // 判断允许的注册方式
            $type = get_option('dfoxa_account_type');
            if(empty($type))
                throw new \Exception('account.empty-type');

            //自定义注册方式检查
            if($type == 'custom'){
                $query = bizContentFilter(array('type'));
                if(empty($query->type))
                    throw new \Exception('account.empty-type');

                $type = $query->type;
            }

            $account_regtype = self::$namespace . $type;

            if(!class_exists($account_regtype))
                throw new \Exception('account.error-type');

            $run = new $account_regtype();
            $run->register();
        }
    }
?>