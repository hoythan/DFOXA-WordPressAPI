<?php

namespace account\token;

use account\sign\sign as Sign;

class verify extends token
{
    public function run()
    {
        $access_token = self::_getAccessToken();

        $expire = parent::_expireTime();
        $userid = self::getUserID($access_token, $expire);

        if (self::_isGetUser()) {
            $ret = Sign::signInAccount(array(
                'type' => 'id',
                'field' => $userid
            ), false, false);
            $ret['expire'] = time() + $expire;
            dfoxaGateway($ret);
        } else {
            dfoxaGateway(array(
                'expire' => time() + $expire,
                'sub_msg' => 'access_token验证通过'
            ));
        }

    }

    /**
     * 根据access_token获取指定用户
     * @param string $access_token 留空表示获取当前登录用户
     * @param bool $get_user 是否获取用户的信息
     * @return array|int 返回用户信息或用户ID
     */
    public static function check($access_token = '', $get_user = false)
    {
        if ($access_token == '') {
            $access_token = self::_getAccessToken();
        }

        $userid = self::getUserID($access_token);
        if ($get_user) {
            // 登录用户账号
            return Sign::signInAccount(array(
                'type' => 'id',
                'field' => $userid
            ), false, false);
        } else {
            return $userid;
        }

    }

    /**
     * 通过 access_token 获取用户ID
     * @param $access_token
     * @param null $expire 过期时间
     * @return int 用户ID
     */
    public static function getUserID($access_token, $expire = null)
    {
        $cacheDriver = new \cached\cache();

        // 从缓存中根据 accesstoken 获取 onlytoken
        $onlytoken = $cacheDriver->get($access_token, 'access_token');

        if (empty($onlytoken))
            dfoxaError('account.expired-accesstoken');

        $userid = (int)explode('#', $onlytoken)[0];
        if (empty($userid))
            dfoxaError('account.expired-accesstoken');

        // 判断 onlytoken
        if ($onlytoken != parent::_creatOnlyToken($userid))
            dfoxaError('account.expired-accesstoken');

        $group_key = 'access_token_' . $userid;
        if ($cacheDriver->get($onlytoken, $group_key) !== 'yes') {
            $cacheDriver->delete($access_token, $group_key);
            dfoxaError('account.expired-accesstoken', array('logs' => '强制下线'));
        }

        // 更新 access_token 过期时间
        $expire = $expire === null ? parent::_expireTime() : (int)$expire;
        $cacheDriver->set($access_token, $onlytoken, 'access_token', $expire);

        return $userid;
    }

    /**
     * 通过各种方式获取用户可能提交到的 access_token
     * @return mixed 用户access_token
     */
    private static function _getAccessToken()
    {
        $query = bizContentFilter(array(
            'access_token'
        ));

        // 如果请求参数中有access_token则直接返回参数中的token
        if (!empty($query->access_token))
            return $query->access_token;

        // 从 URL地址 中获取
        if (isset($_GET['access_token']))
            return $_GET['access_token'];

        // 从 Cookie 中获取
        if (isset($_COOKIE['access_token']))
            return $_COOKIE['access_token'];

        // ...

        // 报错
        dfoxaError('account.empty-accesstoken');
    }


    /**
     * 判断请求中是否需要获取用户信息或仅需获取用户ID
     * @return bool
     */
    private static function _isGetUser()
    {
        $query = bizContentFilter(array(
            'get_user'
        ));

        // 如果请求参数中有access_token则直接返回参数中的token
        if (!empty($query->get_user) && $query->get_user === true)
            return true;

        // 从 URL地址 中获取
        if (isset($_GET['get_user']) && $_GET['get_user'] === 'true')
            return true;

        return false;
    }
}

?>