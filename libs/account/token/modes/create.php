<?php

namespace account\token;
class create extends token
{
    function __construct()
    {
    }

    public static function get($userid)
    {
        $private_key = get_blog_option(get_main_site_id(), 'dfoxa_t_rsa_private');
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

        // 混淆 access_token，使每次产生不同的 token 值
        $access_token = "";
        if (!openssl_private_encrypt(get_GUIDStr(), $access_token, $private_key))
            dfoxaError('account.error-private');
        $access_token = str_replace(array('=', '/', '-', '+', '&', '*', '?'), array(''), base64_encode($access_token));
        $access_token = substr($access_token, -33, -1);

        // 切换缓存系统至主站点
        switch_to_blog(1);
        $res = wp_cache_set($access_token, $onlytoken, '_access_token', parent::_expireTime());
        wp_cache_set($onlytoken, 1, '_access_token_' . $userid);

        // 是否成功写入缓存
        if ($res !== true)
            dfoxaError('cache.empyt-cachetype');

        // 恢复缓存系统站点
        switch_to_blog(dfoxa_get_query_mulitsite_blog_id());
        return $access_token;
    }
}

?>