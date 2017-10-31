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
}

?>