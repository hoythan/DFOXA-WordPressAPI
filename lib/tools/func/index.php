<?php

namespace tools;

class func
{
    public function run()
    {
        $query = bizContentFilter(array(
            'require_onces',
            'classes',
            'function',
            'args'
        ));

        // 加载必要文件
        if (empty($query->require_onces) && count($query->require_onces) > 0) {
            foreach ($query->require_onces as $file) {
                if (file_exists(ABSPATH . $file)) {
                    require_once(ABSPATH . $file);
                }
            }
        }

        // 检查函数是否存在
        if (empty($query->function) && !function_exists($query->function))
            dfoxaError('func.empyt-function');

        $result = call_user_func_array($query->function, $query->args);
        
        // 执行函数
        dfoxaGateway();
    }
}