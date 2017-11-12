<?php
    namespace gateway;

    class code extends mothod
    {
        /**
         * 返回message对应内容
         * @param string $message
         * @return bool|mixed
         */
        public static function _e($message = '')
        {
            $codes = array(
                '10000' => array(
                    'code' => 10000,
                    'msg' => '接口调用成功',
                    'sub_msg' => '接口调用成功',
                    'solution' => ''
                ),
                'gateway.options-success' => array(
                    'code'      => 99999,
                    'msg'       => 'OPTIONS 检查通过',
                    'sub_msg'   => '',
                    'solution'  => '跨域请求时,通常会先发送一次OPTIONS请求进行连接确认,可忽略此消息.'
                ),
                'gateway.close-api' => array(
                    'code'      => 99998,
                    'msg'       => '此接口不对外开放',
                    'sub_msg'   => '',
                    'solution'  => ''
                ),
                'gateway.empty-gateway' => array(
                    'code' => 10001,
                    'msg' => '接口网关配置不能留空',
                    'sub_msg' => '请重新配置你的接口网关',
                    'solution' => '重新设置后台接口网关'
                ),
                'gateway.empty-method' => array(
                    'code' => 10002,
                    'msg' => '错误的请求地址',
                    'sub_msg' => '当前的接口请求地址有误,请检查后再试',
                    'solution' => '重新检查你的请求地址是否有误'
                ),
                'gateway.method-undefined' => array(
                    'code' => 10003,
                    'msg' => '',
                    'sub_msg' => '',
                    'solution' => ''
                ),
                'gateway.error-request' => array(
                    'code' => 10004,
                    'msg' => '不支持的 Content-Type 类型',
                    'sub_msg' => '推荐使用 application/json',
                    'solution' => '尝试重新编写查询语句'
                ),
                'gateway.empty-run' => array(
                    'code' => 10005,
                    'msg' => '接口没有响应',
                    'sub_msg' => '接口执行成功,但没有返回相关内容',
                    'solution' => '请检查接口配置是否正确'
                ),
                'gateway.empty-request' => array(
                    'code' => 10006,
                    'msg' => '接口请求格式有误',
                    'sub_msg' => '接口请求格式有误',
                    'solution' => '请检查接口配置是否正确'
                ),
                'gateway.method-discard' => array(
                    'code' => 10007,
                    'msg' => '接口已被废弃',
                    'sub_msg' => '该接口已被废弃停止使用',
                    'solution' => '请检查接口配置是否正确'
                ),
                'gateway.error-openssl' => array(
                    'code' => 10007,
                    'msg' => '未配置OpenSSL',
                    'sub_msg' => '',
                    'solution' => '请检查接口配置是否正确'
                ),
                'account.empty-register' => array(
                    'code' => 11001,
                    'msg' => '注册接口未配置',
                    'sub_msg' => '请重新设置你的相关接口配置',
                    'solution' => '重新设置有关账户配置中的相关接口配置'
                ),
                'account.close-register' => array(
                    'code'  => 11002,
                    'msg'   => '注册功能已关闭',
                    'sub_msg' => '',
                    'solution' => '重新设置有关账户配置中的相关注册配置'
                ),
                'account.empty-smsapi' => array(
                    'code' => 11003,
                    'msg' => 'SMS所需接口参数未定义',
                    'sub_msg' => '请重新设置你的相关接口配置',
                    'solution' => '重新设置有关账户配置中的相关短信接口配置'
                ),
                'account.empty-smsservice' => array(
                    'code' => 11004,
                    'msg' => '未定义所需的短信服务商',
                    'sub_msg' => '请重新设置你的相关接口配置',
                    'solution' => '重新设置有关账户配置中的相关短信接口配置'
                ),
                'account.exists-account' => array(
                    'code' => 11005,
                    'msg' => '该账号已被注册',
                    'sub_msg' => '请检查后再试',
                    'solution' => '检查后再试'
                ),
                'account.error-email' => array(
                    'code' => 11006,
                    'msg' => '邮箱格式错误',
                    'sub_msg' => '请检查后再试',
                    'solution' => '检查后再试'
                ),
                'account.empty-register' => array(
                    'code' => 11007,
                    'msg' => '账号注册所需资料不能为空',
                    'sub_msg' => '请检查后再试',
                    'solution' => '检查后再试'
                ),
                'account.error-create' => array(
                    'code' => 11008,
                    'msg' => '账号创建错误',
                    'sub_msg' => '请稍后再试',
                    'solution' => '请稍后再试'
                ),
                'account.exists-email' => array(
                    'code' => 11009,
                    'msg' => '邮箱已被使用',
                    'sub_msg' => '请检查后再试',
                    'solution' => '检查后再试'
                ),
                'account.close-login' => array(
                    'code' => 11010,
                    'msg'   => '已关闭账户登陆功能',
                    'sub_msg' => '请稍后再试',
                    'solution' => '请稍后再试'
                ),
                'account.empty-login' => array(
                    'code' => 11011,
                    'msg' => '登陆接口未配置',
                    'sub_msg' => '请重新设置你的相关接口配置',
                    'solution' => '重新设置有关账户配置中的相关接口配置'
                ),
                'account.empty-type' => array(
                    'code' => 11012,
                    'msg'   => '登陆/注册的方式接口未配置',
                    'sub_msg' => '请重新设置你的相关接口配置',
                    'solution' => '重新设置有关账户配置中的相关接口配置'
                ),
                'account.empty-type-custom' => array(
                    'code' => 11013,
                    'msg'   => '还没有配置登陆方式',
                    'sub_msg' => '请重新设置你的接口配置中的type属性',
                    'solution' => '重新设置有关账户配置中的相关接口配置或接口数据'
                ),
                'account.error-accountisemail' => array(
                    'code' => 11014,
                    'msg'   => '账号不可使用邮箱格式注册',
                    'sub_msg' => '请重新设置',
                    'solution' => '账号不得使用邮箱注册,如果需要使用邮箱,请额外填写email字段'
                ),
                'account.error-type' => array(
                    'code' => 11015,
                    'msg'   => '账号登陆/注册接口未配置或接口不存在',
                    'sub_msg' => '请重新设置',
                    'solution' => '重新设置有关账户配置中的相关接口配置'
                ),
                'account.empty-privatekey' => array(
                    'code' => 11016,
                    'msg'   => 'RSA加密配置有误',
                    'sub_msg' => '请检查你的RSA加密配置',
                    'solution' => '请检查你的RSA加密配置'
                ),
                'account.error-private' => array(
                    'code' => 11017,
                    'msg'   => 'RSA私钥错误',
                    'sub_msg' => '请检查你的RSA加密配置',
                    'solution' => '请检查你的RSA加密配置'
                ),
                'account.error-public' => array(
                    'code' => 11018,
                    'msg'   => 'RSA公钥错误',
                    'sub_msg' => '请检查你的RSA加密配置',
                    'solution' => '请检查你的RSA加密配置'
                ),
                'account.empty-accesstoken' => array(
                    'code' => 11019,
                    'msg'   => '账户 AccessToken 不得留空',
                    'sub_msg' => '请重新登陆获取accesstoken',
                    'solution' => '重新登陆获取accesstoken'
                ),
                'account.expired-accesstoken' => array(
                    'code' => 11020,
                    'msg'   => '账户 AccessToken 已过期',
                    'sub_msg' => '请重新登陆获取accesstoken',
                    'solution' => '重新登陆获取accesstoken'
                ),
                'account.empty-register-query' => array(
                    'code' => 11021,
                    'msg'   => '注册所需的资料不全',
                    'sub_msg' => '请检查后再试',
                    'solution' => '重新填写或提交注册信息'
                ),
                'account.empty-login-query' => array(
                    'code' => 11022,
                    'msg'   => '登陆所需的资料不全',
                    'sub_msg' => '请检查后再试',
                    'solution' => '重新填写或提交登陆信息'
                ),
                'account.error-login-account' => array(
                    'code' => 11023,
                    'msg'   => '账号未注册或格式错误',
                    'sub_msg' => '请检查后再试',
                    'solution' => '重新填写或提交登陆信息'
                ),
                'account.error-login-email' => array(
                    'code' => 11024,
                    'msg'   => '邮箱未注册或格式错误',
                    'sub_msg' => '请检查后再试',
                    'solution' => '重新填写或提交登陆信息'
                ),
                'account.error-login-password' => array(
                    'code' => 11025,
                    'msg'   => '账号或密码错误,请检查后再试',
                    'sub_msg' => '请检查后再试',
                    'solution' => '重新填写或提交登陆信息'
                ),
                'account.empty-userlogin' => array(
                    'code' => 11026,
                    'msg'   => '尝试登陆的账号不存在',
                    'sub_msg' => '请检查后再试',
                    'solution' => '重新填写或提交登陆信息'
                ),
                'account.distance-accesstoken' => array(
                    'code' => 11027,
                    'msg'   => '账号在其他处登录',
                    'sub_msg' => '您的账号在其他设备登录,当前设备已断开.',
                    'solution' => '重新填写或提交登陆信息'
                ),
                'cache.empyt-cachetype' => array(
                    'code'  => 12001,
                    'msg'   => '缓存系统未配置或无效',
                    'sub_msg' => '请重新设置你的相关接口配置',
                    'solution' => '重新设置有关接口配置中的相关缓存接口配置'
                ),
                'cache.empyt-memcached' => array(
                    'code' => 12002,
                    'msg'   => 'Memcached 未配置或无效',
                    'sub_msg' => '请重新设置你的Memcached相关接口配置',
                    'solution' => '重新设置有关接口配置中的相关缓存接口配置'
                ),
                'cache.empyt-memcache' => array(
                    'code' => 12002,
                    'msg'   => 'Memcache 未配置或无效',
                    'sub_msg' => '请重新设置你的Memcache相关接口配置',
                    'solution' => '重新设置有关接口配置中的相关缓存接口配置'
                ),
                'func.empyt-function' => array(
                    'code' => 13001,
                    'msg'   => '相关函数不存在',
                    'sub_msg' => '执行的相关函数不存在或为引用,请检查后再试',
                    'solution' => '检查函数是否配置或正确引用'
                ),
                'class.empyt-class' => array(
                    'code' => 13001,
                    'msg'   => '相关函数不存在',
                    'sub_msg' => '执行的相关函数不存在或为引用,请检查后再试',
                    'solution' => '检查函数是否配置或正确引用'
                ),
                'func.error-function' => array(
                    'code' => 13002,
                    'msg'   => '函数执行错误',
                    'sub_msg' => '函数执行错误,请检查后再试',
                    'solution' => '检查函数是否配置或正确引用'
                ),
                'sms.empyt-service' => array(
                    'code'  => 15001,
                    'msg'   => '短信接口未配置或无效',
                    'sub_msg' => '请重新设置你的相关接口配置',
                    'solution' => '重新设置有关接口配置中的相关短信接口配置'
                ),
                'sms.error-phonenumber' => array(
                    'code'  => 15002,
                    'msg'   => '无效的手机号码',
                    'sub_msg' => '请重新输入正确的11位手机号',
                    'solution' => '重新输入'
                ),
                'sms.error-send' => array(
                    'code'      => 15003,
                    'msg'       => '发送失败',
                    'sub_msg'   => '请稍后再试',
                    'solution'  =>'请稍后再试'
                ),
                'sms.business-limit-control' => array(
                    'code'      => 15004,
                    'msg'       => '发送频率过快',
                    'sub_msg'   => '请稍后再试',
                    'solution'  =>'请稍后再试'
                ),
                'sms.error-smscode' => array(
                    'code'      => 15005,
                    'msg'       => '验证码错误',
                    'sub_msg'   => '请稍后再试',
                    'solution'  =>'请稍后再试'
                )
            );

            $codes = apply_filters('update_gateway_codes',$codes);

            foreach ($codes as $code_key => $code){
                if($code_key == $message){
                    $code['sub_code'] = $message;

                    if(get_AppendMsg($message)){
                        $append_message = get_AppendMsg($message);
                        $code = array_merge($code,$append_message);
                        clear_AppendMsg();
                    }
                    // 将用户的请求包含在返回的内容中
                    global $bizContent;
                    $code['request'] = $bizContent;

                    return $code;
                }
            }
            return false;
        }
    }