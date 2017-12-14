<?php
// 阻止直接访问
if (!defined('ABSPATH')) exit;

function dfoxa_api_page(){
    global $dfoxa_default;
    $data = $dfoxa_default;
    foreach ($data as $key => $value) {
        if(get_option($key)){
            $value = get_option($key);
            $data[$key] = $value;
        }
    }
?>
<div class="dfox-wp-highlight-box dfox-wp-fullpage">
	<div class="dfox-wp-highleft">
		<h4>欢迎使用DFOXA WordPress API接口插件</h4>
	</div>
</div>
<?php settings_errors(); ?>
<form method="post">
	<div class="dfox-wp-t">
		<h4>基本设置</h4>
	</div>
	<table class="form-table">
		<tbody>
            <tr>
                <th scope="row">UniqueCode</th>
                <td>
                    <input name="dfoxa_uniquecode" type="text" placeholder="" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_uniquecode']); ?>">
                    <button type="submit" class="button" id="randUnqueuCode">随机生成</button>
                    <p>请填写一个用于数据加密的密钥,建议12位或以上长度</p>
                </td>
            </tr>
            <tr>
                <th scope="row">RSA加密 公钥</th>
                <td>
                    <textarea name="dfoxa_t_rsa_public" rows="10" cols="50" class="large-text code" placeholder="-----BEGIN PUBLIC KEY-----"><?php echo $data['dfoxa_t_rsa_public']; ?></textarea>
                    <p>请访问 <a href="http://web.chacuo.net/netrsakeypair" target="_blank" >Rand RSA</a> 生成RSA加密公钥私钥对 1024位(BIT) 和 PKCS#8</p>
                </td>
            </tr>
            <tr>
                <th scope="row">RSA加密 私钥</th>
                <td>
                    <textarea name="dfoxa_t_rsa_private" rows="10" cols="50" class="large-text code" placeholder="-----BEGIN PRIVATE KEY-----"><?php echo $data['dfoxa_t_rsa_private']; ?></textarea>
                    <p>请填写和上方公钥匹配的私钥</p>
                </td>
            </tr>
            <tr>
                <th scope="row">API 网关格式</th>
                <td>
                    <input name="dfoxa_gateway" type="text" placeholder="" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_gateway']); ?>">
                    <p>推荐使用默认设置,当然你也可以自定义它们 [ <?php esc_attr_e($data['dfoxa_gateway']) ?> => <?php echo home_url('/'.esc_attr($data['dfoxa_gateway'])); ?> ]</p>
                </td>
            </tr>
            <tr>
                <th scope="row">缓存系统设置</th>
                <td>
                    <select name="dfoxa_cache_type">
<!--                        <option --><?php //// if($data['dfoxa_cache_type'] == 'wp'){echo 'selected="selected"';} ?><!-- value="wp">WP Object Cache</option>-->
<!--                        <option --><?php //// if($data['dfoxa_cache_type'] == 'memcache'){echo 'selected="selected"';} ?><!-- value="memcache">Memcache</option>-->
                        <option <?php if($data['dfoxa_cache_type'] == 'wordpress'){echo 'selected="selected"';} ?> value="wordpress">WordPress 自带缓存</option>
                    </select>
                    <p>WordPress 自带缓存请务必确认已经安装相关memcache(d)插件,否则将无法正常使用!</p>
                </td>
            </tr>
		</tbody>
	</table>
	<div class="dfox-wp-foot">
		<?php wp_nonce_field('dfox_wp_save', 'dfox_wp_save_field'); ?>
		<button type="submit" class="button button-primary" name="submit" value="save" style="float:right;">保存以上更改</button>
		<div>
			<button type="submit" class="button" name="submit" value="reset" >初始化本页设置</button>
		</div>
	</div>
</form>
<script type="text/javascript">

	function _getRandomString(len) {  
	    len = len || 32;  
	    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678'; // 默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1  
	    var maxPos = $chars.length;  
	    var pwd = '';  
	    for (i = 0; i < len; i++) {  
	        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));  
	    }  
	    return pwd;  
	}  

	jQuery(document).ready(function($) {
		jQuery('#randUnqueuCode').on('click', '', function(event) {
			event.preventDefault();
			jQuery('input[name="dfoxa_uniquecode"]').val(_getRandomString());
			
		});
	});
</script>
<?php }