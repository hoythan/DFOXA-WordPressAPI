<?php

namespace account\token;

use gateway\mothod as Gateway;
use account\sign\sign as Sign;

class verify extends token
{
    public function run()
    {
        $access_token = self::getAccessToken();

        $response = self::getUserID($access_token, self::isGetUser());
        if ($response) {
            if (is_array($response)) {
                $response['msg'] = 'access_token验证通过';
                Gateway::responseSuccessJSON($response);
            } else {
                Gateway::responseSuccessJSON(array(
                    'msg' => 'access_token验证通过'
                ));
            }

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
            $access_token = self::getAccessToken();
        }

        return self::getUserID($access_token, $get_user);
    }

    public static function getUserID($access_token, $get_user = false)
    {
        $cacheDriver = new \cached\cache();

        // 从缓存中根据accesstoken获取onlytoken
        $onlytoken = $cacheDriver->get($access_token);

        if (empty($onlytoken))
            dfoxaError('account.expired-accesstoken');

        $userid = (int)explode('#', $onlytoken)[0];
        if (empty($userid))
            dfoxaError('account.expired-accesstoken');

        // 判断onlytoken 是否和当前设备匹配
        if ($onlytoken != parent::creatOnlyToken($userid))
            dfoxaError('account.expired-accesstoken');

        /*
         * 检测onlytoken的唯一性
         * 参考create.php ~41
         * onlytoken_check_$userid
         */
//        if($cacheDriver->get('onlytoken_check_' . $userid) !== false && $onlytoken != $cacheDriver->get('onlytoken_check_' . $userid))
//            dfoxaError('account.distance-accesstoken');

        // 检查用户是否存在
        $user = get_user_by('ID', $userid);
        if (empty($user))
            dfoxaError('account.expired-accesstoken');

        // 设置token
        $cacheDriver->set($access_token, $onlytoken, parent::expireTime());

        if (!$get_user) {
            return $userid;
        } else {
            return Sign::_getUserAccount('ID', $user->ID);
        }


    }

    public static function getAccessToken()
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


    public static function isGetUser()
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