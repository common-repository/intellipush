<?php
/**
 * Sidebar: Statistics
 * @since      1.0.0
 */
$Intellipush_isAuth = IntellipushHelper_isAuth();

$Intellipush_Statistics = IntellipushHelper_getStatistics();
$Intellipush_Statistics = $Intellipush_Statistics->success ? $Intellipush_Statistics->response->data->numberOf : null;

?>


<?php if($Intellipush_isAuth):?>
	<div class="postbox">
		<h2 class="hndle ip--cursor-default"><span><?php esc_attr_e('Statistics', 'intellipush');?></span></h2>
		<div class="inside">
			<ul>
				<li>
					<a href="<?php echo IntellipushHelper_getHomeUrl();?>/contacts/contacts" class="ip--text-decoration-none" target="_blank">
						<span class="dashicons dashicons-arrow-right"></span> 
						<?php echo sprintf(_n('%s contact', '%s contacts', $Intellipush_Statistics->contacts, 'intellipush'), $Intellipush_Statistics->contacts);?>
					</a>
				</li>
				<li>
					<a href="<?php echo IntellipushHelper_getHomeUrl();?>/contacts/contactlists" class="ip--text-decoration-none" target="_blank">
						<span class="dashicons dashicons-arrow-right"></span> 
						<?php echo sprintf(_n('%s contactlist', '%s contactlists', $Intellipush_Statistics->contactlists, 'intellipush'), $Intellipush_Statistics->contactlists);?>
					</a>
				</li>
				<li>
					<a href="<?php echo IntellipushHelper_getHomeUrl();?>/notifications" class="ip--text-decoration-none" target="_blank">
						<span class="dashicons dashicons-arrow-right"></span> 
						<?php echo sprintf(_n('%s scheduled messages', '%s scheduled messages', $Intellipush_Statistics->unsendtNotifications, 'intellipush'), $Intellipush_Statistics->unsendtNotifications);?>
					</a>
				</li>
			</ul>
		</div>
	</div>
<?php endif; ?>