<?php

namespace account\sign;

use Respect\Validation\Validator as Validator;
use account\token\create as AccessToken;
use account\token\verify as Verify;

abstract class sign
{
    public function run()
    {
        dfoxaError('gateway.close-api');
    }

    /**
     * 登录账号
     * @param array $args
     *      [type] 登录方式 ['id','login','email','emailcode','phone','phonecode']
     *          - email 和 emailcode 的区别为,email仅表示使用邮箱,但是不发送验证码,emailcode表示邮箱需要验证
     *          - phone 和 phonecode 的区别为,phone仅表示使用手机号,但是不发送验证码,phonecode表示手机号需要验证
     *      [field] 值,例如type为id 则 此处填写用户 ID
     *          - type = 'emailcode' 填写邮箱
     *          - type = 'phonecode' 填写手机号
     *      [value] 登录密码,留空表示不需要验证密码直接登录(危险)
     *          - type = 'emailcode' value 留空表示获取登录验证码,不为空表示验证验证码
     *          - type = 'phonecode' value 留空表示获取登录验证码,不为空表示验证验证码
     *
     * @param bool $checkPassword   是否检查密码,false 表示Value不需要输入登录密码 （emailcode 和 phonecode除外）
     * @param bool $checkSignType   是否检查登录类型,false
     * @param bool $refreshAccessToken 刷新当前登录用户的AccessToken . 私有参数!!!用于 Verify Token 时获取用户信息所用
     */
    public static function signInAccount($args = array(
        'type' => '',
        'field' => '',
        'value' => ''
    ), $checkPassword = true, $checkSignType = true,$refreshAccessToken = true)
    {
        $type = strtolower($args['type']);
        $field = $args['field'];
        $value = $args['value'];
        $user = false;

        // 判断是否后台配置了登录相关设置
        $limit = get_option('dfoxa_account_signin_limit');
        $types = get_option('dfoxa_account_signin_types');

        if (empty($limit) || empty($types))
            dfoxaError('account.empty-login-api');

        // 判断是否允许登录
        if ($limit === 'disable')
            dfoxaError('account.close-login');

        if (!in_array($type, $types) && $checkSignType)
            dfoxaError('account.error-login', array('sub_msg' => '"' . $type . '"是不被允许的登录方式'), 204);

        switch ($type) {
            case 'id':
                // 验证用户ID是否存在
                if (absint($field) < 1)
                    dfoxaError('account.warning-login', array('sub_msg' => '登录账号格式有误'));

                $user = get_user_by('id', $field);

                // 验证账号是否存在
                if (!$user)
                    dfoxaError('account.undefined-login');

                // 验证登录密码是否正确
                self::_checkUserPassword($user, $value, $checkPassword);
                break;
            case 'login':
                // 验证登录账号是否存在
                if (empty($field) || !Validator::stringType()->validate($field))
                    dfoxaError('account.warning-login', array('sub_msg' => '登录账号格式有误'));

                $user = get_user_by('login', $field);

                // 验证账号是否存在
                if (!$user)
                    dfoxaError('account.undefined-login');

                // 验证登录密码是否正确
                self::_checkUserPassword($user, $value, $checkPassword);

                break;
            case 'email':
                // 验证登录账号是否存在
                if (empty($field) || !Validator::email()->validate($field))
                    dfoxaError('account.warning-login', array('sub_msg' => '登录账号格式有误'));

                $user = get_user_by('email', $field);

                // 验证账号是否存在
                if (!$user)
                    dfoxaError('account.undefined-login');

                // 验证登录密码是否正确
                self::_checkUserPassword($user, $value, $checkPassword);
                break;
            case 'emailcode':
                // 验证登录账号是否存在
                if (empty($field) || !Validator::email()->validate($field))
                    dfoxaError('account.warning-login', array('sub_msg' => '登录账号格式有误'));

                $user = get_user_by('email', $field);

                // 验证账号是否存在
                if (!$user)
                    dfoxaError('account.undefined-login');
                // 验证验证码
                $email_username = apply_filters('dfoxa_signin_email_username', $user->display_name, $user);

                $sendEmail = new \tools\email\VerifyCode();
                if (empty($value)) {
                    $sendEmail->sendVerifyCode($field, $email_username, '登录');
                    dfoxaGateway(array('sub_msg' => '邮件发送成功,请前往邮箱查看'));
                }

                if (!$sendEmail->checkVerifyCode($field, $value))
                    dfoxaError('account.errorcode-email');

                // 使验证码无效
                $sendEmail->clearVerifyCode($field);
                break;
//            case 'phone':
//
//                break;
//            case 'phonecode':
//
//                break;
            default:
                dfoxaError('account.error-login', array('sub_msg' => '"' . $type . '"是不被允许的登录方式'), 204);
                break;

        }

        // 拼接参数
        $access_token = $refreshAccessToken === false ? Verify::getSignUserAccessToken() : AccessToken::get($user->ID);
        $responseData = array(
            'user_id' => $user->ID,
            'user_email' => $user->user_email,
            'display_name' => $user->display_name,
            'user_registered' => $user->user_registered,
            'access_token' => $access_token,
            'expire' => time() + AccessToken::_expireTime()
        );

        if (get_user_meta($user->ID, 'user_phone', true) && self::_checkPhoneNumber(get_user_meta($user->ID, 'user_phone', true)))
            $responseData['user_phone'] = get_user_meta($user->ID, 'user_phone', true);


        // 注册登录filter
        $ret = apply_filters('dfoxa_account_signin_response', $responseData);
        return $ret;
    }

    /**
     * 账号注册,在注册成功后会调用 signInAccount 使用ID进行登录并返回相关内容
     * @param array $args
     *      [type] 注册方式 ['login','email','emailcode','phone','phonecode']
     * @param array $create_user
     *      如果注册方式中有填写相关内容,则下方填写无效,例如使用login登录,login值填写无效
     *          - login 为用户登录名,即 login ,留空将随机生成一个
     *          - password 字段非必填项,如不配置则会将密码发送至用户邮箱或手机,取决于用哪种方式进行注册
     *          - email 可留空
     *          - phone 可留空
     */
    public static function signUpAccount($args = array(
        'type' => '',
        'field' => '',
        'value' => ''
    ), $create_user = array(
        'login' => '',
        'password' => '',
        'email' => ''
    ))
    {
        $type = strtolower($args['type']);
        $field = isset($args['field']) ? $args['field'] : '';
        $value = isset($args['value']) ? $args['value'] : '';
        $user_id = false;
        $send_password = false; // 是否需要在欢迎邮件中发送密码给用户

        // 判断是否后台配置了登录相关设置
        $limit = get_option('dfoxa_account_signup_limit');
        $types = get_option('dfoxa_account_signup_types');

        if (empty($limit) || empty($types))
            dfoxaError('account.empty-register-api');

        // 判断是否允许登录
        if ($limit === 'disable')
            dfoxaError('account.close-register');

        if (!in_array($type, $types))
            dfoxaError('account.error-register', array('sub_msg' => '"' . $type . '"是不被允许的注册方式'), 204);

        switch ($type) {
            case 'id':
                break;
            case 'login':
                // 验证登录账号是否存在
                if (empty($field) || !Validator::stringType()->validate($field))
                    dfoxaError('account.warning-register', array('sub_msg' => '注册账号格式有误'));

                $user = get_user_by('login', $field);
                // 验证账号是否存在
                if ($user)
                    dfoxaError('account.warning-register', array('sub_msg' => '该账户已被注册'));

                // 验证用户账号是否符合规范
                if (!validate_username($field)) {
                    dfoxaError('account.warning-register', array('sub_msg' => '用户名格式不正确'));
                }

                // 密码有效验证,如果留空则自动创建密码并发送邮件告知
                if (empty($create_user['password'])) {
                    $create_user['password'] = wp_generate_password();
                    $send_password = true;
                }
                $password = apply_filters('dfoxa_signup_password', $create_user['password']);
                $send_password = apply_filters('dfoxa_signup_send_password', $send_password);
                if ($create_user['password'] !== $password) {
                    // 如果通过hook修改了用户提交的密码,将会发送新密码给用户
                    $send_password = true;
                }

                // 邮箱验证
                if(empty($create_user['email'])){
                    $create_user['email'] = '';
                }else{
                    // 验证邮箱格式是否符合规范

                    // 验证邮箱是否存在
                }

                // 注册账号
                $user_id = wp_create_user($field, $create_user['password'], $create_user['email']);

                break;
            case 'email':
                break;
            case 'emailcode':
                // 验证注册账号是否存在
                if (empty($field) || !Validator::email()->validate($field))
                    dfoxaError('account.warning-register', array('sub_msg' => '注册账号格式有误'));

                // 验证账号是否存在
                if (email_exists($field))
                    dfoxaError('account.warning-register', array('sub_msg' => '该邮箱已被注册'));

                // 验证用户名称是否有效,如果留空则自动生成,否则检测是否符合规范,不符合则报错
                $email_login = $create_user['login'];
                if (empty($create_user['login'])) {
                    $create_user['login'] = '_' . get_RandStr(10, 10);
                    // 确保生成的用户名不存在
                    while (!username_exists($create_user['login']) && !validate_username($create_user['login'])) {
                        $create_user['login'] = '_' . get_RandStr(10, 10);
                    }

                    $email_login = apply_filters('dfoxa_signup_email_login', '新用户', $create_user['login']);
                } else if (!validate_username($create_user['login'])) {
                    dfoxaError('account.warning-register', array('sub_msg' => '用户名格式不正确'));
                } else if (username_exists($create_user['login'])) {
                    dfoxaError('account.warning-register', array('sub_msg' => '该用户名已被注册'));
                }

                // 密码有效验证,如果留空则自动创建密码并发送邮件告知
                if (empty($create_user['password'])) {
                    $create_user['password'] = wp_generate_password();
                    $send_password = true;
                }
                $password = apply_filters('dfoxa_signup_password', $create_user['password']);
                $send_password = apply_filters('dfoxa_signup_send_password', $send_password);
                if ($create_user['password'] !== $password) {
                    // 如果通过hook修改了用户提交的密码,将会发送新密码给用户
                    $send_password = true;
                }

                // 验证验证码
                $sendEmailVerify = new \tools\email\VerifyCode();
                if (empty($value)) {
                    $sendEmailVerify->sendVerifyCode($field, $email_login, '注册');
                    dfoxaGateway(array('msg' => '邮件发送成功', 'sub_msg' => '请前往邮箱查看'));
                }

                if (!$sendEmailVerify->checkVerifyCode($field, $value))
                    dfoxaError('account.errorcode-email');

                // 验证通过,创建用户
                $user_id = wp_create_user($create_user['login'], $create_user['password'], $field);
                if (is_wp_error($user_id))
                    dfoxaError('account.warning-register', array('sub_msg' => $user_id->get_error_message()));

                // 使验证码无效
                $sendEmailVerify->clearVerifyCode($field);

                // 发送欢迎邮件
                $sendEmailWelcome = new \tools\email\Message();
                if ($send_password) {
                    $sendEmailWelcome->sendWelcome($field, $create_user['login'], '注册', $create_user['password']);
                } else {
                    $sendEmailWelcome->sendWelcome($field, $create_user['login'], '注册');
                }
                break;
            case 'phone':
                break;
            case 'phonecode':
                break;
            default :
                break;
        }

        $ret = self::signInAccount(
            array(
                'type' => 'id',
                'field' => $user_id,
            ),
            false, false);

        return $ret;
    }

    /**
     * 找回密码
     *      - 可以先通过id或账号获取邮箱或手机号,再调用本接口
     * @param array $args
     *      [type] 找回方式 ['emailcode','phonecode']
     *          - [emailcode]       表示发送验证邮件到相关邮箱
     *          - [phonecode]       表示发送验证码到相关手机号
     *      [field]
     *          - type = 'emailcode' 填写邮箱
     *          - type = 'phonecode' 填写手机号
     *      [value]
     *          - type = 'emailcode' value 留空表示获取登录验证码,不为空表示验证验证码
     *          - type = 'phonecode' value 留空表示获取登录验证码,不为空表示验证验证码
     */
    public static function forgotAccount($args = array(
        'type' => '',
        'field' => '',
        'value' => ''
    ))
    {

    }


    /**
     * 检查密码是否正确
     * @param $user
     * @param $password
     * @return bool
     */
    private static function _checkUserPassword($user, $password, $checkPassword = true)
    {
        if ($checkPassword === false)
            return true;

        if (empty($password))
            dfoxaError('account.warning-login', array('sub_msg' => '登录密码不可为空'));

        if (!wp_check_password($password, $user->data->user_pass, $user->ID))
            dfoxaError('account.warning-login', array('sub_msg' => '账号或密码有误'));

        return true;
    }


    /**
     * 验证手机号格式
     * @param $phone
     * @return bool
     */
    private static function _checkPhoneNumber($phone)
    {
        return sendSMS::_verifyPhoneNumber($phone);
    }
}

?>