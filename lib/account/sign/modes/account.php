<?php

namespace account\sign;

use account\token\create as tokenCreate;
use gateway\mothod as Gateway;

use Respect\Validation\Validator as Validator;

/*
 * 常规方式的登陆/注册
 */

class account extends sign
{
    /*
     * 登录账号
     * 允许插件内调用
     */
    public function get_login(){
        $query = bizContentFilter(array(
            'username',
            'email',
            'password'
        ));


        $account = '';

        if (empty($query->password))
            dfoxaError('account.error-login-password');

        if (!empty($query->username) && username_exists($query->username)) {
            // 如果账号存在并且已注册则使用账号登陆
            $account = $query->username;
        } elseif (!empty($query->email) && Validator::email()->validate($query->email) && email_exists($query->email)) {
            // 如果邮箱存在并格式正确并已注册则使用邮箱登陆
            $userby = get_user_by('email', $query->email);
            $account = $userby->user_email;
        } else {
            dfoxaError('account.error-login-password');
        }

        // 登陆账户(验证密码)
        $user = wp_authenticate($account, $query->password);
        if (is_wp_error($user))
            dfoxaError('account.error-login-password');

        return self::_getUserAccount('ID',$user->ID,true);
    }

    /*
     * 注册账号
     * 允许插件内调用
     */
    public function get_register()
    {
        $query = bizContentFilter(array(
            'username',
            'email',
            'password'
        ));

        if (empty($query->username) || empty($query->email) || empty($query->password))
            dfoxaError('account.empty-register-query');

        $user = parent::_registerAccount($query->username, $query->password, $query->email);
        $userid = $user['userid'];
        return self::_getUserAccount('ID', $userid, true);
    }
}