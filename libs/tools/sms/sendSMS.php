<?php

namespace tools\sms;

/*
 * 短信验证码接口
 *
 * send 发送短信
 * sends 群发短信
 */

class sendSMS
{
    public $SMSObj;

    function __construct()
    {
        $this->sms_service = is_multisite() ? get_blog_option(get_main_site_id(), 'dfoxa_sms_service') : get_option('dfoxa_sms_service');

        // 检查环境是否允许
        $this->_setupCheckOptions();

        // 注册相关短信服务商
        $smsMode = '\tools\sms\\' . $this->sms_service;

        if (!class_exists($smsMode))
            dfoxaError('sms.empyt-service');

        // 子类请通过此对象操作短信服务商接口
        $this->SMSObj = new $smsMode();
    }

    /*
     * 工具类接口通常不对外暴露
     */
    public function run()
    {
        dfoxaError('gateway.close-api');
    }

    /*
     * 发送短信
     */

    public function send($phone, $data)
    {
        // 验证手机号
        if (!self::_verifyPhoneNumber($phone))
            dfoxaError('sms.error-phonenumber');

        // 验证 data

        return $this->SMSObj->send($phone, $data);
    }





    /*
        功能模块
        ==========================================================================================
    */
    /*
     * 验证手机号是否正确
     */
    public static function _verifyPhoneNumber($phone)
    {
        if (strlen($phone) != 11)
            return false;

        if (!preg_match("/0?(13|14|15|17|18)[0-9]{9}$/", $phone))
            return false;

        return true;
    }


    /*
     * 模版参数处理
     */
    public static function _filterParam($smsparam)
    {
        $params = explode(PHP_EOL, $smsparam);
        $smsParams = array();
        foreach ($params as $param) {
            $param = trim($param);
            $exp = explode(':', $param);
            // 替换模版字符串
            // {{code => 验证码}}
            $code = rand(1000, 9999);
            $exp[1] = str_replace('{{code}}', $code, $exp[1]);

            // {{sitename => 网站名称}}
            $sitename = get_bloginfo('name');
            $exp[1] = str_replace('{{sitename}}', $sitename, $exp[1]);

            $smsParams[$exp[0]] = trim($exp[1]);
        }

        return $smsParams;
    }

    /*
     * 检查所需配置是否启用
     */
    private function _setupCheckOptions()
    {
        $appkey = is_multisite() ? get_blog_option(get_main_site_id(), 'dfoxa_sms_appkey') : get_option('dfoxa_sms_appkey');
        $appsecret = is_multisite() ? get_blog_option(get_main_site_id(), 'dfoxa_sms_appsecret') : get_option('dfoxa_sms_appsecret');
        $sms_cycle = is_multisite() ? get_blog_option(get_main_site_id(), 'dfoxa_sms_cycle') : get_option('dfoxa_sms_cycle');

        if ($this->sms_service == 'alidayu') {
            if (empty($appkey) || empty($appsecret) || (int)$sms_cycle < 10)
                dfoxaError('access.empty-smsapi');

            return true;
        }

        dfoxaError('access.empty-smsservice');
    }


    public function test()
    {
        return 'test verify\sms\sms test';
    }
}