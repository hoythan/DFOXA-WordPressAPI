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

        // 当前用户的唯一效验码
        $onlytoken = parent::creatOnlyToken($userid);

        // 加盐的效验码
        $salttoken = substr($onlytoken . get_RandStr(5, 6), -32);

        // 检测是否开始OPENSSL
        if(!function_exists('openssl_private_encrypt'))
            dfoxaError('gateway.error-openssl');

        // 加密后的内容
        $encrypted = "";
        if (!openssl_private_encrypt($salttoken, $encrypted, $private_key))
            dfoxaError('account.error-private');

        $encrypted = str_replace(array('=', '/', '-', '+', '&', '*', '?'), array(''), base64_encode($encrypted));
        $access_token = substr($encrypted, -33, -1);

        /*
            写入内存
            过期时间 1 小时后,每次调用 API 接口自动延长
         */
        $expire = time() + 3600;
        $cacheDriver = new \cached\cache();
        $res = $cacheDriver->set($access_token, $onlytoken, '', $expire);

        // 是否成功写入缓存
        if ($res !== true)
            dfoxaError('cache.empyt-cachetype');

        /*
         * 保证token的唯一性，其他设备登录将无法访问
         * 将 $onlytoken 存储在缓存,判断 token_$userid 是否为当前onlytoken值
         */
        $cacheDriver->set('onlytoken_check_' . $userid, $onlytoken);

        return $access_token;
    }
}

?>