<?php
/**
 * Admin page: Welcome
 * @since      1.0.0
 */
?>
<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<div class="postbox splash">
		<div class="inside">
			<img src="<?php echo IntellipushHelper_getPluginUrl() . 'assets/logo-white.svg';?>" width="300">
			<h2>Congratulations!</h2>
			<p>You are now ready to start using Intellipush with your Wordpress. If you are using WooCommerce, you can easily set up automated triggers for when orders are changing status.</p>
			<p><i>Example: An order changes from status "processing" to status "shipped" an automated SMS is sendt to the customer informing of good stuff on the way :)</i></p>
			<p>
				<a href="<?php menu_page_url('intellipush');?>" class="button button-primary ip--button-large">Overview</a>
				<a href="<?php menu_page_url('intellipush-messages');?>" class="button ip--button-large">Send messages</a>
			</p>
		</div>
	</div>
</div>

