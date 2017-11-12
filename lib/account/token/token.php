<?php

namespace account\token;

abstract class token
{

    public static function creatOnlyToken($userid)
    {
        $userip = get_ClientIP();
        $useragent = $_SERVER['HTTP_USER_AGENT'];
//        $token_prefix = $userip.'#'.$useragent;
        $token_prefix = $userip;
        return $userid . '#' . md5($token_prefix);
    }

    /**
     * 获取 token 过期时间
     */
    public static function expireTime(){
        $append_time = (int)get_option('dfoxa_account_access_token_expire');
        if (empty($append_time)) {
            $append_time = 3600;
        } elseif ($append_time <= 60) {
            $append_time = 60;
        }

        return time() + $append_time;
    }

}

?>