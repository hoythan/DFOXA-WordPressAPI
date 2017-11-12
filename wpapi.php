<?php
/*
Plugin Name: DFOXA-WordPressAPI
Plugin URI: https://doofox.cn
Description: WordPress API 扩展插件,允许使用 WordPres 开发标准的API接口,为基于 WordPress 的前后端分离项目实现便捷轻快的后端开发体验. 
Version: 1.0.0
Author: DooFox. Inc,
Author URI: https://doofox.cm
License:MIT License
DFOXWP Version:	2.0

MIT License

Copyright (c) 2016 DooFox. Inc,

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/
define('DFOXA_PLUGIN_URL', plugins_url('', __FILE__));
define('DFOXA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DFOXA_PLUGINS', dirname(__FILE__) . '/plugins');
define('DFOXA_SEP', DIRECTORY_SEPARATOR);

require 'pages/function.php';
require 'vendor/autoload.php';

function reload_dfoxa_wpapi()
{
    $gateway = new \gateway\mothod();
    $gateway->run();
}

add_action('wp', 'reload_dfoxa_wpapi');

/*
	后台
 */
global $dfoxa_default;
$dfoxa_default = array(
    'dfoxa_uniquecode' => '',
    'dfoxa_t_rsa_public' => '',
    'dfoxa_t_rsa_private' => '',
    'dfoxa_gateway' => 'gateway.do',
    'dfoxa_cache_type' => 'wp',
    // 账户配置
    'dfoxa_account_login' => 'open',
    'dfoxa_account_reg' => 'open',
    'dfoxa_account_type' => 'account',
    'dfoxa_t_account_query_usermetakey' => '',
    'dfoxa_t_account_edit_usermetakey' => '',
    'dfoxa_account_access_token_expire' => '3600',
    // 短信配置
    'dfoxa_sms_service' => '',
    'dfoxa_sms_appkey' => '',
    'dfoxa_sms_appsecret' => '',
    // 媒体库
    'dfoxa_media' => 'open',
    'dfoxa_media_size' => '2048',
    'dfoxa_media_type' => '',
    'dfoxa_media_user' => 'select',
    'dfoxa_media_user_role' => array(),
    'dfoxa_media_url' => '',
);
// ADMIN
require_once('dfox_wp/load.php');
add_filter('dfox_wp_setting_add_page', 'dfoxa_add_settingpage', 1, 1);
function dfoxa_add_settingpage($pageArr)
{
    $pageArr['dfoxa_wpapi'] = array(
        'page_title' => 'DFOXA WPAPI设置',
        'menu_title' => 'WPAPI设置',
        'menu_slug'  => 'wpapi',
        'position'   => 10,
        'pages' => array(
            'api' => array(
                'function' => 'dfoxa_api_page',
                'menu_title' => '接口配置',
                'init' => DFOXA_PLUGIN_DIR . '/pages/page.init.php',
                'page' => DFOXA_PLUGIN_DIR . '/pages/api/page.php'
            ),
            'account' => array(
                'function' => 'dfoxa_account_page',
                'menu_title' => '账户配置',
                'init' => DFOXA_PLUGIN_DIR . '/pages/page.init.php',
                'page' => DFOXA_PLUGIN_DIR . '/pages/account/page.php',
                'child' => array(
                    'sms' => array(
                        'function' => 'dfoxa_sms_page',
                        'menu_title' => '手机短信配置',
                        'init' => DFOXA_PLUGIN_DIR . '/pages/page.init.php',
                        'page' => DFOXA_PLUGIN_DIR . '/pages/account/sms.php'
                    )
                )
            ),
            'media' => array(
                'function' => 'dfoxa_media_page',
                'menu_title' => '媒体库',
                'page' => DFOXA_PLUGIN_DIR . '/pages/media/page.php',
                'init' => DFOXA_PLUGIN_DIR . '/pages/page.init.php'
            ),
            'plugins' => array(
                'function' => 'dfoxa_plugins_page',
                'menu_title' => '插件管理',
                'page' => DFOXA_PLUGIN_DIR . '/pages/plugins/page.php',
                'init' => DFOXA_PLUGIN_DIR . '/pages/plugins/page.php'
            )
        )
    );
    return $pageArr;
}
?>