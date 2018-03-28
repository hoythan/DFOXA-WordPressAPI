<?php
// 阻止直接访问
if (!defined('ABSPATH')) exit;

function dfoxa_media_page()
{
    global $dfoxa_default;
    $data = $dfoxa_default;
    foreach ($data as $key => $value) {
        if (get_option($key)) {
            $value = get_option($key);
            $data[$key] = $value;
        }
    }

    // 用户组
    foreach (wp_roles()->roles as $role_key => $role) {
        $roles[] = array('key' => $role_key, 'name' => $role['name']);
    }
    ?>
    <div class="dfox-wp-highlight-box dfox-wp-fullpage">
        <div class="dfox-wp-highleft">
            <h4>所有功能的媒体库方面操作,都在此进行设置/限制</h4>
        </div>
    </div>
    <?php settings_errors(); ?>
    <form method="post">
        <div class="dfox-wp-t">
            <h4>限制</h4>
        </div>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row">开启媒体库功能</th>
                <td>
                    <label>
                        <select name="dfoxa_media">
                            <option <?php if ($data['dfoxa_media'] == 'open') {
                                echo 'selected="selected"';
                            } ?> value="open">开启
                            </option>
                            <option <?php if ($data['dfoxa_media'] == 'close') {
                                echo 'selected="selected"';
                            } ?> value="close">关闭
                            </option>
                        </select>
                        <p>如使用文章前台发布,商城...等所有需要用到图片上传的功能,请务必打开此媒体库限制</p>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">允许上传媒体的用户</th>
                <td>
                    <select name="dfoxa_media_user" data-bind-select='[{"event":"change","if":"select","bind":"._select_user_role"}]'>
                        <option <?php if ($data['dfoxa_media_user'] == 'select') {
                            echo 'selected="selected"';
                        } ?> value="select">指定用户组
                        </option>
                        <option <?php if ($data['dfoxa_media_user'] == 'all') {
                            echo 'selected="selected"';
                        } ?> value="all">所有用户
                        </option>
                    </select>
                    <p>所有用户<b>包括未登录用户!</b></p>
                </td>
            </tr>
            <tr class="_select_user_role <?php if($data['dfoxa_media_user'] != 'select'){echo 'dfox-wp-none';} ?>">
                <th scope="row">允许上传媒体的用户组</th>
                <td>
                    <?php foreach ($roles as $role) { ?>
                        <label for="<?php echo $role['key']; ?>">
                            <input id="<?php echo $role['key']; ?>" type="checkbox" name="dfoxa_media_user_role[]"
                                   value="<?php echo $role['key']; ?>" <?php if (in_array($role['key'],$data['dfoxa_media_user_role'])) {
                                echo 'checked="checked"';
                            } ?> />
                            <?php echo $role['name']; ?></label>
                    <?php } ?>
                    <p>使用文章或商城系统的时候,如果用到传图,<b>请确定用户在允许的范围内!</b></p>
                </td>
            </tr>
            <tr>
                <th scope="row">允删除媒体的用户</th>
                <td>
                    <select name="dfoxa_media_del_user" data-bind-select='[{"event":"change","if":"select","bind":"._select_del_user_role"}]'>
                        <option <?php if ($data['dfoxa_media_del_user'] == 'select') {
                            echo 'selected="selected"';
                        } ?> value="select">指定用户组
                        </option>
                        <option <?php if ($data['dfoxa_media_del_user'] == 'oneself') {
                            echo 'selected="selected"';
                        } ?> value="oneself">只允许删除自己上传的媒体
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="_select_del_user_role <?php if($data['dfoxa_media_del_user'] != 'select'){echo 'dfox-wp-none';} ?>">
                <th scope="row">允许删除媒体的用户组</th>
                <td>
                    <?php foreach ($roles as $role) { ?>
                        <label for="<?php echo $role['key']; ?>_del">
                            <input id="<?php echo $role['key']; ?>_del" type="checkbox" name="dfoxa_media_del_user_role[]"
                                   value="<?php echo $role['key']; ?>" <?php if (in_array($role['key'],$data['dfoxa_media_del_user_role'])) {
                                echo 'checked="checked"';
                            } ?> />
                            <?php echo $role['name']; ?></label>
                    <?php } ?>
                    <p>请谨慎选择允许删除媒体的用户组</p>
                </td>
            </tr>
            <tr>
                <th scope="row">文件上传尺寸限制</th>
                <td>
                    <input name="dfoxa_media_size" type="text" placeholder="2048" class="regular-text code"
                           value="<?php esc_attr_e($data['dfoxa_media_size']); ?>">
                    <p>在此限制文件上传尺寸,务必填写单位,单位支持(B,KB,MB,GB,TB,PB,EB,ZB,YB),例如2mb限制需填写2mb.</p>
                    <p>切记不要超过 WordPress 本身的上传限制</p>
                </td>
            </tr>
            <tr>
                <th scope="row">文件上传格式限制</th>
                <td>
                    <input name="dfoxa_media_type" type="text" placeholder="jpg,png,gif,zip" class="regular-text code"
                           value="<?php esc_attr_e($data['dfoxa_media_type']); ?>">
                    <p>填写 * 很危险,表示允许上传所有文件,虽然你可以这么做</p>
                </td>
            </tr>
            <tr>
                <th scope="row">文件访问允许的域名</th>
                <td>
                    <textarea name="dfoxa_media_url" rows="3" cols="50" class="large-text code"
                              placeholder="*.domain.com"><?php echo $data['dfoxa_media_url']; ?></textarea>
                    <p>用于限制域名访问,建议设置前端和后端的网址,防止文件被外链(必须使用以下拼接方式的地址)</p>
                    <p>留空表示不限制访问来源域名,可使用*作为通配符,<b>一行一个域名</b></p>
                    <p>API调用接口:<?php echo home_url(); ?>/<?php echo get_option('dfoxa_gateway'); ?>/media/file/get?id={{文件ID}}</p>
                    <p></p>
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