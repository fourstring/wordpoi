<?php
/*
Plugin Name: WordPoi
Plugin URI: http://inn-studio.com/wordpoi
Description: Accelerate your wordpress by modify source code.
Author: INN STUDIO
Author URI: http://inn-studio.com
Version: 1.0.0
Text Domain: wordpoi
Domain Path: /languages
*/

add_action('plugins_loaded','wordpoi::init');
class wordpoi{
	public static $iden = 'wordpoi';
	private static $plugin_features = 'wordpoi\\core\\plugin_features';
	private static $plugin_functions = 'wordpoi\\core\\plugin_functions';
	private static $plugin_options = 'wordpoi\\core\\plugin_options';

	private static function tdomain(){
		load_plugin_textdomain(__CLASS__,false,dirname(plugin_basename(__FILE__)). '/languages/');
		$header_translate = array(
			'plugin_name' => __('WordPoi',__CLASS__),
			'plugin_uri' => __('http://inn-studio.com/wordpoi',__CLASS__),
			'description' => __('Accelerate your wordpress by modify source code.',__CLASS__),
			'author_uri' => __('http://inn-studio.com',__CLASS__),
		);
	}
	private static function update(){
		include __DIR__ . '/inc/update.php';
		$update_checker = new wordpoi\inc\PluginUpdateChecker(
			__('http://update.inn-studio.com',__CLASS__) . '/?action=get_update&slug=' . __CLASS__,
			__FILE__,
			__CLASS__
		);
		$update_checker->M00001 = __('Check for updates',__CLASS__);
		$update_checker->M00002 = __('The URL %s does not point to a valid plugin metadata file. ',__CLASS__);
		$update_checker->M00003 = __('WP HTTP error: ',__CLASS__);
		$update_checker->M00004 = __('HTTP response code is %s . (expected: 200) ',__CLASS__);
		$update_checker->M00005 = __('wp_remote_get() returned an unexpected result.',__CLASS__);
		$update_checker->M00006 = __('Can not to read the Version header for %s. The filename may be incorrect, or the file is not present in /wp-content/plugins.',__CLASS__);
		$update_checker->M00007 = __('Skipping update check for %s - installed version unknown.',__CLASS__);
		$update_checker->M00008 = __('This plugin is up to date.',__CLASS__);
		$update_checker->M00009 = __('A new version of this plugin is available.',__CLASS__);
		$update_checker->M00010 = __('Unknown update checker status "%s"',__CLASS__);	
	}
	public static function init(){
		
		include __DIR__ . '/core/core-functions.php';
		include __DIR__ . '/core/core-options.php';
		include __DIR__ . '/core/core-features.php';
		
		self::tdomain();
		self::update();
		/** 
		 * ajax
		 */
		add_action('wp_ajax_' . __CLASS__,__CLASS__ . '::process');
		/**
		 * settings
		 */
		add_action('plugin_base_settings_' . __CLASS__, __CLASS__ . '::display_backend_basic_settings');
		
		add_action('plugin_advanced_settings_' . __CLASS__, __CLASS__ . '::display_backend_advanced_settings');
		
		add_action('plguin_help_settings_' . __CLASS__, __CLASS__ . '::display_backend_help_setting');
		
		add_action('admin_head' , __CLASS__ . '::backend_head');

		
	}
	private static function get_options($key = null){
		static $caches = null;
		if($caches === null)
			$caches = call_user_func(self::$plugin_options . '::get_options');
		if($key)
			return isset($caches[$key]) ? $caches[$key] : false;
		return $caches;
	}
	public static function backend_head(){
		if(!call_user_func([self::$plugin_options,'is_options_page']))
			return false;
	}
	public static function display_backend_help_setting(){
		$plugin_data = call_user_func([self::$plugin_options,'get_plugin_data']);
		?>
		<fieldset>
			<legend><?= __('Plugin Information',__CLASS__);?></legend>
			<table class="form-table">
				<tbody>
					<tr>
						<th><?= __('Plugin name: ',__CLASS__);?></th>
						<td>
							<strong><?= $plugin_data['Name'];?></strong>
						</td>
					</tr>
					<tr>
						<th><?= __('Plugin version: ',__CLASS__);?></th>
						<td>
							<?= $plugin_data['Version'];?>
						</td>
					</tr>
					<tr>
						<th><?= __('Plugin description: ',__CLASS__);?></th>
						<td>
							<?= $plugin_data['Description'];?>
						</td>
					</tr>
					<tr>
						<th><?= __('Plugin home page: ',__CLASS__);?></th>
						<td>
							<a href="<?= esc_url($plugin_data['PluginURI']);?>" target="_blank"><?= esc_html($plugin_data['PluginURI']);?></a>
						</td>
					</tr>
					<tr>
						<th><?= __('Author home page: ',__CLASS__);?></th>
						<td>
							<a href="<?= esc_url($plugin_data['AuthorURI']);?>" target="_blank"><?= esc_html($plugin_data['AuthorURI']);?></a>
						</td>
					</tr>
					<tr>
						<th scope="row"><?= __('Feedback and technical support: ',__CLASS__);?></th>
						<td>
							<p><?= __('E-Mail: ',__CLASS__);?><a href="mailto:kmvan.com@gmail.com">kmvan.com@gmail.com</a></p>
							<p>
								<?= __('QQ (for Chinese users): ',__CLASS__);?><a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=272778765&site=qq&menu=yes">272778765</a>
							</p>
							<p>
								<?= __('QQ Group (for Chinese users):',__CLASS__);?>
								<a href="http://wp.qq.com/wpa/qunwpa?idkey=d8c2be0e6c2e4b7dd2c0ff08d6198b618156d2357d12ab5dfbf6e5872f34a499" target="_blank">170306005</a>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?= __('Donate a coffee: ',__CLASS__);?></th>
						<td>
							<p>
								<a id="paypal_donate" href="https://www.paypal.com/cgi-bin/webscr" title="<?= __('Donation by Paypal',__CLASS__);?>">
									<img src=" https://www.paypalobjects.com/<?= WPLANG;?>/i/btn/btn_donate_LG.gif" alt="<?= __('Donation by Paypal',__CLASS__);?>"/>
								</a>
								<a id="alipay_donate" target="_blank" href="https://ww3.sinaimg.cn/thumb300/686ee05djw1eihtkzlg6mj216y16ydll.jpg" title="<?= __('Donation by Alipay',__CLASS__);?>">
									<img width="92" height="27"src="https://img.alipay.com/pa/img/home/logo-alipay-t.png" alt="<?= __('Donation by Alipay',__CLASS__);?>"/>
								</a>
							</p>
						</td>
					</tr>
				</tbody>
			</table>		
		</fieldset>
		<?php
	}
	public static function display_backend_basic_settings(){
		//$options = self::get_options();
		?>
		<fieldset>
			<legend><?= __('Optimization',__CLASS__);?></legend>
			<p class="description"><?= __('WordPoi will modify WordPress source codes to accelerate the PHP.',__CLASS__);?></p>
			<table class="form-table">
				<tbody>
					<tr>
						<th><?= __('Control',__CLASS__);?></th>
						<td>
							<a class="button button-primary" target="_blank" href="<?= esc_url(call_user_func(
								[
									self::$plugin_features,'get_process_url'
								],[
									'action' => __CLASS__,
									'type' => 'optimize',
								]
							));?>"><?= __('Start optimize',__CLASS__);?></a> <span class="description"><span class="dashicons dashicons-info"></span><?= __('Becareful! This operation is not reversible, please backup the original file before optimize.',__CLASS__);?></span>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<?php
	}
	public static function display_backend_advanced_settings(){
		?>
		<p>EMPTY</p>
		<?php
	}
	public static function process(){
		$type = isset($_GET['type']) && is_string($_GET['type']) ? $_GET['type'] : null;

		switch($type){
			/**
			 * optimize
			 */
			case 'optimize':
				if(!current_user_can('manage_options'))
					die;
				set_time_limit(0);
				
				self::glob_php_files(ABSPATH,'op_dirname2DIR');

				echo '<p>OK</p>';
				break;
		}
		die;
	}
	private static function op_dirname2DIR($filepath){
		
		$content = file_get_contents($filepath);
		$pattern = '/dirname\s*\(\s*__FILE__\s*\)/i';
		$new_content = preg_replace( $pattern, '__DIR__', $content );

		if( $content == $new_content )
			return false;

		echo '<p>dirname2DIR: ' . $filepath . '</p>';
		file_put_contents($filepath,$new_content);
		
		unset($content,$new_content);
	}
	private static function glob_php_files($dir,$callback){
		if(is_dir($dir)){
			$dirs = glob( $dir . '/*', GLOB_ONLYDIR );
			$dir_files = glob( $dir . '/*.php' );
			foreach( array_merge($dirs,$dir_files) as $filepath ){
				if(is_dir($filepath)){
					self::glob_php_files($filepath,$callback);
					continue;
				}else{
					self::$callback($filepath);
				}
			}
		}else{
			self::$callback($dir);
		}
	}
}