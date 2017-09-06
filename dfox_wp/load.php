<?php
if(!function_exists('dfox_wp_add_extra')) {
	add_filter('extra_plugin_headers','dfox_wp_add_extra');
	add_filter('extra_theme_headers','dfox_wp_add_extra');
	function dfox_wp_add_extra($arr){
		$arr['DFOXWPVER'] = 'DFOXWP Version';
		return $arr;
	}
}
if(!function_exists('dfox_wp_check_plugins')) {
	add_filter('admin_init','dfox_wp_check_plugins');
	function dfox_wp_check_plugins(){
		$plugins 	= get_plugins();
		$theme 	    = wp_get_theme();
		$var = $dir = '';
		if($theme->get('DFOXWP Version') != '' && (float)$theme->get('DFOXWP Version') > 0){
			$var = (float)$theme->get('DFOXWP Version');
			$dir = get_stylesheet_directory();
		}
		foreach ($plugins as $key => $plugin) {
			if($plugin['DFOXWP Version'] != '' && (float)$plugin['DFOXWP Version'] > $var){
				$var = (float)$plugin['DFOXWP Version'];
				$dir = preg_replace('/(.*)\/{1}([^\/]*)/i', '$1', WP_PLUGIN_DIR.'/'.$key);
			}
		}
		define('DFOXWP_PLUGIN_DIR',$dir);
	}
}
if(!function_exists('dfox_wp_menu')) {
	add_action('admin_menu', 'dfox_wp_menu');
	function dfox_wp_menu(){
	    add_menu_page( 'DFOXWP 套件', 'DFOXWP 设置', 'edit_themes', 'dfox_wp_admin','dfox_wp_adminpage','',81);
	    $pages = apply_filters('dfox_wp_setting_add_page',array());
	    foreach ($pages as $page) {
	    	add_submenu_page('dfox_wp_admin',$page['menu_title'],'↳ '.$page['menu_title'],'edit_themes',$page['menu_slug'],'dfox_wp_redirect');
	    }
	}   
	function dfox_wp_adminpage(){
		include (DFOXWP_PLUGIN_DIR .'/dfox_wp/admin.php');
	}
	function dfox_wp_redirect(){
		$url = admin_url('admin.php?page=dfox_wp_admin&plugin='.$_GET['page']);
		echo "<script>window.location.href='{$url}';</script>";
		exit;
	}
}
if(!function_exists('dfox_wp_load_plugins')) {
	function dfox_wp_load_plugins($plugins){
		foreach ($plugins as $plugin) {
			require_once("plugins/{$plugin}.php");
		}
	}
}
?>