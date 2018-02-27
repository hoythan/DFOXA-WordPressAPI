<?php

namespace tools\email;

use Respect\Validation\Validator as Validator;

class VerifyCode extends sendEmail
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 发送事件验证码邮件
     * @param $email 收件人
     * @param $user_nickname 用户昵称
     * @param $event 事件名称,一般为登录/注册/找回密码
     * @param null $code 验证码
     * @return bool 是否发送成功
     */
    public function sendVerifyCode($email, $user_nickname, $event, $code = null)
    {
        if (!Validator::email()->validate($email))
            dfoxaError('account.error-email');

        // 验证邮件发送周期
        if ($this->CacheObj->get($email, 'EmailVerifyCodeExpire', false) !== false) {
            $verifyCodeExpireTimestamp = $this->CacheObj->get($email, 'EmailVerifyCodeExpire', false);
            dfoxaError('account.cooling-email', array('expire' => $verifyCodeExpireTimestamp, 'expire_time' => date('r', $verifyCodeExpireTimestamp)));
        }

        // 生成验证码
        if (empty($code))
            $code = rand(100000, 999999);

        $expire = 1800;
        $resend_expire = 300;
        // 发送之前clear缓存
        $this->clearVerifyCode($email);
        // 发送验证码
        $this->CacheObj->set($email, $code, 'EmailVerifyCode', $expire);
        $this->CacheObj->set($email, time() + $resend_expire, 'EmailVerifyCodeExpire', $resend_expire);

        $subject = '您的 ' . get_option('dfoxa_t_email_param_appname') . ' 账户验证码';
        $sendTo = $email;
        $sendBody = parent::filterParam(dirname(__DIR__) . '/templates/verifycode.theme', array(
            'user_nickname' => $user_nickname,
            'query_event' => $event,
            'user_email' => $email,
            'verify_code' => $code,
            'code_expire' => (1800 / 60) . '分钟'
        ));

        $request = parent::send($subject, $sendTo, $sendBody);

        // 发送失败时,清空发件时间等限制
        if (!$request) {
            $this->clearVerifyCode($email);
            dfoxaError('account.senderror-email');
        }

        return true;
    }

    /**
     * 验证邮件验证码
     * @param $email 收件人
     * @param $code 验证码
     * @return bool
     */
    public function checkVerifyCode($email, $code)
    {
        echo '_';
        echo $this->CacheObj->get($email, 'EmailVerifyCode', false);
        exit;
        if ((int)$this->CacheObj->get($email, 'EmailVerifyCode', false) === (int)$code) {
            return true;
        }
        return false;
    }

    public function clearVerifyCode($email)
    {
        $this->CacheObj->delete($email, 'EmailVerifyCode');
        $this->CacheObj->delete($email, 'EmailVerifyCodeExpire');
        return true;
    }
}