<?php

namespace Tests;

use gateway\mothod as Gateway;
class dfoxaState
{
    public function run()
    {
        Gateway::responseSuccessJSON(array(
            'hello' => '看起来这个接口已经准备就绪了.'
        ));
    }
}