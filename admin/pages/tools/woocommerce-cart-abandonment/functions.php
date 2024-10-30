<?php
	/**
	 * Tools: WooCommerce Cart Abandonment
	 * @since      1.0.0
	 */

	function intellipush_tools_woocommerceCartAbandonment_ajax() {
		$events = get_field('intellipush_tools_woocommerceCartAbandonment', 'option');
		foreach ($events as $key => $event) {
			switch($event['action']) {
				case 'add-contact':
					//intellipush_tools_woocommerceEvents_event_addContact($order_id);
					break;
				case 'add-to-contactlist':
					//intellipush_tools_woocommerceEvents_event_addToContactlist($order_id, $value);
					break;
				case 'send-message':
					$messageTemplates = get_field('intellipush_messages_templates', 'option');
					if ($messageTemplates) {
						foreach ($messageTemplates as $key => $template) {
							if($event['target'] === $template['id']) {
								$message = intellipush_tools_woocommerceCartAbandonment_renderedMessage($template['message'], $_POST);
								$countryPhoneCode = IntellipushHelper_getCountryInfoByContryName($_POST['country'])['phoneCode'];
								IntellipushHelper_sendMessage($message, array(array($countryPhoneCode, $_POST['phone'])), $event['delay']);
							}
						}
					}
					break;
			}
		}
		$response = array('success' => 1);
		echo json_encode($response);
		wp_die();
	}

	function intellipush_tools_woocommerceCartAbandonment_renderedMessage($message, $params) {
		global $shortcode_tags;
		$GLOBALS['intellipush_tools_woocommerceCartAbandonment_renderedMessage_params'] = $params;
		$tags = array(
			'wc_order_name' => function($atts) {
				global $intellipush_tools_woocommerceCartAbandonment_renderedMessage_params;
				extract( shortcode_atts( array(
					'default' => ''
				), $atts ) );
				$result = $intellipush_tools_woocommerceCartAbandonment_renderedMessage_params['name'];
				return $result ? $result : esc_attr($default);
			},
			'wc_cart_abandonment_url' => function($atts) {
				global $intellipush_tools_woocommerceCartAbandonment_renderedMessage_params;
				extract( shortcode_atts( array(
					'empty_cart' => ''
				), $atts ) );
				$result = wc_get_page_permalink('cart');
				$mergeCartCode = $intellipush_tools_woocommerceCartAbandonment_renderedMessage_params['merge_cart_code'];
				if ($mergeCartCode) {
					$result .= (parse_url($result, PHP_URL_QUERY) ? '&' : '?') . 'ip_merge_cart=' . $mergeCartCode;
					if ($empty_cart === 'yes') {
						$result .= (parse_url($result, PHP_URL_QUERY) ? '&' : '?') . 'ip_empty_cart';
					}
					$result = IntellipushHelper_createShortUrl($result);
					$result = $result->success ? $result->response->data->short_url : wc_get_page_permalink('cart');
				}
				return $result;
			}
		);
		$shortcode_tags = array_merge($shortcode_tags, $tags);
		$rendered_message = do_shortcode($message);
		foreach($tags as $k=>$v){unset($shortcode_tags[$k]);}
		$GLOBALS['intellipush_tools_woocommerceCartAbandonment_renderedMessage_params'] = null;
		return $rendered_message;
	}

	function intellipush_tools_woocommerceCartAbandonment_cancel($order_id) {
		$order = new WC_Order($order_id);
		$billing = $order->get_address('billing');
		$events = get_field('intellipush_tools_woocommerceCartAbandonment', 'option');
		foreach ($events as $key => $event) {
			switch($event['action']) {
				case 'add-contact':
					//intellipush_tools_woocommerceEvents_event_addContact($order_id);
					break;
				case 'add-to-contactlist':
					//intellipush_tools_woocommerceEvents_event_addToContactlist($order_id, $value);
					break;
				case 'send-message':
					$messageTemplates = get_field('intellipush_messages_templates', 'option');
					if ($messageTemplates) {
						foreach ($messageTemplates as $key => $template) {
							if($event['target'] === $template['id']) {
								IntellipushHelper_deleteScheduledMessage($template['message'], $billing['country'], $billing['phone'], 50);
							}
						}
					}
					break;
			}
		}
	}

	function intellipush_tools_woocommerceCartAbandonment_register_fields()  {
			// Get message templates
		$messageTemplates = get_field('intellipush_messages_templates', 'option');
		if ($messageTemplates) {
			foreach ($messageTemplates as $key => $value) {
				$messageTemplates[$value['id']] = $messageTemplates[$key]['name'];
				unset($messageTemplates[$key]);
			}
		}


		// WooCommerce events
		if (IntellipushHelper_isWooCommerceActivated()) {
			$intellipush_tools_woocommerceCartAbandonment_field = array(
				'parent' => 'group_intellipush_tools_woocommerceCartAbandonment',
				'key' => 'field_intellipush_tools_woocommerceCartAbandonment',
				'name' => 'intellipush_tools_woocommerceCartAbandonment',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'layout' => 'block',
				'button_label' => 'Add event',
				'sub_fields' => array(
					array(
						'key' => 'field_intellipush_tools_woocommerceCartAbandonment_name',
						'label' => 'Name',
						'name' => 'name',
						'type' => 'text',
						'required' => 1,
						'wrapper' => array(
							'width' => '33'
						)
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceCartAbandonment_delay',
						'label' => __('Delay', 'intellipush'),
						'name' => 'delay',
						'type' => 'number',
						'required' => 1,
						'append' => __('minutes', 'intellipush'),
						'min' => '5',
						'default_value' => 5,
						'wrapper' => array(
							'width' => '33'
						)
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceCartAbandonment_action',
						'label' => __('Action', 'intellipush'),
						'name' => 'action',
						'type' => 'select',
						'required' => 1,
						'choices' => array(
							//'add-contact' 				=> __('Add contact', 'intellipush'),
							//'add-to-contactlist' 		=> __('Add to contactlist', 'intellipush'),
							'send-message' 				=> __('Send message', 'intellipush')
						),
						'ui' => 1,
						'return_format' => 'value',
						'wrapper' => array(
							'width' => '33'
						)
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceCartAbandonment_target',
						'label' => __('Target', 'intellipush') . ' <small class="acf-accordion-view ip--font-weight-normal ip--color-link">(' . __('view', 'intellipush') . ')</small>',
						'name' => '',
						'type' => 'accordion',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceCartAbandonment_action',
									'operator' => '==',
									'value' => 'add-to-contactlist'
								)
							),
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceCartAbandonment_action',
									'operator' => '==',
									'value' => 'delete-from-contactlist'
								)
							),
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceCartAbandonment_action',
									'operator' => '==',
									'value' => 'send-message'
								)
							)
						),
						'open' => 0,
						'multi_expand' => 0,
						'endpoint' => 0,
						'required' => 1
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceCartAbandonment_target_contactlist',
						'label' => __('Contactlist', 'intellipush'),
						'name' => 'target',
						'type' => 'select',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceCartAbandonment_action',
									'operator' => '==',
									'value' => 'add-to-contactlist'
								)
							)
						),
						'choices' => $contactlist,
						'multiple' => 0,
						'allow_null' => 1,
						'ui' => 1,
						'return_format' => 'value'
					),
					array(
						'key' => 'field_intellipush_tools_woocommerceCartAbandonment_target_messageTemplates',
						'label' => __('Message template', 'intellipush'),
						'name' => 'target',
						'type' => 'select',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_intellipush_tools_woocommerceCartAbandonment_action',
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
					)
				),
			);
		} else {
			$intellipush_tools_woocommerceCartAbandonment_field = array(
				'parent' => 'group_intellipush_tools_woocommerceCartAbandonment',
				'key' => 'field_intellipush_tools_woocommerceCartAbandonment',
				'type' => 'message',
				'required' => 0,
				'conditional_logic' => 0,
				'message' => 'Please install <strong>WooCommerce</strong> plugin before using this tool',
				'esc_html' => 0,
			);
		}
		acf_add_local_field_group(array(
			'key' => 'group_intellipush_tools_woocommerceCartAbandonment',
			'title' => 'WooCommerce cart abandonment',
			'fields' => array ($intellipush_tools_woocommerceCartAbandonment_field),
			'location' => array (
				array (
					array (
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'intellipush-tools',
					),
				)
			),
			'menu_order' => 20,
		));
	}

	add_action('acf/init', 'intellipush_tools_woocommerceCartAbandonment_register_fields', 100);
	if (IntellipushHelper_isWooCommerceActivated() && IntellipushHelper_isAuth()) {
		add_action('wp_ajax_intellipush_cartAbandonment', 			'intellipush_tools_woocommerceCartAbandonment_ajax', 100 );
		add_action('wp_ajax_nopriv_intellipush_cartAbandonment', 	'intellipush_tools_woocommerceCartAbandonment_ajax', 100 );
		add_action('woocommerce_order_status_completed',    		'intellipush_tools_woocommerceCartAbandonment_cancel', 100);
		add_action('woocommerce_order_status_processing',   		'intellipush_tools_woocommerceCartAbandonment_cancel', 100);
	}
?>
