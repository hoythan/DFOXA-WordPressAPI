<?php
// 阻止直接访问
if (!defined('ABSPATH')) exit;

function dfoxa_sms_page(){
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
        <h4>配置使用手机验证码信息</h4>
    </div>
</div>
<?php settings_errors(); ?>
<form method="post">
    <div class="dfox-wp-t">
        <h4>手机验证接口配置</h4>
    </div>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">手机验证码接口</th>
                <td>
                    <label>
                        <select name="dfoxa_sms_service" data-bind-select='[{"event":"change","if":"alidayu","bind":"._alidayu"}]'>
                            <option <?php if($data['dfoxa_sms_service'] == 'alidayu') echo 'selected="selected"'; ?> value="alidayu">阿里大于</option>
                        </select>
                    </label>
                </td>
            </tr>
            <tr class="_alidayu dfox-wp-none">
                <th scope="row">App Key</th>
                <td>
                    <input name="dfoxa_sms_appkey" type="text" placeholder="" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_sms_appkey']); ?>">
                    <p>请添加相关短信服务商提供的App Key</p>
                </td>
            </tr>
            <tr class="_alidayu dfox-wp-none">
                <th scope="row">App Secret</th>
                <td>
                    <input name="dfoxa_sms_appsecret" type="text" placeholder="" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_sms_appsecret']); ?>">
                    <p>请添加相关短信服务商提供的App Secret</p>
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
<?php }