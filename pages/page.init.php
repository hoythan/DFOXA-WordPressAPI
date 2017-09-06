<?php
// 阻止直接访问
if (!defined('ABSPATH')) exit;

if(isset($_POST['dfox_wp_save_field']) && wp_verify_nonce($_POST['dfox_wp_save_field'],'dfox_wp_save')){
	global $dfoxa_default;
	if($_POST['submit'] === 'reset'){
		foreach ($_POST as $key => $value) {
			if(strstr($key,'dfoxa_')){
				update_option($key,$dfoxa_default[$key]);
			}
		}
		add_settings_error(
	        '初始化成功',
	        esc_attr('settings_updated'),
	        '已成功初始化当前页面的设置',
	        'updated'
    	);
		goto end;
	}else if($_POST['submit'] === 'save'){
		foreach ($_POST as $key => $value) {
			if(strstr($key,'dfoxa_t_')){
				update_option($key,rtrim($value));
			}else if(strstr($key,'dfoxa_')){
				$value = sanitize_text_field($value);
				update_option($key,$value);
			}
		}
		add_settings_error(
	        '保存成功',
	        esc_attr('settings_updated'),
	        '已成功保存当前页面的设置',
	        'updated'
    	);
		goto end;
	}
	end:
	wp_cache_delete('dfoxa_data');
}

?>