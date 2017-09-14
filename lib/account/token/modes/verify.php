<?php

namespace account\token;

use gateway\mothod as Gateway;
use account\sign\sign as Sign;

class verify extends token
{
    public function run()
    {
        $query = bizContentFilter(array(
            'access_token',
            'get_user'
        ));

        if (empty($query))
            throw new \Exception('account.empty-accesstoken');

        $get_user = false;
        if (!empty($query->get_user))
            $get_user = true;


        $response = self::getUserID($query->access_token, $get_user);
        if ($response) {
            if (is_array($response)) {
                $response['msg'] = 'access_token验证通过';
                $response = apply_filters('account_signin_data', $response);
                Gateway::responseSuccessJSON($response);
            } else {
                Gateway::responseSuccessJSON(array(
                    'msg' => 'access_token验证通过'
                ));
            }

        }
    }

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
            throw new \Exception('account.expired-accesstoken');

        $userid = (int)explode('#', $onlytoken)[0];
        if (empty($userid))
            throw new \Exception('account.expired-accesstoken');

        // 判断onlytoken 是否和当前设备匹配
        if ($onlytoken != parent::creatOnlyToken($userid))
            throw new \Exception('account.expired-accesstoken');

        /*
         * 检测onlytoken的唯一性
         * 参考create.php ~41
         * onlytoken_check_$userid
         */
//        if($cacheDriver->get('onlytoken_check_' . $userid) !== false && $onlytoken != $cacheDriver->get('onlytoken_check_' . $userid))
//            throw new \Exception('account.distance-accesstoken');

        // 检查用户是否存在
        $user = get_user_by('ID', $userid);
        if (empty($user))
            throw new \Exception('account.expired-accesstoken');

        // 每次获取用户id 都延长 7天 token有效期
        $expire = time() + 3600;
        $cacheDriver->set($access_token, $onlytoken, $expire);


        if (!$get_user) {
            return $userid;
        } else {
            return array(
                'userid' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'usermeta' => Sign::getUserMetas($user->ID),
                'access_token' => $access_token
            );
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
        throw new \Exception('account.empty-accesstoken');
    }
}

?>