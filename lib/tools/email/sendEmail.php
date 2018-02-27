<?php

namespace tools\email;

use Respect\Validation\Validator as Validator;

/*
 * 短信验证码接口
 *
 * send 发送短信
 * sends 群发短信
 */

class sendEmail
{
    public $EmailObj;
    public $CacheObj;

    function __construct()
    {
        add_filter('wp_mail_content_type', array($this, '_set_html_mail_content_type'));
        add_action('phpmailer_init', array($this, '_phpmailer_smtp'), 1);

        // 检查环境是否允许

        $this->CacheObj = new \cached\cache();
        $this->CacheObj->set('cache_test', 'success', 'test', 10);
        if ($this->CacheObj->get('cache_test', 'test') !== 'success')
            dfoxaError('account.error-email', array('msg' => '验证码发送出现一些问题'));
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
    public function send(
        $subject,
        $sendTo = array(),
        $sendBody
    )
    {
        $request = wp_mail($sendTo, $subject, $sendBody);
        if ($request) {
            return true;
        }
        return false;
    }

    public function _set_html_mail_content_type()
    {
        return 'text/html';
    }

    public function _phpmailer_smtp($phpmailer)
    {
        $phpmailer->Mailer = "smtp";
        $phpmailer->From = get_option('dfoxa_t_email_param_sendfrom_email');
        $phpmailer->FromName = get_option('dfoxa_t_email_param_sendfrom_name');
        $phpmailer->Sender = $phpmailer->From; //Return-Path
        $phpmailer->AddReplyTo($phpmailer->From, $phpmailer->FromName); //Reply-To
        $phpmailer->Host = get_option('dfoxa_email_host');
        $phpmailer->SMTPSecure = get_option('dfoxa_email_secure');
        $phpmailer->Port = get_option('dfoxa_email_port');
        $phpmailer->SMTPAuth = (get_option('dfoxa_email_smtpauth') == "yes") ? TRUE : FALSE;
        if ($phpmailer->SMTPAuth) {
            $phpmailer->Username = get_option('dfoxa_email_username');
            $phpmailer->Password = get_option('dfoxa_email_password');
        }
    }

    /*
     * 模版参数处理
     */
    public function filterParam($template_dir, $params = array())
    {
        // 检查模板是否存在
        if (!file_exists($template_dir))
            dfoxaError('account.notfind-theme-email');

        $html = file_get_contents($template_dir);

        // 读入并替换模板参数
        $html = $this->replaceFilterParam($html, $params);

        // 重复替换一次以处理模板参数中的模板参数
        if (Validator::in($html)->validate('{{')) {
            $html = $this->replaceFilterParam($html, $params);
        }

        return $html;
    }

    public function replaceFilterParam($html, $params = array())
    {
        if (empty($params['query_addess'])) {
            $IP = new \Zhuzhichao\IpLocationZh\Ip();
            $addess = $IP::find(get_ClientIP());

            $IPAddess = implode('_', array($addess[0], $addess[1], $addess[2]));

            $params['query_addess'] = $IPAddess;
            $params['query_addessip'] = get_ClientIP();
        }

        if (empty($params['app_name']))
            $params['app_name'] = stripslashes(get_option('dfoxa_t_email_param_appname'));

        if (empty($params['logo']))
            $params['logo'] = stripslashes(get_option('dfoxa_t_email_param_logo'));

        if (empty($params['time_year']))
            $params['time_year'] = date('Y');

        if (empty($params['welcome']))
            $params['welcome'] = stripslashes(get_option('dfoxa_t_email_param_welcome'));

        if (empty($params['inscription']))
            $params['inscription'] = stripslashes(get_option('dfoxa_t_email_param_inscription'));

        if (empty($params['copyright']))
            $params['copyright'] = stripslashes(get_option('dfoxa_t_email_param_copyright'));

        if (empty($params['footerlinks']))
            $params['footerlinks'] = stripslashes(get_option('dfoxa_t_email_param_footlinks'));

        foreach ($params as $key => $param) {
            $html = str_replace('{{' . $key . '}}', $param, $html);
        }

        return $html;
    }
}