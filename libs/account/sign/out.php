<?php

namespace account\sign;

use account\token\verify as Verify;

class out extends sign
{
    public function run()
    {
        //权限验证
        $user_id = Verify::getSignUserID();
        $user = get_user_by('id', $user_id);

        wp_logout();

        do_action('dfoxa_account_signout', $user);
        dfoxaGateway();
    }
}