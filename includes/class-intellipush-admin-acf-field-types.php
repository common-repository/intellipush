<?php
	class Intellipush_acf_field_unique_id extends acf_field {
		function initialize() {

			$this->name     = 'unique_id';
			$this->label    = 'Unique ID';
			$this->category = 'basic';
			$this->defaults = array(
				'hidden' => 0
			);

			add_filter('acf/prepare_field', array($this, 'prepare_field'));
		}
		function render_field( $field ) {
			printf( '<input type="text" name="%s" value="%s" readonly>',
				esc_attr( $field['name'] ),
				esc_attr( $field['value'] )
			);
		}
		function render_field_settings( $field ) {
			// Hidden
			acf_render_field_setting( $field, array(
				'label'			=> __('Hidden','acf'),
				'name'			=> 'hidden',
				'type'			=> 'true_false',
				'ui'			=> 1
			));
		}
		function prepare_field ( $field ) {
			if ($field['type'] === 'unique_id' && $field['hidden']) {
				$field['wrapper']['class'] = 'ip--display-none';
			}
			return $field;
		}
		function update_value( $value, $post_id, $field ) {
			return !empty($value) ? $value : uniqid();
		}
	}
