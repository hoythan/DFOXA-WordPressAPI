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
    public function register()
    {
        Gateway::responseSuccessJSON($this->get_register());
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
            'password',
            'usermeta'
        ));

        if (empty($query->username) || empty($query->email) || empty($query->password))
            throw new \Exception('account.empty-register-query');

        $userid = parent::_registerAccount($query->username, $query->password, $query->email);

        $usermeta = array();
        if (!empty($query->usermeta)) {
            foreach ($query->usermeta as $key => $value) {
                update_user_meta($userid, $key, $value);
                $usermeta[$key] = $value;
            }
        }

        return array(
            'userid' => $userid,
            'username' => $query->username,
            'email' => $query->email,
            'usermeta' => parent::getUserMetas($userid),
            'access_token' => tokenCreate::get($userid)
        );
    }

    /*
     * 登陆
     */
    public function login()
    {
        $query = bizContentFilter(array(
            'username',
            'email',
            'password'
        ));


        $account = '';

        if (empty($query->password))
            throw new \Exception('account.error-login-password');

        if (!empty($query->username) && username_exists($query->username)) {
            // 如果账号存在并且已注册则使用账号登陆
            $account = $query->username;
        } elseif (!empty($query->email) && Validator::email()->validate($query->email) && email_exists($query->email)) {
            // 如果邮箱存在并格式正确并已注册则使用邮箱登陆
            $userby = get_user_by('email', $query->email);
            $account = $userby->user_email;
        } else {
            throw new \Exception('account.error-login-password');
        }

        // 登陆账户(验证密码)
        $user = wp_authenticate($account, $query->password);
        if (is_wp_error($user))
            throw new \Exception('account.error-login-password');

        $responseData = array(
            'userid' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'usermeta' => parent::getUserMetas($user->ID),
            'access_token' => tokenCreate::get($user->ID)
        );
        $responseData = apply_filters('account_signin_data',$responseData);
        Gateway::responseSuccessJSON($responseData);
    }

    public function test()
    {
        echo 'test account\sign\account';
    }
}