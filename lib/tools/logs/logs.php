<?php

namespace tools\logs;

class logs
{
    public $logsType = 'db';
    public $logGroup = '';

    function __construct($group = '')
    {
        // 配置日志事件
        $this->logGroup = $group;
        // 记录方式
        $this->logsType = 'db';
    }

    public function add($event = '')
    {
        return new add($this->logGroup, $this->logsType, $event);
    }

    public function get()
    {
        return new get($this->logGroup, $this->logsType);
    }
}