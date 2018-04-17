<?php

namespace tools\sms;

use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
use Flc\Alidayu\Requests\IRequest;

class alidayu
{
    public $config;
    function __construct()
    {
        $appkey = get_blog_option(get_main_site_id(), 'dfoxa_sms_appkey');
        $appsecret = get_blog_option(get_main_site_id(), 'dfoxa_sms_appsecret');

        $this->config = array(
            'app_key' => $appkey,
            'app_secret' => $appsecret
        );
    }

    /*
     * 发送短信
     *
     *
        $data

        array(
            'template_code' => 'SMS_xxxx',
            'sign_name' => 'DOOFOX',
            'param' => array()
        )
     */
    public function send($phone, $data)
    {
        $client = new Client(new App($this->config));
        $request = new AlibabaAliqinFcSmsNumSend;

        $request->setRecNum($phone)
            ->setSmsParam($data['param'])
            ->setSmsFreeSignName($data['sign_name'])
            ->setSmsTemplateCode($data['template_code']);

        $response = $client->execute($request);
        if(empty($response->sub_code) && $response->result->code == 0)
            return true;

        return self::_getErrorTest($response->sub_code);
    }

    private static function _getErrorTest($sub_code)
    {
        $_e_Code = array(
            'isv.OUT_OF_SERVICE' => '接口账户余额不足,业务停机',
            'isv.PRODUCT_UNSUBSCRIBE' => '产品服务未开通',
            'isv.ACCOUNT_NOT_EXISTS' => '账户信息不存在',
            'isv.ACCOUNT_ABNORMAL' => '账户信息异常',
            'isv.SMS_TEMPLATE_ILLEGAL' => '模版ID不合法',
            'isv.SMS_SIGNATURE_ILLEGAL' => '签名不合法',
            'isv.MOBILE_NUMBER_ILLEGAL' => '手机号码不存在或格式错误',
            'isv.MOBILE_COUNT_OVER_LIMIT' => '手机号码数量超过限制',
            'isv.TEMPLATE_MISSING_PARAMETERS' => '短信模版缺少变量参数',
            'isv.INVALID_PARAMETERS' => '短信模版参数异常或有误',
            'isv.BUSINESS_LIMIT_CONTROL' => '短信发送频率过快,请稍后再试',
            'isv.INVALID_JSON_PARAM' => 'JSON参数不合法',
            'isp.SYSTEM_ERROR' => '短信服务商未知系统错误',
            'isv.BLACK_KEY_CONTROL_LIMIT' => '模版变量存在非法关键词',
            'isv.PARAM_NOT_SUPPORT_URL' => '不支持url地址作为变量参数',
            'isv.PARAM_LENGTH_LIMIT' => '变量长度超出限制',
            'isv.AMOUNT_NOT_ENOUGH' => '账户余额不足,请检查后再试'
        );

        if (empty($_e_Code[$sub_code])) {
            return '未知错误,请检查后再试' . $sub_code;
        }
        return $_e_Code[$sub_code];
    }
}