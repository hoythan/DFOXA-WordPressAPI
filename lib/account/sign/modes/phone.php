<?php

namespace account\sign;

use account\token\create as tokenCreate;
use gateway\mothod as Gateway;
/*
 * 手机验证的登陆/注册
 */
class phone extends sign
{
    /*
     * 注册
     */
    public function register()
    {
        
    }

    /*
     * 登陆
     */
    public function login()
    {
        $query = bizContentFilter(array(
            "phone",
            "password"
        ));

        // 验证账号是否存在
        if(!username_exists($query->phone)){
            throw new \Exception('account.error-login-account');
        }

        echo 'ok';
        exit;
    }

    public function test()
    {
        echo 'test account\sign\phone';
    }
}