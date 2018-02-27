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

    $signin_types = array(
      array(
          'key' => 'id',
          'name' => '用户ID'
      ),
      array(
          'key' => 'login',
          'name' => '账号'
      ),
      array(
          'key' => 'email',
          'name' => '邮箱'
      ),
      array(
          'key' => 'emailcode',
          'name' => '邮箱验证码'
      ),
      array(
          'key' => 'phone',
          'name' => '手机号'
      ),
      array(
          'key' => 'phonecode',
          'name' => '手机验证码'
      ),
      array(
          'key' => 'wechat',
          'name' => '微信'
      )
    );
    $signup_types = array(
        array(
            'key' => 'login',
            'name' => '账号'
        ),
        array(
            'key' => 'email',
            'name' => '邮箱'
        ),
        array(
            'key' => 'emailcode',
            'name' => '邮箱验证码'
        ),
        array(
            'key' => 'phone',
            'name' => '手机号'
        ) ,
        array(
            'key' => 'phonecode',
            'name' => '手机验证码'
        )
    );
    ?>
    <div class="dfox-wp-highlight-box dfox-wp-fullpage">
        <div class="dfox-wp-highleft">
            <h4></h4>
        </div>
    </div>
    <?php settings_errors(); ?>
    <form method="post">
        <div class="dfox-wp-t">
            <h4>登录与注册</h4>
        </div>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">账户登录限制</th>
                    <td>
                        <label>
                            <select name="dfoxa_account_signin_limit" data-bind-select='[{"event":"change","not":"disable","bind":"._select_account_signin_limit"}]'>
                                <option <?php if($data['dfoxa_account_signin_limit'] == 'disable') echo 'selected="selected"'; ?> value="disable">禁止登陆</option>
                                <option <?php if($data['dfoxa_account_signin_limit'] == 'single') echo 'selected="selected"'; ?> value="single">只允许单一设备</option>
                                <option <?php if($data['dfoxa_account_signin_limit'] == 'single_device') echo 'selected="selected"'; ?> value="single">只允许单一端设备</option>
                                <option <?php if($data['dfoxa_account_signin_limit'] == 'ip') echo 'selected="selected"'; ?> value="ip">允许同IP多设备</option>
                                <option <?php if($data['dfoxa_account_signin_limit'] == 'open') echo 'selected="selected"'; ?> value="open">开放登录限制</option>
                            </select>
                        </label>
                        <p>不满足条件时,前者会被后者强制下线(AccessToken 验证不通过),后者无任何感知.</p>
                        <p>只允许单一端设备:支持 PC 端和移动端两端同时在线,但每端只允许一个登录.</p>
                        <p>开放登录限制:允许同一账号多个设备登录.</p>
                    </td>
                </tr>
                <tr class="_select_account_signin_limit <?php if($data['dfoxa_account_signin_limit'] === 'disable'){echo 'dfox-wp-none';} ?>">
                    <th scope="row">允许的登录方式</th>
                    <td>
                        <?php foreach ($signin_types as $type) { ?>
                            <label for="signin_<?php echo $type['key']; ?>">
                                <input id="signin_<?php echo $type['key']; ?>" type="checkbox" name="dfoxa_account_signin_types[]"
                                       value="<?php echo $type['key']; ?>" <?php if (in_array($type['key'],$data['dfoxa_account_signin_types'])) {
                                    echo 'checked="checked"';
                                } ?> />
                                <?php echo $type['name']; ?></label>

                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">账户注册限制</th>
                    <td>
                        <label>
                            <select name="dfoxa_account_signup_limit"  data-bind-select='[{"event":"change","not":"disable","bind":"._select_account_signup_limit"}]'>
                                <option <?php if($data['dfoxa_account_signup_limit'] == 'open') echo 'selected="selected"'; ?> value="open">开放注册</option>
                                <option <?php if($data['dfoxa_account_signup_limit'] == 'disable') echo 'selected="selected"'; ?> value="disable">禁止注册</option>
                            </select>
                        </label>
                    </td>
                </tr>
                <tr class="_select_account_signup_limit <?php if($data['dfoxa_account_signup_limit'] === 'disable'){echo 'dfox-wp-none';} ?>">
                    <th scope="row">允许的注册方式</th>
                    <td>
                        <?php foreach ($signup_types as $type) { ?>
                            <label for="signup_<?php echo $type['key']; ?>">
                                <input id="signup_<?php echo $type['key']; ?>" type="checkbox" name="dfoxa_account_signup_types[]"
                                       value="<?php echo $type['key']; ?>" <?php if (in_array($type['key'],$data['dfoxa_account_signup_types'])) {
                                    echo 'checked="checked"';
                                } ?> />
                                <?php echo $type['name']; ?></label>

                        <?php } ?>
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