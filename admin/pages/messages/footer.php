<div id="intellipush-messages-shortcodes" class="ip--display-none">
	<h3>Message shortcodes</h3>
	<p><small>You can use <strong>shortcodes for return values</strong> like <code><small>[wc_order_name]</small></code>, this will return something like <code><small>John Johansen</small></code> when using in a right templates <small>(Can use in)</small> and you can also set the <strong>attributes</strong> <small>(not required)</small> for taking more controll like <code><small>Hello [wc_order_name default="there"]</small></code> for sure, that means if it can't find order name it will return <code><small>Hello there</small></code> by default.</small></p>
	<table class="wp-list-table widefat striped ip--margin-top-20">
		<thead>
			<tr>
				<th scope="col" class="column-primary"><?php esc_attr_e('Shortcode', 'intellipush'); ?></th>
				<th scope="col"><?php esc_attr_e('Attributes', 'intellipush'); ?></th>
				<th scope="col"><?php esc_attr_e('Can use in', 'intellipush'); ?></th>
				<th scope="col"><?php esc_attr_e('Return', 'intellipush'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="column-primary" data-colname="<?php esc_attr_e('Shortcode', 'intellipush'); ?>">
					<code>[wc_order_name]</code>
					<button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_attr_e('Show more details', 'intellipush'); ?></span></button>
				</td>
				<td data-colname="<?php esc_attr_e('Attributes', 'intellipush'); ?>">
					<span title='[wc_order_name default="there"]'>default</span>
				</td>
				<td data-colname="<?php esc_attr_e('Can use in', 'intellipush'); ?>">
					WooCommerce events<br>
					WooCommerce cart abandoment
				</td>
				<td data-colname="<?php esc_attr_e('Return', 'intellipush'); ?>">
					Billing full name
				</td>
			</tr>
			<tr>
				<td class="column-primary" data-colname="<?php esc_attr_e('Shortcode', 'intellipush'); ?>">
					<code>[wc_cart_abandonment_url]</code>
					<button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_attr_e('Show more details', 'intellipush'); ?></span></button>
				</td>
				<td data-colname="<?php esc_attr_e('Attributes', 'intellipush'); ?>">
					<span title='[wc_order_name empty_cart="yes"]'>empty_cart</span>
				</td>
				<td data-colname="<?php esc_attr_e('Can use in', 'intellipush'); ?>">
					WooCommerce cart abandoment
				</td>
				<td data-colname="<?php esc_attr_e('Return', 'intellipush'); ?>">
					Cart url with added products
				</td>
			</tr>
		</tbody>
	</table>
</div>