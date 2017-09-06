<?php
	/**
	*  后台管理套件
	*/
	class DFOXWPSuite{
		public $settingPages;
		function __construct(){
			$this->register();
			$this->settingPages = apply_filters('dfox_wp_setting_add_page',array());
			$this->registerSettingPage();
			$this->createAdminPage();
		}
		/*
			注册样式和脚本
		 */
		function register(){
			wp_register_style('dfoxwpstyle',plugins_url('', __FILE__).'/dfoxwp.style.min.css');
			// wp_register_script('dfoxwpjs',plugins_url('', __FILE__).'/dfoxwp.min.js', array('jquery-core','jquery-form'),''); 
			wp_enqueue_style('dfoxwpstyle'); 
			// wp_enqueue_script('dfoxwpjs');
			// wp_localize_script('dfoxwpjs', 'dfox_wp_local', array(
		 //        'ajax_url' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http'))
		 //    ));

		    wp_enqueue_script('dfoxwpjs',plugins_url('', __FILE__).'/dfoxwp.min.js',array('jquery-core','jquery-form'));
		    wp_localize_script('dfoxwpjs', 'dfox_wp_local', array(
		        'ajax_url' => admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http'))
		    ));
		}
		/*
			重组 SettingPage, 包括去除无效内容和排序
		 */
		function registerSettingPage(){
			$pages = $this->settingPages;
			foreach ($pages as $index => $page) {
				if(!isset($page['page_title']) || !isset($page['menu_title']) || !isset($page['menu_slug']) || !isset($page['child_slug'])){
					unset($pages[$index]);
					continue;
				}
				if(!isset($page['position']) || $page['position'] == ''){
					$page['position'] = 99999;
				}
				$pages[$index] = $page;
			}
			// 排序
			$pages_num = count($pages);
			for ($i = 0; $i < $pages_num; $i++) {
				for ($j = $i + 1; $j < $pages_num; $j++) {
					if($pages[$i]['position'] > $pages[$j]['position']){
						$tmp = $pages[$i]['position'];
						$pages[$i]['position'] = $pages[$j]['position'];
						$pages[$j]['position'] = $tmp;
					}
				}
			}
			$this->settingPages = $pages;
		}
		/*
			获取当前页面
		 */
		function getNowSetting($key = ''){
			$pages = $this->settingPages;
			$now_page = '';
			if(!isset($_GET['settingpage'])){
				$now_page = $pages[0];
			}else{
				foreach ($pages as $page) {
					if($_GET['settingpage'] == $page['menu_slug']){
						$now_page = $page;
						break;
					}
				}
			}
			return $key == '' ? $now_page : $now_page[$key];
		}
		/*
			获取当前页面地址
			$fix 可选 为获取的地址添加后缀
		 */
		function getAdminUrl($fix = ''){
			return admin_url('admin.php?page=dfox_wp_admin'.$fix);
		}
		function getMenuUrl($page = '',$fix = ''){
			if($page == ''){
				$page = $this->getNowSetting();
			}
			return $this->getAdminUrl('&settingpage='.$page['menu_slug'].$fix);
		}
		// 获取当前子页面地址
		function getChildUrl($key = ''){
			if($key == ''){

			}
			return $this->getMenuUrl('','&childpage='.$key);

		}
		// 获取当前子页面信息
		function getChildPage($key = ''){
			$page = $this->getNowSetting();
			$childpage = reset($page['child_slug']);
			if(isset($_GET['childpage']) && isset($page['child_slug'][$_GET['childpage']])){
				$childpage = $page['child_slug'][$_GET['childpage']];
			}
			if($key == 'page'){
				if(!isset($childpage[$key])){
					$childpage[$key] = '';
				}
			}
			return $key == '' ? $childpage : $childpage[$key];
		}
		function getNowChildSlug(){
			$page = $this->getNowSetting('child_slug');
			if(!isset($_GET['childpage']) || $_GET['childpage'] == ''){
				$sulg = key($page);
			}else{
				$sulg = $_GET['childpage'];
			}
			return $sulg;
		}
		function createAdminPage(){
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
										$active = $this->getNowSetting('menu_slug') == $page['menu_slug'] ? 'dfox-wp-active' : '';
										echo '<li class="'.$active.'"><a href="'.$this->getMenuUrl($page).'">'.$page['menu_title'].'</a></li>';
									}
								?>
								<?php do_action('dfox_wp_add_menu') ?>
								<li class="dfox-wp-no-left">
									<a href="https://nnnn.blog" target="_blank" class="dfox-wp-addmore">
										<span class="dashicons dashicons-plus"></span>添加更多插件
									</a>
								</li>
						</div>
						<div class="dfox-wp-body">
							<h2>
								<?php echo $this->getNowSetting('page_title'); ?>
							</h2>
							<?php if(count($this->getNowSetting('child_slug')) > 1){ ?>
							<div class="dfox-wp-nav dfox-wp-fullpage">
								<ul>
									<?php 
										$nowsulg = $this->getNowChildSlug();
										foreach ($this->getNowSetting('child_slug') as $slug => $child) {
											$active = $slug == $nowsulg ? 'dfox-wp-active' : '';
											echo '<li class="'.$active.'"><a href="'.$this->getChildUrl($slug).'">'.$child['menu_title'].'</a></li>';
										}
									?>
								</ul>
                                <?php
                                ?>
							</div>
							<?php } ?>
							<?php
								/*
									首先在运行时关闭 magic_quotes_runtime 和 magic_quotes_sybase：
									http://blog.wpjam.com/article/php-magic-quotes-and-wordpress/
								 */
								@ini_set( 'magic_quotes_runtime', 0 );
								@ini_set( 'magic_quotes_sybase',  0 );
								// 定义头部,建议吧相关保存等内容存放至此
								if($this->getChildPage('init') != ''){
									require_once($this->getChildPage('init'));
								}
								// 可定义可不定义,实现按需加载
								if($this->getChildPage('page') != ''){
									require_once($this->getChildPage('page'));
								}
								if(function_exists($this->getChildPage('function'))) {
									call_user_func($this->getChildPage('function'));
								}
							?>
						</div>
					</div>
				</div>
			</div>
		<?php 
			echo ob_get_clean();
		}
	}
?>