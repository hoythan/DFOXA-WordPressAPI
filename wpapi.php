<?php
/*
Plugin Name: DFOXA-WordPressAPI
Plugin URI: https://doofox.cn
Description: WordPress API 扩展插件,允许使用 WordPres 开发标准的API接口,为基于 WordPress 的前后端分离项目实现便捷轻快的后端开发体验. 
Version: 1.1.0
Author: DooFox. Inc,
Author URI: https://doofox.cm
License:MIT License
DFOXWP Version:	2.0

MIT License

Copyright (c) 2017 DooFox. Inc,

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
define( 'DFOXA_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'DFOXA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DFOXA_PLUGINS', dirname( __FILE__ ) . '/plugins' );
define( 'DFOXA_SEP', DIRECTORY_SEPARATOR );

require 'dfox_wp/pages/function.php';
require 'functions.php';
require 'vendor/autoload.php';

function load_dfoxa_wpapi() {
	$gateway = new \gateway\method();
	$gateway->run();
}

add_action( 'wp', 'load_dfoxa_wpapi', -999 );

/*
	后台
 */
global $dfoxa_default;
$dfoxa_default = array(
	'dfoxa_uniquecode'                   => '',
	'dfoxa_t_rsa_public'                 => '',
	'dfoxa_t_rsa_private'                => '',
	'dfoxa_gateway'                      => 'gateway.do',
	'dfoxa_cache_type'                   => 'wp',
	// 账户配置
//    'dfoxa_account_login' => 'open',
//    'dfoxa_account_reg' => 'open',
//    'dfoxa_account_type' => 'account',
//    'dfoxa_t_account_query_usermetakey' => '',
//    'dfoxa_t_account_edit_usermetakey' => '',
	'dfoxa_account_signin_limit'         => 'ip',
	'dfoxa_account_signin_types'         => array( 'id', 'login', 'email', 'phonecode' ),
	'dfoxa_account_signup_limit'         => 'open',
	'dfoxa_account_signup_types'         => array( 'emailcode' ),
	'dfoxa_account_access_token_expire'  => '3600',
	'dfoxa_media_del_user'               => 'oneself',
	'dfoxa_media_user_role'              => array(),
	'dfoxa_media_del_user_role'          => array(),
	// 邮箱配置
	'dfoxa_email_host'                   => 'smtp.exmail.qq.com',
	'dfoxa_email_port'                   => '465',
	'dfoxa_email_secure'                 => 'ssl',
	'dfoxa_email_smtpauth'               => 'yes',
	'dfoxa_email_username'               => '',
	'dfoxa_email_password'               => '',
	'dfoxa_t_email_param_appname'        => '',
	'dfoxa_t_email_param_sendfrom_email' => '',
	'dfoxa_t_email_param_sendfrom_name'  => get_bloginfo( 'name' ),
	'dfoxa_t_email_param_logo'           => '',
	'dfoxa_t_email_param_welcome'        => '您已成功注册  {{app_name}}  账户，即刻开始从任意地方登录到您的  {{app_name}}  账户 (<span><a href="mailto:{{user_email}}" style="color:#2b2b2b;text-decoration:none" target="_blank"><u></u><b>{{user_email}}</b><u></u></a></span>)，开始您的轻阅读之旅。',
	'dfoxa_t_email_param_footlinks'      => '',
	'dfoxa_t_email_param_inscription'    => get_bloginfo( 'name' ),
	'dfoxa_t_email_param_copyright'      => '<span style="white-space:nowrap">Copyright © 2017</span>
<span>
<span><a href="https://doofox.com" style="color:#888888;text-decoration:none" target="_blank"> DooFox </a></span>
</span>
<span style="white-space:nowrap">, Inc. 保留所有权利。</span>',
	// 短信配置
	'dfoxa_sms_service'                  => '',
	'dfoxa_sms_appkey'                   => '',
	'dfoxa_sms_appsecret'                => '',
	// 媒体库
	'dfoxa_media'                        => 'open',
	'dfoxa_media_size'                   => '2048',
	'dfoxa_media_type'                   => '',
	'dfoxa_media_user'                   => 'select',
	'dfoxa_media_user_role'              => array(),
	'dfoxa_media_url'                    => '',
);
// ADMIN
require_once( 'dfox_wp/load.php' );
add_filter( 'dfox_wp_setting_add_page', 'dfoxa_add_settingpage', 1, 1 );
function dfoxa_add_settingpage( $pageArr ) {
	$pageArr['dfoxa_wpapi'] = array(
		'page_title' => 'DFOXA WPAPI设置',
		'menu_title' => 'WPAPI设置',
		'menu_slug'  => 'wpapi',
		'position'   => 10,
		'pages'      => array(
			'api'     => array(
				'function'   => 'dfoxa_api_page',
				'menu_title' => '接口配置',
				'init'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/page.init.php',
				'page'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/api/page.php'
			),
			'account' => array(
				'function'   => 'dfoxa_account_page',
				'menu_title' => '基本配置',
				'init'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/page.init.php',
				'page'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/account/page.php',
				'child'      => array(
					'email' => array(
						'function'   => 'dfoxa_email_page',
						'menu_title' => '邮箱配置',
						'init'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/page.init.php',
						'page'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/account/email.php'
					),
					'sms'   => array(
						'function'   => 'dfoxa_sms_page',
						'menu_title' => '手机短信配置',
						'init'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/page.init.php',
						'page'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/account/sms.php'
					)
				)
			),
			'media'   => array(
				'function'   => 'dfoxa_media_page',
				'menu_title' => '媒体库',
				'page'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/media/page.php',
				'init'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/page.init.php'
			),
			'plugins' => array(
				'function'   => 'dfoxa_plugins_page',
				'menu_title' => '插件管理',
				'page'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/plugins/page.php',
				'init'       => DFOXA_PLUGIN_DIR . '/dfox_wp/pages/plugins/page.php'
			)
		)
	);

	return $pageArr;
}

?>