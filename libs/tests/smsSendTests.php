<?php

namespace tests;

use tools\sms\sendSMS;

class smsSendTests
{
    public function run()
    {
        $SendSMS = new sendSMS();

        $SendSMS->send('18858858581',array(
            'template_code' => 'SMS_93470003',
            'sign_name' => '清莹网口碑客资',
            'param' => array()
        ));



        exit;
    }
}