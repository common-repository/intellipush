<?php
	/**
	 * Tools: WooCommerce events
	 * @since      1.0.0
	 */

	function intellipush_tools_woocommerceEvents_register_fields()  {
		if (IntellipushHelper_isWooCommerceActivated()) {
			// Get contactlist
			$contactlist = IntellipushHelper_getContactlist();
			if ($contactlist) {
				foreach ($contactlist as $key => $value) {
					$contactlist[$value->id] = $contactlist[$key]->list_name;
					unset($contactlist[$key]);
				}
			}

			// Get message templates
			$messageTemplates = get_field('intellipush_messages_templates', 'option');
			if ($messageTemplates) {
				foreach ($messageTemplates as $key => $value) {
					$messageTemplates[$value['id']] = $messageTemplates[$key]['name'];
					unset($messageTemplates[$key]);
				}
			}
		}



		// WooCommerce events
		if (IntellipushHelper_isWooCommerceActivated()) {
			$intellipush_tools_woocommerceEvents_field = array(
				'parent' => 'group_intellipush_tools_woocommerceEvents',
				'key' => 'field_intellipush_tools_woocommerceEvents',
				'name' => 'intellipush_tools_woocommerceEvents',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'layout' => 'block',
				'button_label' => 'Add event',
				'sub_fields' => array(
					array(
						'key' => 'field_intellipush_tools_woocommerceEvents_name',
						'label' => 'Name',
						'name' => 'name',
						'type' => 'text',
						'required' => 1,
						'wrapper' => array(
							'width' => '33'
						)
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceEvents_event',
						'label' => __('Event', 'intellipush'),
						'name' => 'event',
						'type' => 'select',
						'required' => 1,
						'choices' => array(
							'order-completed' 		=> __('Order completed', 'intellipush'),
							'order-pending' 		=> __('Order pending', 'intellipush'),
							'order-processing' 		=> __('Order processing', 'intellipush'),
							'order-on-hold' 		=> __('Order on hold', 'intellipush'),
							'order-refunded' 		=> __('Order refunded', 'intellipush'),
							'order-cancelled' 		=> __('Order cancelled', 'intellipush'),
							'order-failed' 			=> __('Order failed', 'intellipush')
						),
						'ui' => 1,
						'return_format' => 'value',
						'wrapper' => array(
							'width' => '33'
						)
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceEvents_action',
						'label' => __('Action', 'intellipush'),
						'name' => 'action',
						'type' => 'select',
						'required' => 1,
						'choices' => array(
							'add-contact' 				=> __('Add contact', 'intellipush'),
							'add-to-contactlist' 		=> __('Add to contactlist', 'intellipush'),
							'delete-from-contactlist' 	=> __('Delete from contactlist', 'intellipush'),
							'send-message' 				=> __('Send message', 'intellipush'),
							'delete-scheduled-message' 	=> __('Delete scheduled message', 'intellipush')
						),
						'ui' => 1,
						'return_format' => 'value',
						'wrapper' => array(
							'width' => '33'
						)
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceEvents_target',
						'label' => __('Target', 'intellipush') . ' <small class="acf-accordion-view ip--font-weight-normal ip--color-link">(' . __('view', 'intellipush') . ')</small>',
						'name' => '',
						'type' => 'accordion',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'add-to-contactlist'
								)
							),
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'delete-from-contactlist'
								)
							),
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'send-message'
								)
							),
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'delete-scheduled-message'
								)
							)
						),
						'open' => 0,
						'multi_expand' => 0,
						'endpoint' => 0,
						'required' => 1
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceEvents_target_contactlist',
						'label' => __('Contactlist', 'intellipush'),
						'name' => 'target',
						'type' => 'select',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'add-to-contactlist'
								)
							),
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'delete-from-contactlist'
								)
							),
						),
						'choices' => $contactlist,
						'multiple' => 0,
						'allow_null' => 1,
						'ui' => 1,
						'return_format' => 'value'
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceEvents_target_messageTemplates',
						'label' => __('Message template', 'intellipush'),
						'name' => 'target',
						'type' => 'select',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'send-message'
								)
							),
						),
						'choices' => $messageTemplates,
						'multiple' => 0,
						'allow_null' => 1,
						'ui' => 1,
						'return_format' => 'value'
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceEvents_target_scheduledMessageTemplates',
						'label' => __('Scheduled message template', 'intellipush'),
						'name' => 'target',
						'type' => 'select',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'delete-scheduled-message'
								)
							),
						),
						'choices' => $messageTemplates,
						'multiple' => 0,
						'allow_null' => 1,
						'ui' => 1,
						'return_format' => 'value'
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceEvents_target_delay',
						'label' => __('Delay', 'intellipush'),
						'name' => 'delay',
						'type' => 'number',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'send-message'
								)
							)
						),
						'wrapper' => array(
							'width' => '50'
						),
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => __('minutes', 'intellipush'),
						'min' => '0',
						'max' => '',
						'step' => '',
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceEvents_target_repeat',
						'label' => __('Repeat', 'intellipush'),
						'name' => 'repeat',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceEvents_action',
									'operator' => '==',
									'value' => 'send-message'
								)
							)
						),
						'wrapper' => array(
							'width' => '50'
						),
						'choices' => array(
							'never' 	=> __('Never', 'intellipush'),
							'daily' 	=> __('Daily', 'intellipush'),
							'weekly' 	=> __('Weekly', 'intellipush'),
							'monthly' 	=> __('Monthly', 'intellipush')
						),
						'ui' => 1,
						'return_format' => 'value',
					)
				),
			);
		} else {
			$intellipush_tools_woocommerceEvents_field = array(
				'parent' => 'group_intellipush_tools_woocommerceEvents',
				'key' => 'field_intellipush_tools_woocommerceEvents',
				'type' => 'message',
				'required' => 0,
				'conditional_logic' => 0,
				'message' => 'Please install <strong>WooCommerce</strong> plugin before using this tool',
				'esc_html' => 0,
			);
		}
		acf_add_local_field_group(array(
			'key' => 'group_intellipush_tools_woocommerceEvents',
			'title' => 'WooCommerce events',
			'fields' => array ($intellipush_tools_woocommerceEvents_field),
			'location' => array (
				array (
					array (
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'intellipush-tools',
					),
				)
			),
			'menu_order' => 10,
		));
	}

	function intellipush_tools_woocommerceEvents_event_doAction($eventName, $order_id) {
		$events = get_field('intellipush_tools_woocommerceEvents', 'option');
		foreach ($events as $key => $value) {
			if ($value['event'] === $eventName) {
				switch($value['action']) {
					case 'add-contact':
						intellipush_tools_woocommerceEvents_event_addContact($order_id);
						break;
					case 'add-to-contactlist':
						intellipush_tools_woocommerceEvents_event_addToContactlist($order_id, $value);
						break;
					case 'delete-from-contactlist':
						intellipush_tools_woocommerceEvents_event_deleteFromContactlist($order_id, $value);
						break;
					case 'send-message':
						intellipush_tools_woocommerceEvents_event_sendMessage($order_id, $value);
						break;
					case 'delete-scheduled-message':
						intellipush_tools_woocommerceEvents_event_deleteScheduledMessage($order_id, $value);
						break;
				}
			}
		}
	}

	function intellipush_tools_woocommerceEvents_event_addContact($order_id) {
		$order = new WC_Order($order_id);
		$billing = $order->get_address('billing');
		IntellipushHelper_addContact(
			$billing['first_name'] . ' ' . $billing['last_name'],
			$billing['country'],
			$billing['phone'],
			$billing['email'],
			$billing['company'],
			$billing['postcode']
		);
	}

	function intellipush_tools_woocommerceEvents_event_addToContactlist($order_id, $event) {
		$order = new WC_Order($order_id);
		$billing = $order->get_address('billing');
		$contact = IntellipushHelper_addContact(
			$billing['first_name'] . ' ' . $billing['last_name'],
			$billing['country'],
			$billing['phone'],
			$billing['email'],
			$billing['company'],
			$billing['postcode']
		);
		if ($contact->success) {
			IntellipushHelper_addToContactlist($contact->id, $event['target']);
		}
	}

	function intellipush_tools_woocommerceEvents_event_deleteFromContactlist($order_id, $event) {
		$order = new WC_Order($order_id);
		$billing = $order->get_address('billing');
		IntellipushHelper_deleteFromContactlist($event['target'], $billing['country'], $billing['phone']);
	}

	function intellipush_tools_woocommerceEvents_event_sendMessage($order_id, $event) {
		$order = new WC_Order($order_id);
		$billing = $order->get_address('billing');
		$messageTemplates = get_field('intellipush_messages_templates', 'option');
		if ($messageTemplates) {
			foreach ($messageTemplates as $key => $value) {
				if($event['target'] === $value['id']) {
					$message = intellipush_tools_woocommerceEvents_event_renderedMessage($value['message'], $order);
					$countryPhoneCode = IntellipushHelper_getCountryInfoByContryName($billing['country'])['phoneCode'];
					if($countryPhoneCode) {
						IntellipushHelper_sendMessage($message, array(array($countryPhoneCode, $billing['phone'])), $event['delay'],  $event['repeat']);
					}
				}
			}
		}
	}


	function intellipush_tools_woocommerceEvents_event_renderedMessage($message, $order) {
		global $shortcode_tags;
		$GLOBALS['intellipush_tools_woocommerceEvents_event_renderedMessage_order'] = $order;
		$tags = array(
			'wc_order_name' => function($atts) {
				global $intellipush_tools_woocommerceEvents_event_renderedMessage_order;
				extract( shortcode_atts( array(
					'default' => ''
				), $atts ) );
				$billing = $intellipush_tools_woocommerceEvents_event_renderedMessage_order->get_address('billing');
				$result = $billing['first_name'] . ' ' . $billing['last_name'];
				return $result ? $result : esc_attr($default);
			}
		);
		$shortcode_tags = array_merge($shortcode_tags, $tags);
		$rendered_message = do_shortcode($message);
		foreach($tags as $k=>$v){unset($shortcode_tags[$k]);}
		$GLOBALS['intellipush_tools_woocommerceEvents_event_renderedMessage_order'] = null;
		return $rendered_message;
	}



	function intellipush_tools_woocommerceEvents_event_deleteScheduledMessage($order_id, $event) {
		$order = new WC_Order($order_id);
		$billing = $order->get_address('billing');
		$messageTemplates = get_field('intellipush_messages_templates', 'option');
		if ($messageTemplates) {
			foreach ($messageTemplates as $key => $value) {
				if($event['target'] === $value['id']) {
					IntellipushHelper_deleteScheduledMessage($value['message'], $billing['country'], $billing['phone'], 50);
				}
			}
		}
	}

	function intellipush_tools_woocommerceEvents_event_orderCompleted($order_id) {
		intellipush_tools_woocommerceEvents_event_doAction('order-completed', $order_id);
	}
	function intellipush_tools_woocommerceEvents_event_orderPending ($order_id) {
		intellipush_tools_woocommerceEvents_event_doAction('order--pending', $order_id);
	}
	function intellipush_tools_woocommerceEvents_event_orderProcessing($order_id) {
		intellipush_tools_woocommerceEvents_event_doAction('order-processing', $order_id);
	}
	function intellipush_tools_woocommerceEvents_event_orderOnHold ($order_id) {
		intellipush_tools_woocommerceEvents_event_doAction('order-on-hold', $order_id);
	}
	function intellipush_tools_woocommerceEvents_event_orderRefunded ($order_id) {
		intellipush_tools_woocommerceEvents_event_doAction('order-refunded', $order_id);
	}
	function intellipush_tools_woocommerceEvents_event_orderCancelled ($order_id) {
		intellipush_tools_woocommerceEvents_event_doAction('order-cancelled', $order_id);
	}
	function intellipush_tools_woocommerceEvents_event_orderFailed ($order_id) {
		intellipush_tools_woocommerceEvents_event_doAction('order-failed', $order_id);
	}

	add_action('acf/init', 'intellipush_tools_woocommerceEvents_register_fields', 100);
	if (IntellipushHelper_isWooCommerceActivated() && IntellipushHelper_isAuth()) {
		add_action('woocommerce_order_status_completed',    'intellipush_tools_woocommerceEvents_event_orderCompleted', 100);
		add_action('woocommerce_order_status_pending',      'intellipush_tools_woocommerceEvents_event_orderPending', 100);
		add_action('woocommerce_order_status_processing',   'intellipush_tools_woocommerceEvents_event_orderProcessing', 100);
		add_action('woocommerce_order_status_on-hold',      'intellipush_tools_woocommerceEvents_event_orderOnHold', 100);
		add_action('woocommerce_order_status_refunded',     'intellipush_tools_woocommerceEvents_event_orderRefunded', 100);
		add_action('woocommerce_order_status_cancelled',    'intellipush_tools_woocommerceEvents_event_orderCancelled', 100);
		add_action('woocommerce_order_status_failed',       'intellipush_tools_woocommerceEvents_event_orderFailed', 100);
	}
?>
