<?php

namespace account\sign;

use Respect\Validation\Validator as Validator;
use account\token\create as tokenCreate;
use gateway\mothod as Gateway;

abstract class sign
{
    public function run()
    {
        throw new \Exception('gateway.close-api');
    }

    public static function _registerAccount($user_account, $user_password = '', $user_email = '')
    {
        // 判断账号是否注册
        if (username_exists($user_account))
            throw new \Exception('account.exists-account');

        // 判断账号是否是邮箱
        if (Validator::email()->validate($user_account))
            throw new \Exception('account.error-accountisemail');

        // 判断是否用户自定义密码,否则生成随机密码
        if (empty($user_password)) {
            $user_password = get_RandStr();
        }

        // 验证邮箱格式和是否注册
        if (!empty($user_email)) {
            if (!Validator::email()->validate($user_email))
                throw new \Exception('account.error-email');

            if (email_exists($user_email))
                throw new \Exception('account.exists-email');
        }

        // 注册用户
        $result = wp_create_user($user_account, $user_password, $user_email);
        if (is_wp_error($result) || $result == 0) {
            set_AppendMsg('account.error-create', array('errorMsg' => $result));
            throw new \Exception('account.error-create');
        }

        $userid = $result;
        return $userid;
    }

    public static function getUserMetas($userid)
    {
        $usermeta = array();
        $usermetakey = get_option('dfoxa_account_query_usermetakey');

        // 判断后台是否有设置metakey
        if (empty($usermetakey))
            return $usermeta;

        if ($usermetakey != '*') {
            $metakey = explode(',', $usermetakey);
            foreach ($metakey as $key) {
                $usermeta[$key] = get_user_meta($userid, $key, true);
            }
        } else {
            $disablemetas = array(
                'session_tokens'
            );
            $usermetas = get_user_meta($userid);
            foreach ($usermetas as $key => $value) {
                if (!in_array($key, $disablemetas)) {
                    if (count($value) <= 1) {
                        $usermeta[$key] = maybe_unserialize($value[0]);
                    } else {
                        $usermeta[$key] = $value;
                    }
                }
            }

        }

        // 只返回用户需要的usermeta
        $query = bizContentFilter(array(
            'usermeta'
        ));
        if (!empty($query->usermeta)) {
            $metas = $usermeta;
            $usermeta = array();
            foreach ($query->usermeta as $metakey => $metaval) {
                if (!empty($metas[$metakey])) {
                    $usermeta[$metakey] = $metas[$metakey];
                } else {
                    $usermeta[$metakey] = "";
                }
            }

        }
        return $usermeta;
    }


    /*
     * 用户登陆(无身份验证,谨慎使用)
     */
    public static function _loginAccount($field, $value)
    {
        $user = get_user_by($field, $value);
        if (empty($user))
            throw new \Exception('account.empty-userlogin');

        // 自动登录
        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID);
        do_action('wp_login', $user->user_login);

        $responseData = array(
            'userid' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'usermeta' => self::getUserMetas($user->ID),
            'access_token' => tokenCreate::get($user->ID)
        );
        $responseData = apply_filters('account_signin_data', $responseData);
        Gateway::responseSuccessJSON($responseData);
    }

    /*
     * 获取用户信息
     * (不包括access_token)
     */
    public static function _getUserAccount($field, $value){
        $user = get_user_by($field, $value);
        if (empty($user))
            throw new \Exception('account.empty-userlogin');

        $responseData = array(
            'userid' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'usermeta' => self::getUserMetas($user->ID)
        );
        $responseData = apply_filters('account_signin_data', $responseData);
        return $responseData;
    }
}

?>