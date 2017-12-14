<?php

namespace account\token;

abstract class token
{

    protected static function _creatOnlyToken($userid)
    {
        $userip = get_ClientIP();
        $useragent = $_SERVER['HTTP_USER_AGENT'];

        $salt = 'default';

        $limit = get_option('dfoxa_account_signin_limit');
        switch ($limit) {
            case 'disable':
                dfoxaError('account.close-login');
                break;
            case 'single':
                $salt = $userip . '#' . $useragent;
                break;
            case 'ip':
                $salt = $userip . '#' . '';
                break;
            case 'open':
                break;
            default:
                $salt = $userip . '#' . $useragent;
                break;
        }

        return $userid . '#' . md5($salt);
    }


    /**
     * 获取 token 过期时间
     */
    public static function _expireTime()
    {
        $append_time = (int)get_option('dfoxa_account_access_token_expire');
        if (empty($append_time)) {
            $append_time = 3600;
        } elseif ($append_time <= 60) {
            $append_time = 60;
        }

        return $append_time;
    }

}

?>