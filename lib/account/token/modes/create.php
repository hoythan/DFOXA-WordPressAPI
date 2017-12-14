<?php

namespace account\token;
class create extends token
{

    public function run()
    {
        dfoxaError('gateway.close-api');
    }

    public static function get($userid)
    {
        $private_key = get_option('dfoxa_t_rsa_private');
        // 检查私钥格式
        if (empty($private_key))
            dfoxaError('account.empty-privatekey');

        // 检测是否开始OPENSSL
        if (!function_exists('openssl_private_encrypt'))
            dfoxaError('gateway.error-openssl');

        // 当前用户的唯一效验码
        $onlytoken = parent::_creatOnlyToken($userid);

        // 加盐的效验码
        $salttoken = substr($onlytoken . get_RandStr(5, 6), -32);

        // 加密后的内容
        $encrypted = "";
        if (!openssl_private_encrypt($salttoken, $encrypted, $private_key))
            dfoxaError('account.error-private');

        $encrypted = str_replace(array('=', '/', '-', '+', '&', '*', '?'), array(''), base64_encode($encrypted));
        $access_token = substr($encrypted, -33, -1);


        $cacheDriver = new \cached\cache();
        $res = $cacheDriver->set($access_token, $onlytoken, 'access_token', parent::_expireTime());
        $group_key = 'access_token_' . $userid;
        $cacheDriver->clearGroup($group_key);
        $cacheDriver->set($onlytoken, 'yes', $group_key, parent::_expireTime());

        // 是否成功写入缓存
        if ($res !== true)
            dfoxaError('cache.empyt-cachetype');

        return $access_token;
    }
}

?>