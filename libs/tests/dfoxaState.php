<?php

namespace tests;

class dfoxaState
{
    public function run()
    {
        $query = bizContentFilter(array(
            'check_text'
        ));

        if(!empty($query->check_text)){
            echo $query->check_text;
            exit;
        }else{
            dfoxaGateway(array(
                'hello' => '看起来这个接口已经准备就绪了.',
                'api_ready' => true
            ));
        }

    }
}