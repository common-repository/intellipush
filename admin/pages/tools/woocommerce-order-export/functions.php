<?php
	/**
	 * Tools: WooCommerce order export
	 * @since      1.0.0
	 */

	function intellipush_tools_woocommerceOrderExport_register_fields()  {
		// Get contactlist
		$contactlist = IntellipushHelper_getContactlist();
		if ($contactlist) {
			foreach ($contactlist as $key => $value) {
				$contactlist[$value->id] = $contactlist[$key]->list_name;
				unset($contactlist[$key]);
			}
		}
		array_unshift( $contactlist , '--- '. __('Create a new contactlist', 'intellipush') );
		// WooCommerce events
		if (IntellipushHelper_isWooCommerceActivated()) {
			$intellipush_tools_woocommerceOrderExport_field = array(
				array(
					'key' => 'field_intellipush_tools_woocommerceOrderExport_orderStatus',
					'label' => __('Order status', 'intellipush'),
					'name' => 'intellipush_tools_woocommerceOrderExport_orderStatus',
					'type' => 'select',
					'wrapper' => array(
						'width' => '50'
					),
					'choices' => array(
						'completed' 		=> __('Order completed', 'intellipush'),
						'pending' 			=> __('Order pending', 'intellipush'),
						'processing' 		=> __('Order processing', 'intellipush'),
						'on-hold' 			=> __('Order on hold', 'intellipush'),
						'refunded' 			=> __('Order refunded', 'intellipush'),
						'cancelled' 		=> __('Order cancelled', 'intellipush'),
						'failed' 			=> __('Order failed', 'intellipush')
					),
					'allow_null' => 0,
					'multiple' => 1,
					'ui' => 1,
					'ajax' => 0,
					'return_format' => 'value',
					'placeholder' => '',
				),
				array(
					'key' => 'field_intellipush_tools_woocommerceOrderExport_country',
					'label' => __('Country', 'intellipush'),
					'name' => 'intellipush_tools_woocommerceOrderExport_country',
					'type' => 'select',
					'wrapper' => array(
						'width' => '50'
					),
					'choices' => array(
						'NO' 		=> __('Norway', 'intellipush'),
						'SE' 		=> __('Sweden', 'intellipush'),
						'DK' 		=> __('Denmark', 'intellipush'),
						'BR' 		=> __('Brazil', 'intellipush'),
					),
					'allow_null' => 1,
					'multiple' => 0,
					'ui' => 1,
					'ajax' => 0,
					'return_format' => 'value',
					'placeholder' => '',
				),
				array(
					'key' => 'field_intellipush_tools_woocommerceOrderExport_dateAfter',
					'label' => __('Date after', 'intellipush'),
					'name' => 'intellipush_tools_woocommerceOrderExport_dateAfter',
					'type' => 'date_picker',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'display_format' => 'd/m/Y',
					'return_format' => 'd/m/Y',
					'first_day' => 1,
				),
				array(
					'key' => 'field_intellipush_tools_woocommerceOrderExport_dateBefore',
					'label' => __('Date before', 'intellipush'),
					'name' => 'intellipush_tools_woocommerceOrderExport_dateBefore',
					'type' => 'date_picker',
					'instructions' => '',
					'required' => 0,
					'wrapper' => array(
						'width' => '50',
						'class' => '',
						'id' => '',
					),
					'display_format' => 'd/m/Y',
					'return_format' => 'd/m/Y',
					'first_day' => 1,
				),
				array(
					'key' => 'field_intellipush_tools_woocommerceOrderExport_contactlist',
					'label' => __('Export to contactlist', 'intellipush'),
					'name' => 'intellipush_tools_woocommerceOrderExport_contactlist',
					'type' => 'select',
					'wrapper' => array(
						'width' => '50'
					),
					'choices' => $contactlist,
					'multiple' => 0,
					'ui' => 1,
				),
				array(
					'key' => 'field_intellipush_tools_woocommerceOrderExport_action',
					'type' => 'message',
					'required' => 0,
					'wrapper' => array(
						'width' => '50'
					),
					'message' => '<p class="ip--margin-top-30 ip--text-align-right">' .
							'<span class="ip--color-red ip--display-none status status-no-contactlist">' . __('Select a contactlist', 'intellipush') . '</span>' .
							'<span class="ip--color-red ip--display-none status status-no-order">' . __('Order not found, try to change some filters?', 'intellipush') . '</span>' .
							'<span class="ip--color-red ip--display-none status status-error"></span>' .
							'<span class="ip--color-highlight ip--display-none status status-in-progress">' . __('Exporting ', 'intellipush') . ' <span class="status-in-progress-exported">0</span>/<span class="status-in-progress-total">0</span></span>' .
							'<span class="ip--color-green ip--display-none status status-completed">' . __('Completed', 'intellipush') . ' <span class="status-completed-exported">0</span>/<span class="status-completed-total">0</span></span>' .
						'<a class="ip--margin-left-10 acf-button button button-primary" href="#">Export now</a></p>'
				),
			);
		} else {
			$intellipush_tools_woocommerceOrderExport_field = array(
				'parent' => 'group_intellipush_tools_woocommerceOrderExport',
				'key' => 'intellipush_tools_woocommerceOrderExport_field',
				'type' => 'message',
				'required' => 0,
				'conditional_logic' => 0,
				'message' => 'Please install <strong>WooCommerce</strong> plugin before using this tool',
				'esc_html' => 0,
			);
		}
		acf_add_local_field_group(array(
			'key' => 'group_intellipush_tools_woocommerceOrderExport',
			'title' => 'WooCommerce order export',
			'fields' => $intellipush_tools_woocommerceOrderExport_field,
			'location' => array (
				array (
					array (
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'intellipush-tools',
					),
				)
			),
			'menu_order' => 30,
		));
	}

	function intellipush_tools_woocommerceOrderExport_addToContactlist() {
		// https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query
		// https://github.com/woocommerce/woocommerce/wiki/Order-and-Order-Line-Item-Data
		// https://github.com/woocommerce/woocommerce/blob/master/includes/class-wc-order-query.php
		$args = array(
			'status' => $_POST['orderStatus'] ? $_POST['orderStatus'] :  array_keys(wc_get_order_statuses()),
			'date_before' => $_POST['dateBefore'] ? date('Y-m-d', strtotime($_POST['dateBefore'])) : null,
			'date_after' => $_POST['dateAfter'] ? date('Y-m-d', strtotime($_POST['dateAfter'])) : null,
			'billing_country' => $_POST['country'] ? $_POST['country'] : null,
			'paginate' => true,
			'return' => 'ids',
			'limit' => $_POST['limit'],
			'paged' => $_POST['paged']
		);

		
		$results = wc_get_orders( $args );

		$contactlist = $_POST['contactlist'];
		if ($contactlist === '0') {
			$create_contactlist = IntellipushHelper_createContactlist('WooCommerce order export ' . date('d/m/Y H:i:s'));
			if($create_contactlist->success) {
				$contactlist = $create_contactlist->id;
			} else {
				echo json_encode(array('error' => __('Can not create a new contactlist', 'intellipush')));
				wp_die();
			}
		}

		$contacts = array();
		foreach ($results->orders as $order_id) {
			$order = new WC_Order($order_id);
			$billing = $order->get_address('billing');
			$contacts[] = array(
				'name' => $billing['first_name'] . ' ' . $billing['last_name'],
				'country' => $billing['country'],
				'phoneNumber' => $billing['phone'],
				'email' => $billing['email'],
				'company' => $billing['company'],
				'zipCode' => $billing['postcode']
			);
		}
		IntellipushHelper_addToContactlistBatch($contacts, $contactlist);

	
		$return = array(
			'contactlist' => $contactlist,
			'orders' => $results->orders ? $results->orders : null,
			'paged' => (int)$_POST['paged'],
			'total' => (int)$results->total,
			'max_num_pages' => (int)$results->max_num_pages
		);
		echo json_encode($return);
		wp_die();
	}

	add_action('acf/init', 'intellipush_tools_woocommerceOrderExport_register_fields', 100);
	if (IntellipushHelper_isWooCommerceActivated() && IntellipushHelper_isAuth() && is_admin()) {
		wp_enqueue_script( 'intellipush_tools_woocommerceOrderExportScript', plugin_dir_url( __FILE__ ) . '/script.js', array ( 'jquery' ), '1.0.0', true);
		add_action( 'wp_ajax_intellipush_tools_woocommerceOrderExport_addToContactlist', 'intellipush_tools_woocommerceOrderExport_addToContactlist', 100 );
	}
?>
