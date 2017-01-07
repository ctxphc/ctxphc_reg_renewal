<?php
/**
 * Created by PhpStorm.
 * User: ken_kilgore1
 * Date: 3/29/2015
 * Time: 8:51 AM
 */

function __construct() {
	define('CLUB_MEMBERSHIP__VERSION', $this->plugin_version);
	define('CLUB_MEMBERSHIP__SITE_URL', site_url());
	define('CLUB_MEMBERSHIP__HOME_URL', home_url());
	define('CLUB_MEMBERSHIP__URL', $this->plugin_url());
	define('CLUB_MEMBERSHIP__PATH', $this->plugin_path());
	$debug_enabled = get_option('CLUB_MEMBERSHIP__enable_debug');
	if (isset($debug_enabled) && !empty($debug_enabled)) {
		define('CLUB_MEMBERSHIP__DEBUG', true);
	} else {
		define('CLUB_MEMBERSHIP__DEBUG', false);
	}
	$use_sandbox = get_option('CLUB_MEMBERSHIP__enable_testmode');
	if (isset($use_sandbox) && !empty($use_sandbox)) {
		define('CLUB_MEMBERSHIP__USE_SANDBOX', true);
	} else {
		define('CLUB_MEMBERSHIP__USE_SANDBOX', false);
	}
	define('CLUB_MEMBERSHIP__DEBUG_LOG_PATH', $this->debug_log_path());
	$this->plugin_includes();
	$this->loader_operations();
}

function plugin_includes() {
	include_once('wp-paypal-order.php');
	include_once('paypal-ipn.php');
}

function loader_operations() {
	register_activation_hook(__FILE__, array($this, 'activate_handler'));
	add_action('plugins_loaded', array($this, 'plugins_loaded_handler'));
	if (is_admin()) {
		add_filter('plugin_action_links', array($this, 'add_plugin_action_links'), 10, 2);
	}
	add_action('admin_notices', array($this, 'admin_notice'));
	add_action('wp_enqueue_scripts', array($this, 'plugin_scripts'));
	add_action('admin_menu', array($this, 'add_options_menu'));
	add_action('init', array($this, 'plugin_init'));
	add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
	add_filter('manage_club_membership_order_posts_columns', 'club_membership_order_columns');
	add_action('manage_club_membership_order_posts_custom_column', 'club_membership_custom_column', 10, 2);
	add_shortcode('club_membership_', 'club_membership_button_handler');
}

function plugins_loaded_handler() {  //Runs when plugins_loaded action gets fired
	$this->check_upgrade();
}

function admin_notice() {
	if (CLUB_MEMBERSHIP__DEBUG) {  //debug is enabled. Check to make sure log file is writable
		$real_file = CLUB_MEMBERSHIP__DEBUG_LOG_PATH;
		if (!is_writeable($real_file)) {
			echo '<div class="updated"><p>' . __('WP PayPal Debug log file is not writable. Please check to make sure that it has the correct file permission (ideally 644). Otherwise the plugin will not be able to write to the log file. The log file (log.txt) can be found in the root directory of the plugin - ', 'wp-paypal') . '<code>' . CLUB_MEMBERSHIP__URL . '</code></p></div>';
		}
	}
}

function activate_handler() {
	add_option('club_membership_plugin_version', $this->plugin_version);
	add_option('club_membership_email', get_bloginfo('admin_email'));
	add_option('club_membership_currency_code', 'USD');
}

function check_upgrade() {
	if (is_admin()) {
		$plugin_version = get_option('club_membership_plugin_version');
		if (!isset($plugin_version) || $plugin_version != $this->plugin_version) {
			$this->activate_handler();
			update_option('club_membership_plugin_version', $this->plugin_version);
		}
	}
}

function plugin_init() {
	//register orders
	club_membership_order_page();
	//process PayPal IPN
	club_membership_process_ipn();
}

function add_meta_boxes() {
	//add_meta_box('wp-paypal-order-box', __('Edit PayPal Order', 'wp-paypal'), 'club_membership_order_meta_box', 'club_membership_order', 'normal', 'high');
}

function plugin_scripts() {
	if (!is_admin()) {

	}
}

function plugin_url() {
	if ($this->plugin_url)
		return $this->plugin_url;
	return $this->plugin_url = plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__));
}

function plugin_path() {
	if ($this->plugin_path)
		return $this->plugin_path;
	return $this->plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
}

function debug_log_path() {
	return CLUB_MEMBERSHIP__PATH . '/log.txt';
}

function add_plugin_action_links($links, $file) {
	if ($file == plugin_basename(dirname(__FILE__) . '/main.php')) {
		$links[] = '<a href="options-general.php?page=wp-paypal-settings">Settings</a>';
	}
	return $links;
}

function add_options_menu() {
	if (is_admin()) {
		add_submenu_page('edit.php?post_type=club_membership_order', __('Settings', 'wp-paypal'), __('Settings', 'wp-paypal'), 'manage_options', 'wp-paypal-settings', array($this, 'options_page'));
		add_submenu_page('edit.php?post_type=club_membership_order', __('Debug', 'wp-paypal'), __('Debug', 'wp-paypal'), 'manage_options', 'wp-paypal-debug', array($this, 'debug_page'));
	}
}

function options_page() {
	$plugin_tabs = array(
		'wp-paypal-settings' => 'General'
	);
	echo '<div class="wrap">' . screen_icon() . '<h2>WP PayPal v' . CLUB_MEMBERSHIP__VERSION . '</h2>';
	echo '<div class="update-nag">Please visit the <a target="_blank" href="http://wphowto.net/wordpress-paypal-plugin">WP PayPal</a> documentation page for usage instructions.</div>';
	echo '<div id="poststuff"><div id="post-body">';

	if (isset($_GET['page'])) {
		$current = $_GET['page'];
		if (isset($_GET['action'])) {
			$current .= "&action=" . $_GET['action'];
		}
	}
	$content = '';
	$content .= '<h2 class="nav-tab-wrapper">';
	foreach ($plugin_tabs as $location => $tabname) {
		if ($current == $location) {
			$class = ' nav-tab-active';
		} else {
			$class = '';
		}
		$content .= '<a class="nav-tab' . $class . '" href="?page=' . $location . '">' . $tabname . '</a>';
	}
	$content .= '</h2>';
	echo $content;

	$this->general_settings();

	echo '</div></div>';
	echo '</div>';
}

function general_settings() {
	if (isset($_POST['club_membership_update_settings'])) {
		$nonce = $_REQUEST['_wpnonce'];
		if (!wp_verify_nonce($nonce, 'club_membership_general_settings')) {
			wp_die('Error! Nonce Security Check Failed! please save the settings again.');
		}
		update_option('club_membership_enable_testmode', (isset($_POST["enable_testmode"]) && $_POST["enable_testmode"] == '1') ? '1' : '');
		update_option('club_membership_email', trim($_POST["paypal_email"]));
		update_option('club_membership_currency_code', trim($_POST["currency_code"]));
		echo '<div id="message" class="updated fade"><p><strong>';
		echo 'Settings Saved!';
		echo '</strong></p></div>';
	}
	?>

	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<?php wp_nonce_field('club_membership_general_settings'); ?>

		<table class="form-table">

			<tbody>

			<tr valign="top">
				<th scope="row">Enable Test Mode</th>
				<td> <fieldset><legend class="screen-reader-text"><span>Enable Test Mode</span></legend><label for="enable_testmode">
							<input name="enable_testmode" type="checkbox" id="enable_testmode" <?php if (get_option('club_membership_enable_testmode') == '1') echo ' checked="checked"'; ?> value="1">
							Check this option if you want to enable PayPal sandbox for testing</label>
					</fieldset></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="paypal_email">PayPal Email</label></th>
				<td><input name="paypal_email" type="text" id="paypal_email" value="<?php echo get_option('club_membership_email'); ?>" class="regular-text">
					<p class="description">Your PayPal email address</p></td>
			</tr>

			<tr valign="top">
				<th scope="row"><label for="currency_code">Currency Code</label></th>
				<td><input name="currency_code" type="text" id="currency_code" value="<?php echo get_option('club_membership_currency_code'); ?>" class="regular-text">
					<p class="description">The currency of the payment (example: USD, CAD, GBP, EUR)</p></td>
			</tr>

			</tbody>

		</table>

		<p class="submit"><input type="submit" name="club_membership_update_settings" id="club_membership_update_settings" class="button button-primary" value="Save Changes"></p></form>

	<?php
}

function debug_page() {
	?>
	<div class="wrap">
		<h2>WP PayPal Debug Log</h2>
		<div id="poststuff">
			<div id="post-body">
				<?php
				if (isset($_POST['club_membership_update_log_settings'])) {
					$nonce = $_REQUEST['_wpnonce'];
					if (!wp_verify_nonce($nonce, 'club_membership_debug_log_settings')) {
						wp_die('Error! Nonce Security Check Failed! please save the settings again.');
					}
					update_option('club_membership_enable_debug', (isset($_POST["enable_debug"]) && $_POST["enable_debug"] == '1') ? '1' : '');
					echo '<div id="message" class="updated fade"><p>Settings Saved!</p></div>';
				}
				if (isset($_POST['club_membership_reset_log'])) {
					$nonce = $_REQUEST['_wpnonce'];
					if (!wp_verify_nonce($nonce, 'club_membership_reset_log_settings')) {
						wp_die('Error! Nonce Security Check Failed! please save the settings again.');
					}
					if (club_membership_reset_log()) {
						echo '<div id="message" class="updated fade"><p>Debug log file has been reset!</p></div>';
					} else {
						echo '<div id="message" class="error"><p>Debug log file could not be reset!</p></div>';
					}
				}
				$real_file = CLUB_MEMBERSHIP__DEBUG_LOG_PATH;
				$content = file_get_contents($real_file);
				$content = esc_textarea($content);
				?>
				<div id="template"><textarea cols="70" rows="25" name="club_membership_log" id="club_membership_log"><?php echo $content; ?></textarea></div>
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<?php wp_nonce_field('club_membership_debug_log_settings'); ?>
					<table class="form-table">
						<tbody>
						<tr valign="top">
							<th scope="row">Enable Debug</th>
							<td> <fieldset><legend class="screen-reader-text"><span>Enable Debug</span></legend><label for="enable_debug">
										<input name="enable_debug" type="checkbox" id="enable_debug" <?php if (get_option('club_membership_enable_debug') == '1') echo ' checked="checked"'; ?> value="1">
										Check this option if you want to enable debug</label>
								</fieldset></td>
						</tr>

						</tbody>

					</table>
					<p class="submit"><input type="submit" name="club_membership_update_log_settings" id="club_membership_update_log_settings" class="button button-primary" value="Save Changes"></p>
				</form>
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<?php wp_nonce_field('club_membership_reset_log_settings'); ?>
					<p class="submit"><input type="submit" name="club_membership_reset_log" id="club_membership_reset_log" class="button" value="Reset Log"></p>
				</form>
			</div>
		</div>
	</div>
	<?php
}

$GLOBALS['club_membership'] = new CLUB_MEMBERSHIP();

function club_membership_button_handler($atts) {
	$testmode = get_option('club_membership_enable_testmode');
	if (isset($testmode) && !empty($testmode)) {
		$atts['env'] = "sandbox";
	}
	$atts['callback'] = home_url() . '/?club_membership_ipn=1';
	$paypal_email = get_option('club_membership_email');
	$currency = get_option('club_membership_currency_code');
	if (isset($atts['currency']) && !empty($atts['currency'])) {

	} else {
		$atts['currency'] = $currency;
	}
	$button_code = '<div>';
	$button_code .= '<script src="' . CLUB_MEMBERSHIP__URL . '/lib/paypal-button.min.js?merchant=' . $paypal_email . '"';
	foreach ($atts as $key => $value) {
		$button_code .= 'data-' . $key . '="' . $value . '"';
	}
	$button_code .= 'async';
	$button_code .= '></script>';
	$button_code .= '</div>';
	return $button_code;
}

function club_membership_debug_log($msg, $success, $end = false) {
	if (!CLUB_MEMBERSHIP__DEBUG) {
		return;
	}
	$date_time = date('F j, Y g:i a');//the_date('F j, Y g:i a', '', '', FALSE);
	$text = '[' . $date_time . '] - ' . (($success) ? 'SUCCESS :' : 'FAILURE :') . $msg . "\n";
	if ($end) {
		$text .= "\n------------------------------------------------------------------\n\n";
	}
	// Write to log.txt file
	$fp = fopen(CLUB_MEMBERSHIP__DEBUG_LOG_PATH, 'a');
	fwrite($fp, $text);
	fclose($fp);  // close file
}

function club_membership_debug_log_array($array_msg, $success, $end = false) {
	if (!CLUB_MEMBERSHIP__DEBUG) {
		return;
	}
	$date_time = date('F j, Y g:i a');//the_date('F j, Y g:i a', '', '', FALSE);
	$text = '[' . $date_time . '] - ' . (($success) ? 'SUCCESS :' : 'FAILURE :') . "\n";
	ob_start();
	print_r($array_msg);
	$var = ob_get_contents();
	ob_end_clean();
	$text .= $var;
	if ($end) {
		$text .= "\n------------------------------------------------------------------\n\n";
	}
	// Write to log.txt file
	$fp = fopen(CLUB_MEMBERSHIP__DEBUG_LOG_PATH, 'a');
	fwrite($fp, $text);
	fclose($fp);  // close filee
}

function club_membership_reset_log() {
	$log_reset = true;
	$date_time = date('F j, Y g:i a');//the_date('F j, Y g:i a', '', '', FALSE);
	$text = '[' . $date_time . '] - SUCCESS : Log reset';
	$text .= "\n------------------------------------------------------------------\n\n";
	$fp = fopen(CLUB_MEMBERSHIP__DEBUG_LOG_PATH, 'w');
	if ($fp != FALSE) {
		@fwrite($fp, $text);
		@fclose($fp);
	} else {
		$log_reset = false;
	}
	return $log_reset;
}
