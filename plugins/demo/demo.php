<?php
/*
    Plugin Name:Plugin Demo
    Plugin URI:https://doofox.cn
    Author:DooFox Inc
    Author URI:https://doofox.com
    Version:0.1
    Description:DFOXA-WPAPI 的插件示例
    Tags:demo,示例,示范,演示,DooFox
    Namespace:doofox\demo
 */

namespace doofox\demo;
class HelloWorld
{

    public function run()
    {
        echo 'Hello World!~';
        exit;
    }
}

/*
 * ============================
 *          插件编写教程
 * ============================
 *
 * 在编写插件之前,你需要配置你的插件信息,尤其是 Plugin Name 插件名称以及 Namespace 插件命名空间
 * + Plugin Name    插件名称
 * - Plugin URI     插件官方网址
 * - Author         插件作者
 * - Author URI     插件作者的网址
 * - Version        插件版本号
 * - Description    插件描述
 * - Tags           插件描述标签
 * + Namespace      插件命名空间,如果命名空间配置不正确,你的插件可能不会被调用
 *
 * ============================
 *          插件注意事项
 * ============================
 *
 * 1.你的插件类中必须有一个 public 名为 run 的函数,这是插件的入口函数,当访问插件时,将会触发执行入口函数
 * 2.插件报错请使用 dfoxaError('sub_code'); 在插件的任何未知使用它,将会触发接口返回报错内容,详细使用方式请参考 接口报错
 * 3.要获取用户的请求内容,请使用 bizContentFilter 函数获取,它可以提供一个所需参数
 *
 *      bizContentFilter(array('username','userage'))  |  获取用户提交的信息中的username 和 userage
 *
 * 4.要获取当前登录用户的信息,请使用
 *

 * ============================
 *          接口返回内容
 * ============================
 *
 *
 *
 *
 * ============================
 *            接口报错
 * ============================
 */