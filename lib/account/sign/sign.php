<?php

namespace account\sign;

use Respect\Validation\Validator as Validator;
use account\token\create as tokenCreate;

abstract class sign
{
    public function run()
    {
        dfoxaError('gateway.close-api');
    }

    /**
     * 账号自动登录或自动注册,根据用户账号
     * 危险的接口,如果未创建该用户,则自动生成密码和邮箱.
     * @param $field The field to retrieve the user with. id | ID | slug | email | login.
     * @param $value A value for $field. A user ID, slug, email address, or login name.
     */
    public static function _autoSignAccount($field, $value)
    {
        // 判断账号是否注册
        $user = get_user_by($field, $value);
        if (!empty($user)) {
            return self::_loginAccount($field, $value);
        }

        $user_account = '';
        $user_email = '';

        if ($field === 'login' || $field === 'slug') {
            $user_account = $value;
        } else if ($field == 'email') {
            $user_account = $value;
            $user_email = $value;
        } else {
            $user_account = 'user_' . get_RandStr();
        }
        $userid = self::_registerAccount($user_account, $user_email);

        return self::_loginAccount('id', $userid);
    }

    /*
     * 用户登陆方法 (无身份验证,谨慎使用),
     * 返回登录成功后的用户ID 否则接口报错
     */
    public static function _loginAccount($field, $value)
    {
        $user = get_user_by($field, $value);
        if (empty($user))
            dfoxaError('account.empty-userlogin');

        // 自动登录
        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID);
        do_action('wp_login', $user->user_login);

        $responseData = self::_getUserAccount('ID', $user->ID, true);

        return $responseData;
    }

    /*
     * 注册方法,返回注册成功后的用户信息 否则接口报错
     */
    public static function _registerAccount($user_account, $user_password = '', $user_email = '')
    {
        // 判断账号是否注册
        if (username_exists($user_account))
            dfoxaError('account.exists-account');

        // 判断账号是否是邮箱
        if (Validator::email()->validate($user_account))
            dfoxaError('account.error-accountisemail');

        // 判断是否用户自定义密码,否则生成随机密码
        if (empty($user_password)) {
            $user_password = get_RandStr();
        }

        // 验证邮箱格式和是否注册
        if (!empty($user_email)) {
            if (!Validator::email()->validate($user_email))
                dfoxaError('account.error-email');

            if (email_exists($user_email))
                dfoxaError('account.exists-email');
        }

        // 注册用户
        $result = wp_create_user($user_account, $user_password, $user_email);
        if (is_wp_error($result) || $result == 0) {
            dfoxaError('account.error-create', array('errorMsg' => $result));
        }

        return self::_getUserAccount('ID', $result, true);
    }

    /*
     * 获取用户信息
     * (非登录接口,需要自行验证用户,不提供用户 access_token 信息)
     * 强行获取token 可能会导致安全问题以及该用户被强制下线问题
     */
    public static function _getUserAccount($field, $value, $getToken = false)
    {
        $user = get_user_by($field, $value);
        if (empty($user))
            dfoxaError('account.empty-userlogin');

        $responseData = array(
            'userid' => $user->ID,
            'username' => $user->user_login,
            'displayname' => get_the_author_meta('display_name', $user->ID),
            'email' => $user->user_email,
            'usermeta' => self::_getUserMetas($user->ID)
        );

        if ($getToken)
            $responseData['access_token'] = tokenCreate::get($user->ID);

        $responseData = apply_filters('dfoxa_account_signin_data', $responseData);
        return $responseData;
    }

    /*
     * 获取用户usermeta
     */
    public static function _getUserMetas($userid)
    {
        $usermeta = array();
        $usermetakey = get_option("dfoxa_account_query_usermetakey");

        // 判断后台是否有设置metakey
        if (empty($usermetakey))
            return $usermeta;

        if ($usermetakey != "*") {
            $metakey = explode(",", $usermetakey);
            foreach ($metakey as $key) {
                $usermeta[$key] = get_user_meta($userid, $key, true);
            }
        } else {
            $disablemetas = array("session_tokens");
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
        $query = bizContentFilter(array("usermeta"));
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

        $usermeta = apply_filters('account_usermetas', $usermeta);
        return $usermeta;
    }
}

?>