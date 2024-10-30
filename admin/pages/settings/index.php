<?php
/**
 * Admin page: Settings
 * @since      1.0.0
 */
	if ( isset($_GET['settings-updated']) ) {
		echo('<meta http-equiv="refresh" content="0">');
		IntellipushHelper_clearCache();
		exit;
	}
	if ( isset($_GET['ctoken']) ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, IntellipushHelper_getHomeUrl().'/wpconnect/getconnection?ctoken='.$_GET['ctoken']);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		$result = $result ? json_decode($result, true) : null;
		curl_close($ch);

		if ($result && $result['success'] && $result['data'] && $result['data']['api_id'] && $result['data']['secret_key']) {
			update_option('intellipush_settings_api_id', $result['data']['api_id']);
			update_option('intellipush_settings_api_secret_key', $result['data']['secret_key']);
			IntellipushHelper_clearCache();
			ob_clean();
			wp_redirect(menu_page_url('intellipush-welcome', false));
			exit;
		}
	}
?>
<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<form method="post" action="options.php">
		<?php
			settings_fields($this->plugin_name . '_settings');
			do_settings_sections($this->plugin_name . '_settings');
		?>

		<?php if($Intellipush_isAuth):?>
			<p>
				<?php _e('You are authenticated as', 'intellipush'); ?> <strong><?php echo $Intellipush_isUserInfo->response->data->name;?></strong> 
				<?php if($Intellipush_isUserInfo->response->data->sub_account):?>
					<small>(<?php _e('Subaccount', 'intellipush'); ?>)</small>
				<?php endif;?>
			</p>
		<?php endif;?>
		<p>
			<small>
				This server IP address is <strong><?php echo IntellipushHelper_getServerIP();?></strong>, can be used in your 
				<a href="<?php echo IntellipushHelper_getHomeUrl();?>/settings/api" target="_blank">whitelist</a> or <a href="<?php echo IntellipushHelper_getHomeUrl();?>/developer/subaccounts" target="_blank">subaccount's whitelist</a>
			</small>
		</p>

		<?php if($Intellipush_isAuth):?>
			<p><small><a href="<?php echo IntellipushHelper_getCurrentUrl();?>&intellipush-clear-cache">Clear cache</a></small></p>
		<?php endif;?>

		<?php submit_button();?>
	</form>
</div>

