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

        // 判断是否支持 SSL 加密
        $encrypted = ""; // 加密后的内容
        if (!openssl_private_encrypt($onlytoken, $encrypted, $private_key))
            dfoxaError('account.error-private');

        $encrypted = str_replace(array('=', '/', '-', '+', '&', '*', '?'), array(''), base64_encode($encrypted));
        $access_token = substr($encrypted, -33, -1);

        $cacheDriver = new \cached\cache();
        $res = $cacheDriver->set($access_token, $onlytoken, '_access_token', parent::_expireTime());
        $cacheDriver->set($onlytoken, 1, '_access_token_' . $userid);

        // 是否成功写入缓存
        if ($res !== true)
            dfoxaError('cache.empyt-cachetype');

        return $access_token;
    }
}

?>