<?php

namespace tools;

class classes
{
    public function run(){
        $query = bizContentFilter(array(
            'require_onces',
            'classes',
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
        if(empty($query->function) && !function_exists($query->function))
            dfoxaError('func.empyt-function');


        // 执行函数

    }
}