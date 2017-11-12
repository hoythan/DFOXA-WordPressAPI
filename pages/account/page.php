<?php
// 阻止直接访问
if (!defined('ABSPATH')) exit;

function dfoxa_account_page(){
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
            <h4>配置使用手机注册、邮箱注册等，相关的注册方式需要相关的服务配置</h4>
        </div>
    </div>
    <?php settings_errors(); ?>
    <form method="post">
        <div class="dfox-wp-t">
            <h4>账号配置</h4>
        </div>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">开启用户登陆接口</th>
                    <td>
                        <label>
                            <select name="dfoxa_account_login">
                                <option <?php if($data['dfoxa_account_login'] == 'open'){echo 'selected="selected"';} ?> value="open">开启</option>
                                <option <?php if($data['dfoxa_account_login'] == 'close'){echo 'selected="selected"';} ?> value="close">关闭</option>
                            </select>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">开启用户注册接口</th>
                    <td>
                        <label>
                            <select name="dfoxa_account_reg">
                                <option <?php if($data['dfoxa_account_reg'] == 'open'){echo 'selected="selected"';} ?>
                                value="open">开启</option>

                                <option <?php if($data['dfoxa_account_reg'] == 'close'){echo 'selected="selected"';} ?>
                                        value="close">关闭</option>
                            </select>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">登陆/注册方式</th>
                    <td>
                        <label>
                            <select name="dfoxa_account_type">
                                <option <?php if($data['dfoxa_account_type'] == 'account'){echo 'selected="selected"';} ?>
                                        value="account">常规</option>
                                <option <?php if($data['dfoxa_account_type'] == 'phone'){echo 'selected="selected"';} ?>
                                        value="phone">手机</option>
                                <option <?php if($data['dfoxa_account_type'] == 'autophone'){echo 'selected="selected"';} ?>
                                        value="autophone">手机(登录即注册)</option>
                                <option <?php if($data['dfoxa_account_type'] == 'custom'){echo 'selected="selected"';} ?>
                                        value="custom">自选</option>
                            </select>
                        </label>
                        <p>常规:使用邮箱/账号(可以是手机号)和登录密码注册或登录</p>
                        <p>手机:使用手机号和验证码登录</p>
                        <p>手机(登录即注册):使用手机号和验证码登录,如果账号不存在自动注册（直接使用登录接口）</p>
                        <p>自选:如果你需要多个登录注册方式,你可以选择此方式,你将允许使用上面的所有登录注册方式</p>
                        <p>自选登陆请务必配置好相关接口（例如手机验证码等）,接口调用时请使用<b>type="account"</b>或<b>type="phone"</b>等加以区分</p>
                        <p>常规 type="account" / 手机 type="phone" / 手机(登录即注册) type="autophone"</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">AccessToken 有效期</th>
                    <td>
                        <input name="dfoxa_account_access_token_expire" type="text" placeholder="单位 秒" class="regular-text code"  value="<?php esc_attr_e($data['dfoxa_account_access_token_expire']); ?>">
                        <p>单位秒,默认 3600 秒,<b>不能超过 2592000 秒(30天)</b></p>
                        <p>用户登录注册后将得到唯一的AccessToken,在用户超过指定时间内不使用相关登录接口会导致Token过期</p>
                        <p>使用以下接口会自动延长Token有效期</p>
                        <p>* account.token.verify 验证token时</p>
                        <p>* 在代码中使用account\token\verify::check获取用户授权信息时</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">查询时可获取的UserMeta字段</th>
                    <td>
                        <textarea name="dfoxa_account_query_usermetakey" rows="10" cols="50" class="large-text code" placeholder="avatar,firstname,lastname,age..."><?php esc_attr_e($data['dfoxa_account_query_usermetakey']); ?></textarea>
                        <p>在登陆注册或查询用户信息时,允许返回的usermeta字段一行一个,你可以直接填写 * 但是这可能会返回比较"危险"的数据.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">所允许修改的UserMeta字段</th>
                    <td>
                        <textarea name="dfoxa_account_edit_usermetakey" rows="10" cols="50" class="large-text code" placeholder="avatar,firstname,lastname,age..."><?php esc_attr_e($data['dfoxa_account_edit_usermetakey']); ?></textarea>
                        <p>请填写接口的usermeta所支持的自定义字段,一行一个,你可以直接填写 * 虽然这是很"危险"的事情.</p>
                        <p>*在用户注册,更新时允许的用户meta字段名</p>
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