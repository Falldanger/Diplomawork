<?php

if ( !class_exists( 'bulk_meta_box' ) ) {

	class bulk_meta_box {

		private $screens = array(
			'page',
		);
		private $fields	 = array();

		/**
		 * Class construct method. Adds actions to their respective WordPress hooks.
		 */
		public function __construct() {
			$this->fields	 = array(
				array(
					'id'	 => 'transparent-header',
					'label'	 => esc_html__( 'Transparent header', 'bulk' ),
					'type'	 => 'checkbox',
				),
				array(
					'id'	 => 'header-font-color',
					'label'	 => esc_html__( 'Transparent header font color', 'bulk' ),
					'type'	 => 'color',
				),
			);
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_post' ) );
		}

		/**
		 * Hooks into WordPress' add_meta_boxes function.
		 * Goes through screens (post types) and adds the meta box.
		 */
		public function add_meta_boxes() {
			global $post;

			if ( !empty( $post ) ) {
				$pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);
				if ( $pageTemplate == 'template-parts/template-homepage.php' ) {
					foreach ( $this->screens as $screen ) {
						add_meta_box(
						'header-options', esc_html__( 'Header Options', 'bulk' ), array( $this, 'add_meta_box_callback' ), $screen, 'normal', 'high'
						);
					}
				}
			}
		}

		/**
		 * Generates the HTML for the meta box
		 * 
		 * @param object $post WordPress post object
		 */
		public function add_meta_box_callback( $post ) {
			wp_nonce_field( 'header_options_data', 'header_options_nonce' );
			$this->generate_fields( $post );
		}

		/**
		 * Generates the field's HTML for the meta box.
		 */
		public function generate_fields( $post ) {
			$output = '';
			foreach ( $this->fields as $field ) {
				$label		 = '<label for="' . $field[ 'id' ] . '">' . $field[ 'label' ] . '</label>';
				$db_value	 = get_post_meta( $post->ID, 'header_options_' . $field[ 'id' ], true );
				switch ( $field[ 'type' ] ) {
					case 'checkbox':
						$input	 = sprintf(
						'<input %s id="%s" name="%s" type="checkbox" value="1">', $db_value === '1' ? 'checked' : '', $field[ 'id' ], $field[ 'id' ]
						);
						break;
					default:
						$input	 = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">', $field[ 'type' ] !== 'color' ? 'class="regular-text"' : '', $field[ 'id' ], $field[ 'id' ], $field[ 'type' ], $db_value
						);
				}
				$output .= $this->row_format( $label, $input );
			}
			echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
		}

		/**
		 * Generates the HTML for table rows.
		 */
		public function row_format( $label, $input ) {
			return sprintf(
			'<tr><th scope="row">%s</th><td>%s</td></tr>', $label, $input
			);
		}

		/**
		 * Hooks into WordPress' save_post function
		 */
		public function save_post( $post_id ) {
			if ( !isset( $_POST[ 'header_options_nonce' ] ) )
				return $post_id;

			$nonce = $_POST[ 'header_options_nonce' ];
			if ( !wp_verify_nonce( $nonce, 'header_options_data' ) )
				return $post_id;

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return $post_id;

			foreach ( $this->fields as $field ) {
				if ( isset( $_POST[ $field[ 'id' ] ] ) ) {
					switch ( $field[ 'type' ] ) {
						case 'email':
							$_POST[ $field[ 'id' ] ] = sanitize_email( $_POST[ $field[ 'id' ] ] );
							break;
						case 'text':
							$_POST[ $field[ 'id' ] ] = sanitize_text_field( $_POST[ $field[ 'id' ] ] );
							break;
					}
					update_post_meta( $post_id, 'header_options_' . $field[ 'id' ], $_POST[ $field[ 'id' ] ] );
				} else if ( $field[ 'type' ] === 'checkbox' ) {
					update_post_meta( $post_id, 'header_options_' . $field[ 'id' ], '0' );
				}
			}
		}

	}

	new bulk_meta_box;
}
