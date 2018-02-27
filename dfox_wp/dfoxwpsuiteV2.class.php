<?php

/**
 *  后台管理套件
 */
class DFOXWPSuite
{
    public $settingPages;

    function __construct()
    {
        // 注册样式和脚本
        $this->registerStyle();
        $this->settingPages = apply_filters('dfox_wp_setting_add_page', array());

        // 设置页面和排序
        $this->_setupSettingPage();

        $this->_createAdminPage();
    }

    /*
        注册样式和脚本
     */
    function registerStyle()
    {
        wp_register_style('dfoxwpstyle', plugins_url('', __FILE__) . '/dfoxwp.style.min.css');
        wp_enqueue_style('dfoxwpstyle');

        wp_enqueue_script('dfoxwpjs', plugins_url('', __FILE__) . '/dfoxwp.min.js', array('jquery-core', 'jquery-form'));
        wp_localize_script('dfoxwpjs', 'dfox_wp_local', array(
            'ajax_url' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http'))
        ));
    }

    /*
        重组 SettingPage, 包括去除无效内容和排序
     */
    private function _setupSettingPage()
    {
        $pages = $this->settingPages;
        foreach ($pages as $index => $page) {
            if (!isset($page['page_title']) || !isset($page['menu_title']) || !isset($page['menu_slug']) || !isset($page['pages'])) {
                unset($pages[$index]);
                continue;
            }
            if (!isset($page['position']) || $page['position'] == '') {
                $page['position'] = 99999;
            }
            $pages[$index] = $page;
        }
        // 排序
        $pages_num = count($pages);
        $newpages = array();
        foreach ($pages as $page_key => $page) {
            $newpages[] = $page;
        }
        for ($i = 0; $i < $pages_num; $i++) {
            for ($j = $i + 1; $j < $pages_num; $j++) {
                if ($newpages[$i]['position'] > $newpages[$j]['position']) {
                    $tmp = $newpages[$i];
                    $newpages[$i] = $newpages[$j];
                    $newpages[$j] = $tmp;
                }
            }
        }
        $this->settingPages = $newpages;
    }

    private function _createAdminPage()
    {
        ob_start();
        ?>
        <div class="dfox-wp-admin-page-warp-v2">
            <div class="dfox-wp-table">
                <div class="dfox-wp-tbody">
                    <div class="dfox-wp-sidebar">
                        <ul>
                            <li class="dfox-wp-no-left">
                                <h2><span class="dashicons dashicons-admin-settings"></span>插件设置</h2>
                            </li>
                            <?php
                            foreach ($this->settingPages as $page) {
                                $active = $this->_getThisPluginSlug() == $page['menu_slug'] ? 'dfox-wp-active' : '';
                                echo '<li class="' . $active . '"><a href="' . $this->_getMenuUrl($page['menu_slug']) . '">' . $page['menu_title'] . '</a></li>';
                            }
                            ?>
                            <?php do_action('dfox_wp_add_menu') ?>
                            <li class="dfox-wp-no-left">
                                <a href="https://doofox.cn" target="_blank" class="dfox-wp-addmore">
                                    <span class="dashicons dashicons-plus"></span>添加更多插件
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="dfox-wp-body">
                        <h2>
                            <?php echo $this->_getThisPageTitle(); ?>
                        </h2>
                        <div class="dfox-wp-nav dfox-wp-fullpage">
                            <?php if (count($this->_getThisPluginPages()) > 1) { ?>
                                <ul>
                                    <?php
                                    $pages = $this->_getThisPluginPages();
                                    foreach ($pages as $slug => $page) {
                                        $active = $slug == $this->_getThisPluginPageSlug() ? 'dfox-wp-active' : '';
                                        echo '<li class="' . $active . '"><a href="' . $this->_getPluginPageUrl($slug) . '">' . $this->_getPluginPageTitle($slug) . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            <?php } ?>
                            <!-- 子菜单 -->
                            <?php if (count($this->_getThisPluginPageChildPages()) > 0) { ?>
                                <ul class="child">
                                    <?php
                                    $active = '';
                                    if ($this->_getThisPluginPageChildPageSlug() == $this->_getThisPluginPageSlug()) {
                                        $active = 'dfox-wp-active';
                                    }
                                    echo '<li class="' . $active . '"><a href="' . $this->_getPluginPageUrl($this->_getThisPluginPageSlug()) . '">' . $this->_getPluginPageTitle($this->_getThisPluginPageSlug()) . '</a></li>';
                                    $pages = $this->_getThisPluginPageChildPages();
                                    foreach ($pages as $slug => $page) {
                                        $active = $slug == $this->_getThisPluginPageChildPageSlug() ? 'dfox-wp-active' : '';
                                        echo '<li class="' . $active . '"><a href="' . $this->_getPluginPageChildPageUrl($slug) . '">' . $this->_getPluginPageChildPageTitle($slug) . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            <?php } ?>
                        </div>
                        <?php
                        /*
                            首先在运行时关闭 magic_quotes_runtime 和 magic_quotes_sybase：
                            http://blog.wpjam.com/article/php-magic-quotes-and-wordpress/
                         */
                        @ini_set('magic_quotes_runtime', 0);
                        @ini_set('magic_quotes_sybase', 0);
                        // 加载 page 页面
                        $page = array();
                        if (count($this->_getThisPluginPageChildPages()) > 0) {
                            // 具有子页面的页面
                            $pages = $this->_getThisPluginPageChildPages();
                            if (isset($pages[$this->_getThisPluginPageChildPageSlug()])) {
                                // 子页
                                $page = $pages[$this->_getThisPluginPageChildPageSlug()];
                            } else {
                                // 非子
                                $pages = $this->_getThisPluginPages();
                                $page = $pages[$this->_getThisPluginPageChildPageSlug()];
                            }
                        } else {
                            // 不具有子页面
                            $pages = $this->_getThisPluginPages();
                            $page = $pages[$this->_getThisPluginPageSlug()];
                        }

                        if (!empty($page['init'])) {
                            require_once($page['init']);
                        }
                        // 可定义可不定义,实现按需加载
                        if (!empty($page['page'])) {
                            require_once($page['page']);
                        }
                        if (function_exists($page['function'])) {
                            call_user_func($page['function']);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo ob_get_clean();
    }

    // 获取当前的选中的插件
    private function _getThisPluginSlug()
    {
        $pages = $this->settingPages;
        if (isset($_GET['plugin'])) {
            foreach ($pages as $page) {
                if ($_GET['plugin'] == $page['menu_slug']) {
                    return $page['menu_slug'];
                }
            }
        }

        return $pages[0]['menu_slug'];
    }

    // 获取当前的选中的插件的页面
    private function _getThisPluginPages()
    {
        $pages = $this->settingPages;
        if (!isset($_GET['plugin'])) {
            return $pages[0]['pages'];
        }

        foreach ($pages as $page) {
            if ($page['menu_slug'] == $_GET['plugin']) {
                return $page['pages'];
            }
        }
    }

    // 获取当前的选中的插件的页面的子页面
    private function _getThisPluginPageChildPages()
    {
        $pages = $this->_getThisPluginPages();
        $page = $pages[$this->_getThisPluginPageSlug()];
        if (isset($page['child'])) {
            return $page['child'];
        }
        return array();
    }

    // 获取当前选中的插件的选中页面
    private function _getThisPluginPageSlug()
    {
        $pages = $this->settingPages;

        if (!isset($_GET['plugin'])) {
            foreach ($pages[0]['pages'] as $slug => $page) {
                return $slug;
            }
        }

        if (!isset($_GET['page_slug'])) {
            foreach ($pages as $plugin) {
                if ($plugin['menu_slug'] == $_GET['plugin']) {
                    foreach ($plugin['pages'] as $slug => $page) {
                        return $slug;
                    }
                }

            }
        }

        foreach ($pages as $plugin) {
            if ($plugin['menu_slug'] == $_GET['plugin']) {
                foreach ($plugin['pages'] as $slug => $page) {
                    if ($slug == $_GET['page_slug']) {
                        return $slug;
                    }
                }
            }
        }
    }

    // 获取当前选中的插件的选中页面的子页面slug
    private function _getThisPluginPageChildPageSlug()
    {
        if (isset($_GET['child'])) {
            return $_GET['child'];
        }
        return $this->_getThisPluginPageSlug();
    }

    // 获取当前选中插件的指定slug标题
    private function _getPluginPageTitle($key = '')
    {
        if (!empty($_GET['page_slug'])) {
            $pages = $this->settingPages;
            foreach ($pages as $plugin) {
                foreach ($plugin['pages'] as $slug => $page) {
                    if ($slug == $key) {
                        return $page['menu_title'];
                    }
                }
            }
        } else {
            $pages = self::_getThisPluginPages();
            foreach ($pages as $slug => $plugin) {
                if ($slug == $key) {
                    return $plugin['menu_title'];
                }
            }
        }
    }

    private function _getPluginPageChildPageTitle($slug = '')
    {
        $pages = $this->_getThisPluginPageChildPages();
        return $pages[$slug]['menu_title'];
    }

    // 获取插件页面地址
    private function _getMenuUrl($menuSlug = '', $fix = '')
    {
        if ($menuSlug == '') {
            $menuSlug = $this->_getThisPluginSlug();
        }
        return $this->_getPluginRootUrl('&plugin=' . $menuSlug . $fix);
    }

    // 获取插件页面中的的指定slug地址
    private function _getPluginPageUrl($slug = '')
    {
        if ($slug == '') {
            return $this->_getMenuUrl('', '&page_slug=' . $this->_getThisPluginPageSlug());
        }
        return $this->_getMenuUrl('', '&page_slug=' . $slug);
    }

    private function _getPluginPageChildPageUrl($slug = '')
    {
        return $this->_getPluginPageUrl() . '&child=' . $slug;
    }

    // 获取插件根地址
    private function _getPluginRootUrl($fix = '')
    {
        return dfox_wp_admin_url('admin.php?page=dfox_wp_admin' . $fix);
    }

    // 获取当前页面标题
    private function _getThisPageTitle()
    {
        $pages = $this->settingPages;
        if (isset($_GET['child'])) {
            foreach ($pages as $plugin) {
                foreach ($plugin['pages'] as $page) {
                    if (isset($page['child'])) {
                        foreach ($page['child'] as $child_slug => $childpage) {
                            if ($_GET['child'] == $child_slug) {
                                return $childpage['menu_title'];
                            }
                        }
                    }
                }
            }
        }

        if (isset($_GET['page_slug'])) {
            foreach ($pages as $plugin) {
                foreach ($plugin['pages'] as $slug => $page) {
                    if ($slug == $_GET['page_slug']) {
                        return $page['menu_title'];
                    }
                }
            }
        }

        if (isset($_GET['plugin'])) {
            foreach ($pages as $plugin) {
                if ($plugin['menu_slug'] == $_GET['plugin']) {
                    foreach ($plugin['pages'] as $slug => $page) {
                        return $page['menu_title'];
                    }

                }
            }
        }

        if (isset($_GET['page']) && !isset($_GET['plugin'])) {
            foreach ($pages[0]['pages'] as $slug => $page) {
                return $page['menu_title'];
            }
        }
    }
}

?>