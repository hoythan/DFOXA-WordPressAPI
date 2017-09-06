<?php
/**
 * 函数文件,它会随着WordPress自动加载
 *
 * 你可以在这里操作一些插件数据库创建等等操作
 */

add_filter('dfox_wp_setting_add_page', 'dfoxa_add_settingpageee', 10, 1);
function dfoxa_add_settingpageee($pageArr){
    /*
     * 创建或插入菜单到插件
     */
//    print_r($pageArr);
    return $pageArr;
}