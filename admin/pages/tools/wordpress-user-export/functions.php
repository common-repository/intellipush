<?php
	/**
	 * Tools: Wordpress user export
	 * @since      1.0.0
	 */

	function intellipush_tools_wordpressUserExport_register_fields()  {
		global $wp_roles;
		// Get contactlist
		$contactlist = IntellipushHelper_getContactlist();
		if ($contactlist) {
			foreach ($contactlist as $key => $value) {
				$contactlist[$value->id] = $contactlist[$key]->list_name;
				unset($contactlist[$key]);
			}
		}
		array_unshift( $contactlist , '--- '. __('Create a new contactlist', 'intellipush') );
		// Wordpress events
		$intellipush_tools_wordpressUserExport_field = array(
			array(
				'key' => 'field_intellipush_tools_wordpressUserExport_roles',
				'label' => __('Role', 'intellipush'),
				'name' => 'intellipush_tools_wordpressUserExport_roles',
				'type' => 'select',
				'wrapper' => array(
					'width' => '100'
				),
				'choices' => $wp_roles->get_names(),
				'allow_null' => 0,
				'multiple' => 1,
				'ui' => 1,
				'ajax' => 0,
				'return_format' => 'value',
				'placeholder' => '',
			),
			array(
				'key' => 'field_intellipush_tools_wordpressUserExport_contactlist',
				'label' => __('Export to contactlist', 'intellipush'),
				'name' => 'intellipush_tools_wordpressUserExport_contactlist',
				'type' => 'select',
				'wrapper' => array(
					'width' => '50'
				),
				'choices' => $contactlist,
				'multiple' => 0,
				'ui' => 1,
			),
			array(
				'key' => 'field_intellipush_tools_wordpressUserExport_action',
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
		acf_add_local_field_group(array(
			'key' => 'group_intellipush_tools_wordpressUserExport',
			'title' => 'Wordpress user export',
			'fields' => $intellipush_tools_wordpressUserExport_field,
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

	function intellipush_tools_wordpressUserExport_addToContactlist() {
		$args = array(
			'role' => $_POST['roles'] ? $_POST['roles'] : '',
			'number' => $_POST['number'],
			'paged' => $_POST['paged']
		);
		
		$results = get_users( $args );

		$contactlist = $_POST['contactlist'];
		if ($contactlist === '0') {
			$create_contactlist = IntellipushHelper_createContactlist('Wordpress user export ' . date('d/m/Y H:i:s'));
			if($create_contactlist->success) {
				$contactlist = $create_contactlist->id;
			} else {
				echo json_encode(array('error' => __('Can not create a new contactlist', 'intellipush')));
				wp_die();
			}
		}

		$contacts = array();
		foreach ($results as $user) {
			$user_meta = get_user_meta($user->ID);
			$phone = array_filter($user_meta, function($v, $k){
				return strpos(strtolower($k), 'phone') !== false && preg_match('/^[0-9 +-]/', $v[0]);
			}, ARRAY_FILTER_USE_BOTH);
			$phone = $phone ? reset($phone)[0] : null;

			$contacts[] = array(
				'name' => $user->first_name . ' ' . $user->last_name,
				'country' => 'NO',
				'phoneNumber' => $phone,
				'email' => 'john@netron.no',
				'company' => 'Netron',
				'zipCode' => '1010'
			);
		}
		IntellipushHelper_addToContactlistBatch($contacts, $contactlist);

	
		$return = array(
			'contactlist' => $contactlist,
			'users' => $results ? $results : null,
			'paged' => (int)$_POST['paged'],
			'total' => (int)count($results),
			'max_num_pages' => (int)$results->max_num_pages
		);
		echo json_encode($return);
		wp_die();
	}

	/*
	add_action('acf/init', 'intellipush_tools_wordpressUserExport_register_fields', 100);
	if (IntellipushHelper_isAuth() && is_admin()) {
		wp_enqueue_script( 'intellipush_tools_WordpressUserExportScript', plugin_dir_url( __FILE__ ) . '/script.js', array ( 'jquery' ), '1.0.0', true);
		add_action( 'wp_ajax_intellipush_tools_wordpressUserExport_addToContactlist', 'intellipush_tools_wordpressUserExport_addToContactlist', 100 );
	}
	*/
?>
