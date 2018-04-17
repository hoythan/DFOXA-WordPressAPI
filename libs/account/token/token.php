<?php

namespace account\token;

abstract class token
{
    protected static function _creatOnlyToken($userid)
    {
        $userip = get_ClientIP();
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        $salt = 'default';

        $limit = get_blog_option(get_main_site_id(), 'dfoxa_account_signin_limit');
        $device = wp_is_mobile() ? 'mobile' : 'pc';
        switch ($limit) {
            case 'disable': // 禁止登录
                dfoxaError('account.close-login');
                break;
            case 'single': // 单设备登录
                $salt = $userip . $useragent;
                break;
            case 'single-device': // 不同端单设备登录
                $salt = $device;
                break;
            case 'ip': // 同 IP 多设备登录
                $salt = $userip;
                break;
            case 'open':
                // 开放限制
                $salt = '';
                break;
            default:
                $salt = $userip . $useragent;
                break;
        }

        return $userid . '#' . md5($salt);
    }


    /**
     * 获取 token 过期时间
     */
    public static function _expireTime()
    {
        $append_time = (int)get_blog_option(get_main_site_id(), 'dfoxa_account_access_token_expire');
        if (empty($append_time)) {
            $append_time = 3600;
        } elseif ($append_time <= 60) {
            $append_time = 60;
        }

        return $append_time;
    }
}
?>