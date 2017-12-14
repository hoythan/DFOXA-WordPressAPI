<?php
// 阻止直接访问
if (!defined('ABSPATH')) exit;

function dfoxa_email_page()
{
    global $dfoxa_default;
    $data = $dfoxa_default;
    foreach ($data as $key => $value) {
        if (get_option($key)) {
            $value = get_option($key);
            $data[$key] = $value;
        }
    }
    ?>
    <div class="dfox-wp-highlight-box dfox-wp-fullpage">
        <div class="dfox-wp-highleft">
            <h4>配置邮箱信息和风格</h4>
        </div>
    </div>
    <?php settings_errors(); ?>
    <form method="post">
        <div class="dfox-wp-t">
            <h4>邮箱配置 SMTP</h4>
        </div>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">SMTP 发送服务器</th>
                <td>
                    <input name="dfoxa_email_host" type="text" placeholder="" class="regular-text code"
                           value="<?php esc_attr_e($data['dfoxa_email_host']); ?>">
                    <p>推荐使用腾讯企业邮箱,海外服务器可配置相关海外地址</p>
                </td>
            </tr>
            <tr>
                <th scope="row">发送服务器端口号</th>
                <td>
                    <input name="dfoxa_email_port" type="text" placeholder="" class="regular-text code"
                           value="<?php esc_attr_e($data['dfoxa_email_port']); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">SMTP 加密方式</th>
                <td>
                    <label>
                        <select name="dfoxa_email_secure">
                            <option <?php if($data['dfoxa_email_secure'] == 'none') echo 'selected="selected"'; ?> value="none">None</option>
                            <option <?php if($data['dfoxa_email_secure'] == 'ssl') echo 'selected="selected"'; ?> value="ssl">SSL</option>
                            <option <?php if($data['dfoxa_email_secure'] == 'tls') echo 'selected="selected"'; ?> value="tls">TLS</option>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">SMTP 验证</th>
                <td>
                    <label>
                        <select name="dfoxa_email_smtpauth" data-bind-select='[{"event":"change","if":"yes","bind":"._select_dfoxa_email_smtpauth"}]'>
                            <option <?php if($data['dfoxa_email_smtpauth'] == 'none') echo 'selected="selected"'; ?> value="none">无需验证</option>
                            <option <?php if($data['dfoxa_email_smtpauth'] == 'yes') echo 'selected="selected"'; ?> value="yes">需要验证</option>
                    </label>
                </td>
            </tr>
            <tr class="_select_dfoxa_email_smtpauth <?php if($data['dfoxa_email_smtpauth'] !== 'yes'){echo 'dfox-wp-none';} ?>">
                <th scope="row">登录账号</th>
                <td>
                    <input style="opacity:0;position: fixed;top:0;" autocomplete="off" name="username" type="text"
                           class="regular-text code" value="<?php esc_attr_e($data['dfoxa_email_username']); ?>">
                    <input name="dfoxa_email_username" type="text" class="regular-text code"
                           value="<?php esc_attr_e($data['dfoxa_email_username']); ?>">
                </td>
            </tr>
            <tr class="_select_dfoxa_email_smtpauth <?php if($data['dfoxa_email_smtpauth'] !== 'yes'){echo 'dfox-wp-none';} ?>">
                <th scope="row">登录密码</th>
                <td>

                    <input style="opacity:0;position: fixed;top:0;" type="password">
                    <input name="dfoxa_email_password" type="password" class="regular-text code"
                           value="<?php esc_attr_e($data['dfoxa_email_password']); ?>">
                </td>
            </tr>
            </tbody>
        </table>
        <div class="dfox-wp-t">
            <h4>邮件字段配置</h4>
        </div>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">应用名称</th>
                <td>
                    <input name="dfoxa_t_email_param_appname" type="text" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_t_email_param_appname']); ?>">
                    <p>通常用户邮件标题等位置,例如 XXXAPP,效果为 :您的 XXXAPP 帐户：来自新电脑的访问</p>
                </td>
            </tr>
            <tr>
                <th scope="row">发件人邮箱</th>
                <td>
                    <input name="dfoxa_t_email_param_sendfrom_email" type="text" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_t_email_param_sendfrom_email']); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">发件人名称</th>
                <td>
                    <input name="dfoxa_t_email_param_sendfrom_name" type="text" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_t_email_param_sendfrom_name']); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">Logo 地址</th>
                <td>
                    <input name="dfoxa_t_email_param_logo" type="text" placeholder="https://domain.com/logo.jpg" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_t_email_param_logo']); ?>">
                    <p>建议使用小于 100 * 100 像素的方形图片,并上传到CDN存储</p>
                </td>
            </tr>
            <tr>
                <th scope="row">邮件落款</th>
                <td>
                    <input name="dfoxa_t_email_param_inscription" type="text" placeholder="<?php echo get_bloginfo('name'); ?>" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_t_email_param_inscription']); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">注册欢迎词</th>
                <td>
                    <textarea name="dfoxa_t_email_param_welcome" rows="3" cols="50" class="large-text code" placeholder=""><?php echo stripslashes($data['dfoxa_t_email_param_welcome']); ?></textarea>
                    <p>允许使用变量</p>
                </td>
            </tr>
            <tr>
                <th scope="row">页脚链接</th>
                <td>
                    <textarea name="dfoxa_t_email_param_footlinks" rows="3" cols="50" class="large-text code" placeholder="[This link](http://domain.com/)"><?php echo stripslashes($data['dfoxa_t_email_param_footlinks']); ?></textarea>
                </td>
            </tr>
            <tr>
                <th scope="row">页脚信息</th>
                <td>
                    <textarea name="dfoxa_t_email_param_copyright" rows="3" cols="50" class="large-text code" placeholder="&copy; 2017 ..."><?php echo stripslashes($data['dfoxa_t_email_param_copyright']); ?></textarea>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="dfox-wp-foot">
            <?php wp_nonce_field('dfox_wp_save', 'dfox_wp_save_field'); ?>
            <button type="submit" class="button button-primary" name="submit" value="save" style="float:right;">保存以上更改
            </button>
            <div>
                <button type="submit" class="button" name="submit" value="reset">初始化本页设置</button>
            </div>
        </div>
    </form>
<?php }