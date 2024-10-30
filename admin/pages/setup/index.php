<?php
/**
 * Admin page: Setup
 * @since      1.0.0
 */
?>
<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<div class="postbox splash">
		<div class="inside">
			<img src="<?php echo IntellipushHelper_getPluginUrl() . 'assets/logo-white.svg';?>" width="300">
			<h2>Setup wizard</h2>
			<p>Use Intellipush Connect to configure the plugin. Fast, easy, and secure. Click Intellipush Connect, log in or create a new account, then follow the steps on screen, and you will have SMS integrated with Wordpress in just a few clicks.</p>
			<p>
				<a href="<?php menu_page_url('intellipush-settings');?>" class="button ip--button-large">Manual setup</a>
				<a href="<?php echo IntellipushHelper_getConnectUrl();?>" class="button button-primary ip--button-large">Intellipush Connect <small><small>(Recommended)</small></small></a>
			</p>
		</div>
	</div>
</div>

