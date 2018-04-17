<?php

namespace tools\logs;

use tools\sql\DFOXA_Sql;

class add
{
    private $group = '';
    private $logsType = '';
    private $event = '';

    function __construct($group, $logsType, $event)
    {
        $this->group = $group;
        $this->logsType = $logsType;
        $this->event = $event;
    }


    /**
     * 设置 Debug 日志
     * - 测试 Debug 信息
     */
    public function Debug($title, $content = '')
    {
        $this->add('debug', $title, $content);
    }

    /**
     * 设置 Info 日志
     * - 感兴趣的事件或信息，如用户登录信息，SQL日志信息
     */
    public function Info($title, $content = '')
    {
        $this->add('info', $title, $content);
    }

    /**
     * 设置 Notice 日志
     * - 普通但重要的事件信息,需开发人员留意
     */
    public function Notice($title, $content = '')
    {
        $this->add('notify', $title, $content);
    }

    /**
     * 设置 Warning 日志
     * - 异常事件但不是错误。例子:使用不赞成的API，使用不恰当的API，使用不存在的API接口，不需要的东西不一定是错误的。
     */
    public function Warning($title, $content = '')
    {
        $this->add('warning', $title, $content);
    }

    /**
     * 设置 Error 日志
     * - 用户操作的时出现的错误，通常是一些应该被记录和监视的信息
     */
    public function Error($title, $content = '')
    {
        $this->add('error', $title, $content);
    }

    /**
     * 设置 Alert 日志
     * - 至关重要的信息。示例:外部接口不可用，意外异常，数据库异常等
     */
    public function Alert($title, $content = '')
    {
        $this->add('alert', $title, $content);
    }

    /**
     * 设置 Emergency 日志
     * - 紧急情况，系统无法使用
     */
    public function Emergency($title, $content = '')
    {
        $this->add('emergency', $title, $content);
    }

    /**
     * 设置日志
     * @param string $level 安全等级（不区分大小写）
     * @param string $title 标题（描述）
     * @param string $log 详情日志
     * @return bool
     */
    private function add($level, $title, $content = '')
    {
        $level = strtoupper($level);
        if (!in_array($level, array('DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'ALERT', 'EMERGENCY')))
            return false;

        // 客户端信息
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $ip = get_ClientIP();
        $date = date('Y-m-d h:i:s');

        switch ($this->logsType) {
            case 'db':
                $this->updateDB($level, $title, $content, $agent, $ip, $date);
                break;
            default:
                $this->updateDB($level, $title, $content, $agent, $ip, $date);
                break;
        }
    }


    /**
     * 设置日志到数据库
     *
     * @param string $level 安全等级
     * @param string $title 标题（描述）
     * @param string $log 详情日志
     * @return bool
     */
    private function updateDB($level, $title, $log = '', $agent = '', $ip = '', $date = '')
    {
        global $wpdb;
        $wpdb->logs = $wpdb->get_blog_prefix() . 'logs';

        $filters = array(
            'level' => '%s',
            'group' => '%s',
            'event' => '%s',
            'title' => '%s',
            'log' => '%s',
            'user_ip' => '%s',
            'user_agent' => '%s',
            'create_date' => '%s'
        );

        $data = array(
            'level' => $level,
            'group' => $this->group,
            'event' => $this->event,
            'title' => $title,
            'log' => $log,
            'user_agent' => $agent,
            'user_ip' => $ip
        );

        $Sql = new DFOXA_Sql();
        $response = $Sql->add($wpdb->logs, $data, $filters);
        return $response === false ? false : true;
    }
}