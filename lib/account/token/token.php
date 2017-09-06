<?php

namespace account\token;

abstract class token
{

    public static function creatOnlyToken($userid)
    {
//        if ($_SERVER['REMOTE_ADDR']) {
//            $userip = $_SERVER['REMOTE_ADDR'];
//        } elseif (getenv("REMOTE_ADDR")) {
//            $userip = getenv("REMOTE_ADDR");
//        } elseif (getenv("HTTP_CLIENT_IP")) {
//            $userip = getenv("HTTP_CLIENT_IP");
//        } else {
//            $userip = "127.0.0.1";
//        }
        $userip = "127.0.0.1";
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $token_prefix = $userip.'#'.$useragent;
        return $userid.'#'.md5($token_prefix);
    }
}

?>