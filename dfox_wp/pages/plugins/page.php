<?php
// 阻止直接访问
if (!defined('ABSPATH')) exit;
if (isset($_POST['dfox_wp_save_field']) && wp_verify_nonce($_POST['dfox_wp_save_field'], 'dfox_wp_save')) {
    $plugin = get_dfoxa_plugins();
    if(!$plugin)
        return;

    if(isset($_POST['active'])){
        update_option('dfoxa_' . $_POST['active'],true);
    }elseif (isset($_POST['deactive'])){
        update_option('dfoxa_' . $_POST['deactive'],false);
    }elseif(isset($_POST['remove'])){
        delete_option('dfoxa_' . $_POST['remove']);
        $request = dfoxa_removeDirFiles(dirname(DFOXA_PLUGINS . DFOXA_SEP . $_POST['remove']));
        if($request){
            add_settings_error(
                '删除成功',
                esc_attr('settings_updated'),
                '已成功移除 [' . $plugin["Name"] . '] 插件',
                'updated'
            );
        }else{
            add_settings_error(
                '删除失败',
                esc_attr('settings_updated'),
                '无法移除 [' . $plugin["Name"] . '] 插件,请检查目录权限',
                'error'
            );
        }
    }

    if(isset($_POST['active']) || isset($_POST['deactive'])){
        echo "<script>location.reload();</script>";
    }
}
function dfoxa_plugins_page()
{
    ?>
    <div class="dfox-wp-highlight-box dfox-wp-fullpage">
        <div class="dfox-wp-highleft">
            <h4>管理你的 DFOXA WPAPI 插件</h4>
        </div>
    </div>
    <?php settings_errors(); ?>
    <style>
        .plugin-row {
            display: table;
            padding: 12px 24px;
        }

        .plugin-row:hover {
            background-color: #eee;
        }

        .plugin-row .col-left {
            width: 100%;
        }

        .plugin-row .col-left, .plugin-row .col-right {
            display: table-cell;
            vertical-align: middle;
            white-space: nowrap;
        }

        .plugin-row div h3 {
            margin: 0;
            padding: 0;
        }

        .plugin-row div h3 a {
            color: #000;
            font-size: 16px;
            font-weight: bold;
        }

        .plugin-row div h3 span {
            margin-left: 12px;
            font-size: 12px;
            color: #999;
        }

        .plugin-row div p {
            margin-bottom: 0;
        }

        .plugin-row div p span b {
            margin-right: 4px;
        }

        .plugin-row div p span:after {
            content: '/';
            display: inline-block;
            margin: 0 12px;
            color: #999;
        }

        .plugin-row div p span:last-child:after {
            display: none;
        }
    </style>
    <form method="post">
        <ul>
            <?php
            $plugins = get_dfoxa_plugins();
            if(!is_array($plugins) || count($plugins) === 0)
                return false;

            foreach ($plugins as $plugin_name => $plugin) {
                $plugin_key = 'dfoxa_' . $plugin_name;
                $active = get_option($plugin_key) == '1' ? true : false;
                    ?>
                <li>
                    <div class="plugin-row">
                        <div class="col-left">
                            <h3><a href="<?php echo $plugin['PluginURI']; ?>"
                                   target="_blank"><?php echo $plugin['Name']; ?></a><span><?php echo $plugin['Description']; ?></span>
                            </h3>
                            <p>
                                <span><b>版本号</b><?php echo $plugin['Version']; ?></span>
                                <span><b>作者</b><a href="<?php echo $plugin['AuthorURI']; ?>"
                                                  target="_blank"><?php echo $plugin['Author']; ?></a></span>
                                <span><b>标签</b><?php echo $plugin['Tags']; ?></span>
                            </p>
                        </div>
                        <div class="col-right">
                            <?php if(!$active){ ?>
                                <button type="submit" class="button button-primary" name="active" value="<?php echo $plugin_name; ?>">启用插件</button>
                            <?php }else{ ?>
                                <button type="submit" class="button" name="deactive" value="<?php echo $plugin_name; ?>">停用插件</button>
                            <?php } ?>
                            <button type="submit" class="button" name="remove" value="<?php echo $plugin_name; ?>">删除</button>
                        </div>
                    </div>
                </li>
            <?php } ?>
            <?php wp_nonce_field('dfox_wp_save', 'dfox_wp_save_field'); ?>
        </ul>
    </form>
<?php }