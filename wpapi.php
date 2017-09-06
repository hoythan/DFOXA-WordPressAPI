<?php
/*
Plugin Name:	DFOXA WPAPI
Plugin URI:		https://doofox.com
Description:	WordPress API插件,集成基本文章管理,用户管理等接口,适用于微信\小程序\支付宝口碑等接口开发或自建WEBAPP单页组件调用开发.
Version:		1.0.0
Author:			@快叫我韩大人
Author URI:  	https://doofox.com
License:     	GPL2
License URI: 	https://www.gnu.org/licenses/gpl-2.0.html
DFOXWP Version:	2.0

DFOXW WechatGrab is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

DFOXW WechatGrab is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with DFOXW WechatGrab. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
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
    'dfoxa_account_query_usermetakey' => '',
    'dfoxa_account_edit_usermetakey' => '',
    // 短信配置
    'dfoxa_sms_service' => '',
    'dfoxa_sms_appkey' => '',
    'dfoxa_sms_appsecret' => '',
    // 媒体库
    'dfoxa_media' => 'open',
    'dfoxa_media_size' => '2048',
    'dfoxa_media_type' => '',
    'dfoxa_media_user' => 'author',
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