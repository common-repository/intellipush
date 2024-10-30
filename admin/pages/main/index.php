<?php
/**
 * Admin page: Main
 * @since      1.0.0
 */
?>
<div class="wrap">
	<h1><?php echo $GLOBALS['title']; ?></h1>
	<div id="poststuff">
		<div id="post-body" class="columns-2">
			<!-- Main -->
			<div id="post-body-content">
				<div class="postbox">
					<h2 class="hndle ip--cursor-default"><span><?php esc_attr_e('Dashboard', 'intellipush');?></span></h2>
					<div class="inside">
						<p><strong>Mobile marketing and professional SMS communication</strong></p>
						<p>Use intellipush to communicate personally and reliably with your customers. Give them special offers and update them with their order status.</p>
						<p>If you need help please don't hesitate to contact us over at: <a href="<?php echo IntellipushHelper_getHomeUrl();?>" target="_blank"><?php echo IntellipushHelper_getHomeUrl();?></a></p>
					</div>
				</div>

				<div class="postbox">
					<?php $Intellipush_contacts = IntellipushHelper_getContacts(10, 1, 'time_added', 'DESC'); ?>
					<h2 class="hndle ip--cursor-default"><span><?php esc_attr_e('Latest contacts', 'intellipush');?></span></h2>
					<div class="inside">
						<?php if(count($Intellipush_contacts)) : ?>
						<table class="wp-list-table widefat striped ip--margin-top-20">
							<thead>
								<tr>
									<th scope="col" class="column-primary"><?php esc_attr_e('Name', 'intellipush'); ?></th>
									<th scope="col"><?php esc_attr_e('Country', 'intellipush'); ?></th>
									<th scope="col"><?php esc_attr_e('Telephone', 'intellipush'); ?></th>
									<th scope="col"><?php esc_attr_e('Email', 'intellipush'); ?></th>
									<th scope="col"><?php esc_attr_e('Created', 'intellipush'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($Intellipush_contacts as $key => $value) :?>
									<tr>
										<td class="column-primary" data-colname="<?php esc_attr_e('Name', 'intellipush'); ?>">
											<strong><?php echo $value->name;?></strong>
											<button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_attr_e('Show more details', 'intellipush'); ?></span></button>
										</td>
										<td data-colname="<?php esc_attr_e('Country', 'intellipush'); ?>">
											<?php echo $value->country;?>
										</td>
										<td data-colname="<?php esc_attr_e('Telephone', 'intellipush'); ?>">
											<span class="ip--countrycode"><?php echo $value->countrycode;?></span><?php echo $value->phonenumber;?>
										</td>
										<td data-colname="<?php esc_attr_e('Email', 'intellipush'); ?>">
											<?php if($value->email):?>
												<a href="mailto:<?php echo $value->email;?>"><?php echo $value->email;?></a>
											<?php endif; ?>
										</td>
										<td data-colname="<?php esc_attr_e('Created', 'intellipush'); ?>">
											<?php echo date('d/m/Y', strtotime($value->time_added));?>
											<small>(<?php echo date('H:i', strtotime($value->time_added));?>)</small>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						<?php else : ?>
							<p><?php _e('You do not have any contact yet.', 'intellipush'); ?></p>
						<?php endif;?>
						<?php if(count($Intellipush_contacts) >= 10):?>
							<p class="ip--text-align-right">
								<a href="<?php echo IntellipushHelper_getHomeUrl();?>/contacts/contacts" class="button" target="_blank"><?php _e('View all contacts', 'intellipush'); ?></a>
							</p>
						<?php endif;?>
					</div>
				</div>

				<div class="postbox">
					<?php $Intellipush_shortUrls = IntellipushHelper_getShortUrls(10); ?>
					<h2 class="hndle ip--cursor-default"><span><?php esc_attr_e('Latest shorten URLs', 'intellipush');?></span></h2>
					<div class="inside">
						<?php if(count($Intellipush_shortUrls)) : ?>
						<table class="wp-list-table widefat striped ip--margin-top-20">
							<thead>
								<tr>
									<th scope="col" class="column-primary"><?php esc_attr_e('Long URL', 'intellipush'); ?></th>
									<th scope="col"><?php esc_attr_e('Short URL', 'intellipush'); ?></th>
									<th scope="col"><?php esc_attr_e('Child URLs', 'intellipush'); ?></th>
									<th scope="col"><?php esc_attr_e('Visits', 'intellipush'); ?> <small>(<?php esc_attr_e('Unique', 'intellipush'); ?>)</small></th>
									<th scope="col"><?php esc_attr_e('Created', 'intellipush'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($Intellipush_shortUrls as $key => $value) :?>
									<tr>
										<td class="column-primary" data-colname="<?php esc_attr_e('Long URL', 'intellipush'); ?>">
											<a href="<?php echo $value->long_url;?>" target="_blank"  class="latest-long-url"><strong><?php echo $value->long_url;?></strong></a>
											<button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_attr_e('Show more details', 'intellipush'); ?></span></button>
										</td>
										<td data-colname="<?php esc_attr_e('Short URL', 'intellipush'); ?>">
											<a href="<?php echo $value->short_url;?>" target="_blank"><?php echo $value->short_url;?></a>
										</td>
										<td data-colname="<?php esc_attr_e('Child URLs', 'intellipush'); ?>">
											<?php echo $value->child_urls ? $value->child_urls : '—';?>
										</td>
										<td data-colname="<?php esc_attr_e('Visits', 'intellipush'); ?> (<?php esc_attr_e('Unique', 'intellipush'); ?>)">
											<?php echo $value->visits;?>
											<small>(<?php echo $value->unique_visits;?>)</small>
										</td>
										<td data-colname="<?php esc_attr_e('Created', 'intellipush'); ?>">
											<?php echo date('d/m/Y', strtotime($value->created));?>
											<small>(<?php echo date('H:i', strtotime($value->created));?>)</small>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						<?php else : ?>
							<p><?php _e('You do not have any short url yet, try to send a message with shorten url?', 'intellipush'); ?></p>
						<?php endif;?>
						<?php if(count($Intellipush_shortUrls) >= 10):?>
							<p class="ip--text-align-right">
								<a href="<?php echo IntellipushHelper_getHomeUrl();?>/url" class="button" target="_blank"><?php _e('View more statistics and all shorten URLs', 'intellipush'); ?></a>
							</p>
						<?php endif;?>
					</div>
				</div>
			</div>
			<!-- Sidebar -->
			<div id="postbox-container-1" class="postbox-container">
				<?php include_once dirname(plugin_dir_path(__FILE__)) . '/_sidebar/account.php'; ?>
				<?php include_once dirname(plugin_dir_path(__FILE__)) . '/_sidebar/statistics.php'; ?>
			</div>
		</div>
		<br class="clear">
	</div>
	<style>
		.latest-long-url {
			display: block;
			width: 33vw;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		@media screen and (max-width: 1370px) {
			.latest-long-url {
				width: 20vw;
			}
		}
		@media screen and (max-width: 782px) {
			.latest-long-url {
				width: 70vw;
			}
		}
	</style>
</div>