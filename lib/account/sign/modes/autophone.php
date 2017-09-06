<?php

namespace account\sign;

use account\token\create as tokenCreate;
use cached\cache;
use gateway\mothod as Gateway;
/*
 * 手机自动验证
 */
class autophone extends sign
{
    /*
     * 注册
     */
    public function register()
    {
        $this->auto();
    }

    /*
     * 登陆
     */
    public function login()
    {
        $this->auto();
    }

    public function auto()
    {
        $query = bizContentFilter(array(
            "phone",
            "smscode"
        ));

        if(empty($query->smscode)){
            // 发送登陆验证码
//            $SMSObj = new \tools\sms\sms();
//            $SMSObj->send('login',$query->phone);
//
//            Gateway::responseSuccessJSON(array(
//                'msg' => '验证短信发送成功'
//            ));
        }

        $cacheDriver = new \cached\cache();
        $cacheKey = get_UserOnlyIdent('login'.$query->phone);

        $verifyData = $cacheDriver->get($cacheKey);

        // 判断验证码是否有申请?
        if(empty($verifyData))
            throw new \Exception('sms.error-smscode');

        // 验证验证码
        if(empty($verifyData['code']) || $verifyData['code'] != $query->smscode)
            throw new \Exception('sms.error-smscode');

        // 验证通过,清空验证码
        $cacheDriver->delete($cacheKey);

        $userid = get_usermeta_userid('phone',$query->phone);
        // 判断用户是否存在
        if($userid){
            // 用户存在,登录
            parent::_loginAccount('id',$userid);
        }else{
            // 用户不存在,注册账号
            $userid = parent::_registerAccount('user_' . get_RandStr());
            update_user_meta($userid,'phone',$query->phote);
            parent::_loginAccount('id',$userid);
        }
    }

    public function test()
    {
        echo 'test account\sign\autophone';
    }
}