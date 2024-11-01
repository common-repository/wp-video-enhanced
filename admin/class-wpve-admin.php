<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link          https://plugins360.com
 * @since         1.0.0
 *
 * @package       WP_Video_Enhanced
 * @subpackage    WP_Video_Enhanced/admin
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * WPVE_Admin class.
 *
 * @since    1.0.0
 */
class WPVE_Admin {

	/**
     * Settings tabs array.
     *
	 * @since     1.0.0
	 * @access    protected
     * @var       array
     */
    protected $tabs = array();
	
	/**
     * Settings sections array.
     *
	 * @since     1.0.0
	 * @access    protected
     * @var       array
     */
    protected $sections = array();
	
	/**
     * Settings fields array
     *
	 * @since     1.0.0
	 * @access    protected
     * @var       array
     */
    protected $fields = array();
	
	/**
	 * Check and update plugin options to the latest version.
	 *
	 * @since    1.1.0
	 */
	public function manage_upgrades() {

		if ( WPVE_PLUGIN_VERSION !== get_option( 'wpve_version' ) ) {
		
			$defaults = wpve_get_default_settings();
			
			// Insert the plugin brand settings
			if ( false == get_option( 'wpve_brand_settings' ) ) {
				add_option( 'wpve_brand_settings', $defaults['brand'] );
			}
			
			// Update the plugin version
			update_option( 'wpve_version', WPVE_PLUGIN_VERSION );
		
		}

	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_media();
        wp_enqueue_script( 'wp-color-picker' );		
		wp_enqueue_script( WPVE_PLUGIN_SLUG, WPVE_PLUGIN_URL . 'admin/assets/js/wpve-admin.js', array( 'jquery' ), WPVE_PLUGIN_VERSION, false );

	}
	
	/**
	 * Add a settings menu for the plugin.
	 *
	 * @since    1.0.0
	 */
	public function add_settings_menu() {
	
		add_menu_page(
			__( 'WP Video Enhanced', 'wp-video-enhanced' ),
			__( 'Video Enhanced', 'wp-video-enhanced' ),
			'manage_options',
			'wp-video-enhanced',
			array( $this, 'display_settings_form' ),
			'dashicons-format-video',
			10
		);
	
	}
	
	/**
	 * Add a settings link on the plugin listing page.
	 *
	 * @since     1.0.0
	 * @param     array     $links    An array of plugin action links.
	 * @return    string    $links    Array of filtered plugin action links.
	 */
	public function plugin_action_links( $links ) {

		$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wp-video-enhanced' ), __( 'Settings', 'wp-video-enhanced' ) );
        array_unshift( $links, $settings_link );
		
    	return $links;

	}
	
	/**
	 * Display settings form.
	 *
	 * @since    1.0.0
	 */
	public function display_settings_form() {
	
		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->tabs ) ? sanitize_text_field( $_GET['tab'] ) : 'general';
		require_once WPVE_PLUGIN_DIR . 'admin/templates/settings.php';
		
	}
	
	/**
	 * Initiate settings.
	 *
	 * @since    1.0.0
	 */
	public function admin_init() { 
	
		$this->tabs     = $this->get_tabs();
        $this->sections = $this->get_sections();
        $this->fields   = $this->get_fields();
		
        // Initialize settings
        $this->initialize_settings();
		
	}
	
	/**
     * Get settings tabs.
     *
	 * @since     1.0.0
     * @return    array    $tabs    Setting tabs array.
     */
    public function get_tabs() {
	
		$tabs = array(
			'general' => __( 'General', 'wp-video-enhanced' ),
			'support' => __( 'Support', 'wp-video-enhanced' )
		);
		
		return apply_filters( 'wpve_settings_tabs', $tabs );
	
	}
	
	/**
     * Get settings sections.
     *
	 * @since     1.0.0
     * @return    array    $sections    Setting sections array.
     */
    public function get_sections() {	
		
		$sections = array(
			array(
                'id'    => 'wpve_general_settings',
                'title' => __( 'General Settings', 'wp-video-enhanced' ),
				'tab'   => 'general'
            ),
			array(
                'id'    => 'wpve_brand_settings',
                'title' => __( 'Logo & Branding', 'wp-video-enhanced' ),
				'tab'   => 'general'
            ),
			array(
                'id'          => 'wpve_privacy_settings',
                'title'       => __( 'Privacy Settings', 'wp-video-enhanced' ),
				'description' => __( 'These options will help with privacy restrictions such as GDPR and the EU Cookie Law.', 'wp-video-enhanced' ),
				'tab'         => 'general'
            )
        );
		
		return apply_filters( 'wpve_settings_sections', $sections );
		
	}
	
	/**
     * Get settings fields.
     *
	 * @since     1.0.0
     * @return    array    $fields    Setting fields array.
     */
    public function get_fields() {
	
		$fields = array(
			'wpve_general_settings' => array(
				array(
                    'name'              => 'start_volume',
                    'label'             => __( 'Start Volume', 'wp-video-enhanced' ),
                    'description'       => __( '[0 - 100]. Initial volume when the player starts (overrided by user cookie).', 'wp-video-enhanced' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'intval'
               	)
			),
			'wpve_brand_settings' => array(
				array(
                    'name'              => 'show_logo',
                    'label'             => __( 'Show Logo', 'wp-video-enhanced' ),
                    'description'       => __( 'Check this option to show the watermark on the video.', 'wp-video-enhanced' ),
                    'type'              => 'checkbox',
                    'sanitize_callback' => 'intval'
               	),
				array(
                    'name'              => 'logo_image',
                    'label'             => __( 'Logo Image', 'wp-video-enhanced' ),
                    'description'       => __( 'Upload the image file of your logo. We recommend using the transparent PNG format with width below 100 pixels. If you do not enter any image, no logo will displayed.', 'wp-video-enhanced' ),
                    'type'              => 'file',
                    'sanitize_callback' => 'esc_url_raw'
               	),
				array(
                    'name'              => 'logo_link',
                    'label'             => __( 'Logo Link', 'wp-video-enhanced' ),
                    'description'       => __( 'The URL to visit when the watermark image is clicked. Clicking a logo will have no affect unless this is configured.', 'wp-video-enhanced' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'esc_url_raw'
               	),
				array(
                    'name'              => 'logo_position',
                    'label'             => __( 'Logo Position', 'wp-video-enhanced' ),
                    'description'       => __( 'This sets the corner in which to display the watermark.', 'wp-video-enhanced' ),
                    'type'              => 'select',
					'options'           => array(
						'topleft'     => __( 'Top Left', 'wp-video-enhanced' ),
						'topright'    => __( 'Top Right', 'wp-video-enhanced' ),
						'bottomleft'  => __( 'Bottom Left', 'wp-video-enhanced' ),
						'bottomright' => __( 'Bottom Right', 'wp-video-enhanced' )
					),
                    'sanitize_callback' => 'sanitize_key'
               	),
				array(
                    'name'              => 'logo_margin',
                    'label'             => __( 'Logo Margin', 'wp-video-enhanced' ),
                    'description'       => __( 'The distance, in pixels, of the logo from the edges of the display.', 'wp-video-enhanced' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'intval'
               	),
				array(
                    'name'              => 'copyright_text',
                    'label'             => __( 'Copyright Text', 'wp-video-enhanced' ),
                    'description'       => __( 'Text that is shown when a user right-clicks the player with the mouse.', 'wp-video-enhanced' ),
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
               	)
			),
			'wpve_privacy_settings' => array(
				array(
                    'name'              => 'show_consent',
                    'label'             => __( 'GDPR - Show Consent', 'wp-video-enhanced' ),
                    'description'       => __( 'Ask for consent before loading YouTube/Vimeo content.', 'wp-video-enhanced' ),
                    'type'              => 'checkbox',
                    'sanitize_callback' => 'intval'
               	),
				array(
                    'name'              => 'consent_message',
                    'label'             => __( 'GDPR - Consent Message', 'wp-video-enhanced' ),
                    'description'       => '',
                    'type'              => 'wysiwyg',
                    'sanitize_callback' => 'wp_kses_post'
               	),
				array(
                    'name'              => 'consent_button_label',
                    'label'             => __( 'GDPR - Consent Button Label', 'wp-video-enhanced' ),
                    'description'       => '',
                    'type'              => 'text',
                    'sanitize_callback' => 'sanitize_text_field'
               	)
			)
		);
		
		return apply_filters( 'wpve_settings_fields', $fields );
		
	}
	
	/**
     * Initialize and registers the settings sections and fields to WordPress.
     *
     * @since    1.0.0
     */
    public function initialize_settings() {
	
        // Register settings sections & fields
        foreach ( $this->sections as $section ) {
		
			$page_hook = "wpve_{$section['tab']}_settings";
			
			// Sections
            if ( false == get_option( $section['id'] ) ) {
                add_option( $section['id'] );
            }
			
            if ( isset( $section['description'] ) && ! empty( $section['description'] ) ) {
                $section['description'] = sprintf( '<div class="inside">%s</div>', $section['description'] );
                $callback = create_function( '', 'echo "' . str_replace( '"', '\"', $section['description'] ) . '";' );
            } elseif ( isset( $section['callback'] ) ) {
                $callback = $section['callback'];
            } else {
                $callback = null;
            }
			
            add_settings_section( $section['id'], $section['title'], $callback, $page_hook );
			
			// Fields			
			$fields = $this->fields[ $section['id'] ];
			
			foreach ( $fields as $option ) {
			
                $name     = $option['name'];
                $type     = isset( $option['type'] ) ? $option['type'] : 'text';
                $label    = isset( $option['label'] ) ? $option['label'] : '';
                $callback = isset( $option['callback'] ) ? $option['callback'] : array( $this, 'callback_' . $type );				
                $args     = array(
                    'id'                => $name,
                    'class'             => isset( $option['class'] ) ? $option['class'] : $name,
                    'label_for'         => "{$section['id']}[{$name}]",
                    'description'       => isset( $option['description'] ) ? $option['description'] : '',
                    'name'              => $label,
                    'section'           => $section['id'],
                    'size'              => isset( $option['size'] ) ? $option['size'] : null,
                    'options'           => isset( $option['options'] ) ? $option['options'] : '',
                    'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
                    'type'              => $type,
                    'placeholder'       => isset( $option['placeholder'] ) ? $option['placeholder'] : '',
                    'min'               => isset( $option['min'] ) ? $option['min'] : '',
                    'max'               => isset( $option['max'] ) ? $option['max'] : '',
                    'step'              => isset( $option['step'] ) ? $option['step'] : ''					
                );
				
                add_settings_field( "{$section['id']}[{$name}]", $label, $callback, $page_hook, $section['id'], $args );

            }
			
			// Creates our settings in the options table
        	register_setting( $page_hook, $section['id'], array( $this, 'sanitize_options' ) );
			
        }
		
    }

	/**
     * Displays a text field for a settings field.
     *
	 * @since    1.0.0
     * @param    array     $args    Settings field args.
     */
    public function callback_text( $args ) {
	
        $value       = esc_attr( $this->get_option( $args['id'], $args['section'], '' ) );
        $size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'text';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
		
        $html        = sprintf( '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder );
        $html       .= $this->get_field_description( $args );
		
        echo $html;
		
    }
	
	/**
     * Displays a url field for a settings field.
     *
	 * @since    1.0.0
     * @param    array     $args    Settings field args.
     */
    public function callback_url( $args ) {
        $this->callback_text( $args );
    }
	
	/**
     * Displays a number field for a settings field.
     *
	 * @since    1.0.0
     * @param    array    $args    Settings field args.
     */
    public function callback_number( $args ) {
	
        $value       = esc_attr( $this->get_option( $args['id'], $args['section'], 0 ) );
        $size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $type        = isset( $args['type'] ) ? $args['type'] : 'number';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
        $min         = empty( $args['min'] ) ? '' : ' min="' . $args['min'] . '"';
        $max         = empty( $args['max'] ) ? '' : ' max="' . $args['max'] . '"';
        $step        = empty( $args['max'] ) ? '' : ' step="' . $args['step'] . '"';
		
        $html        = sprintf( '<input type="%1$s" class="%2$s-number" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"%6$s%7$s%8$s%9$s/>', $type, $size, $args['section'], $args['id'], $value, $placeholder, $min, $max, $step );
        $html       .= $this->get_field_description( $args );
		
        echo $html;
		
    }
	
	/**
     * Displays a checkbox for a settings field.
     *
	 * @since    1.0.0
     * @param    array     $args    Settings field args.
     */
    public function callback_checkbox( $args ) {
	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], 0 ) );
		
        $html  = '<fieldset>';
        $html  .= sprintf( '<label for="%1$s[%2$s]">', $args['section'], $args['id'] );
        $html  .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="0" />', $args['section'], $args['id'] );
        $html  .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="1" %3$s />', $args['section'], $args['id'], checked( $value, 1, false ) );
        $html  .= sprintf( '%1$s</label>', $args['description'] );
        $html  .= '</fieldset>';
		
        echo $html;
		
    }
	
	/**
     * Displays a multicheckbox for a settings field.
     *
     * @since    1.0.0
     * @param    array     $args    Settings field args.
     */
    public function callback_multicheck( $args ) {
	
        $value = $this->get_option( $args['id'], $args['section'], array() );
		
        $html  = '<fieldset>';
        $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="" />', $args['section'], $args['id'] );
        foreach ( $args['options'] as $key => $label ) {
            $checked  = in_array( $key, $value ) ? 'checked="checked"' : '';
            $html    .= sprintf( '<label for="%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
            $html    .= sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, $checked );
            $html    .= sprintf( '%1$s</label><br>',  $label );
        }
        $html .= $this->get_field_description( $args );
        $html .= '</fieldset>';
		
        echo $html;
		
    }
	
	/**
     * Displays a radio button for a settings field.
     *
     * @since    1.0.0
     * @param    array     $args    Settings field args.
     */
    public function callback_radio( $args ) {
	
        $value = $this->get_option( $args['id'], $args['section'], '' );
		
        $html  = '<fieldset>';
        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<label for="%1$s[%2$s][%3$s]">',  $args['section'], $args['id'], $key );
            $html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
            $html .= sprintf( '%1$s</label><br>', $label );
        }
        $html .= $this->get_field_description( $args );
        $html .= '</fieldset>';
		
        echo $html;
		
    }
	
	/**
     * Displays a selectbox for a settings field.
     *
     * @since    1.0.0
     * @param    array     $args    Settings field args.
     */
    public function callback_select( $args ) {
	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], '' ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		
        $html  = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
        foreach ( $args['options'] as $key => $label ) {
            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
        }
        $html .= sprintf( '</select>' );
        $html .= $this->get_field_description( $args );
		
        echo $html;
		
    }
	
	/**
     * Displays a textarea for a settings field.
     *
     * @since    1.0.0
     * @param    array    $args    Settings field args.
     */
    public function callback_textarea( $args ) {
	
        $value       = esc_textarea( $this->get_option( $args['id'], $args['section'], '' ) );
        $size        = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="'.$args['placeholder'].'"';
		
        $html        = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]"%4$s>%5$s</textarea>', $size, $args['section'], $args['id'], $placeholder, $value );
        $html       .= $this->get_field_description( $args );
		
        echo $html;
		
    }
	
	/**
     * Displays the html for a settings field.
     *
     * @since    1.0.0
     * @param    array    $args    Settings field args.
     */
    public function callback_html( $args ) {
        echo $this->get_field_description( $args );
    }
	
	/**
     * Displays a rich text textarea for a settings field.
     *
     * @since    1.0.0
     * @param    array    $args    Settings field args.
     */
    public function callback_wysiwyg( $args ) {
	
        $value = $this->get_option( $args['id'], $args['section'], '' );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : '500px';
		
        echo '<div style="max-width: ' . $size . ';">';
        $editor_settings = array(
            'teeny'         => true,
            'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
            'textarea_rows' => 10
        );
        if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
            $editor_settings = array_merge( $editor_settings, $args['options'] );
        }
        wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );
        echo '</div>';
        echo $this->get_field_description( $args );
		
    }
	
	/**
     * Displays a file upload field for a settings field.
     *
     * @since    1.0.0
     * @param    array    $args    Settings field args.
     */
    public function callback_file( $args ) {
	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], '' ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
        $id    = $args['section'] . '[' . $args['id'] . ']';
        $label = isset( $args['options']['button_label'] ) ? $args['options']['button_label'] : __( 'Choose File', 'wp-video-enhanced' );
		
        $html  = sprintf( '<input type="text" class="%1$s-text wpve-url" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
        $html .= '<input type="button" class="button wpve-browse" value="' . $label . '" />';
        $html .= $this->get_field_description( $args );
		
        echo $html;
		
    }
	
	/**
     * Displays a password field for a settings field.
     *
     * @since    1.0.0
     * @param    array    $args    Settings field args.
     */
    public function callback_password( $args ) {
	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], '' ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		
        $html  = sprintf( '<input type="password" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
        $html .= $this->get_field_description( $args );
		
        echo $html;
		
    }
	
	/**
     * Displays a color picker field for a settings field.
     *
     * @since    1.0.0
     * @param    array    $args    Settings field args.
     */
    public function callback_color( $args ) {
	
        $value = esc_attr( $this->get_option( $args['id'], $args['section'], '#ffffff' ) );
        $size  = isset( $args['size'] ) && ! is_null( $args['size'] ) ? $args['size'] : 'regular';
		
        $html  = sprintf( '<input type="text" class="%1$s-text wpve-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, '#ffffff' );
        $html .= $this->get_field_description( $args );
		
        echo $html;
		
    }
	
	/**
     * Displays a select box for creating the pages select box.
     *
     * @since    1.0.0
     * @param    array    $args    Settings field args.
     */
    public function callback_pages( $args ) {
	
        $dropdown_args = array(
			'show_option_none'  => '-- ' . __( 'Select a page', 'wp-video-enhanced' ) . ' --',
			'option_none_value' => -1,
            'selected'          => esc_attr($this->get_option($args['id'], $args['section'], -1 ) ),
            'name'              => $args['section'] . '[' . $args['id'] . ']',
            'id'                => $args['section'] . '[' . $args['id'] . ']',
            'echo'              => 0			
        );
		
        $html  = wp_dropdown_pages( $dropdown_args );
		$html .= $this->get_field_description( $args );
		
        echo $html;
		
    }
	
	/**
     * Get field description for display.
     *
	 * @since    1.0.0
     * @param    array    $args    Settings field args.
     */
    public function get_field_description( $args ) {
	
        if ( ! empty( $args['description'] ) ) {
            $description = sprintf( '<p class="description">%s</p>', $args['description'] );
        } else {
            $description = '';
        }
		
        return $description;
		
    }
	
	/**
     * Sanitize callback for Settings API.
     *
	 * @since     1.0.0
     * @param     array    $options    The unsanitized collection of options.
     * @return                         The collection of sanitized values.
     */
    public function sanitize_options( $options ) {
	
        if ( ! $options ) {
            return $options;
        }
		
        foreach ( $options as $option_slug => $option_value ) {		
            $sanitize_callback = $this->get_sanitize_callback( $option_slug );
			
            // If callback is set, call it
            if ( $sanitize_callback ) {
                $options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
                continue;
            }			
        }
		
        return $options;
		
    }
	
	/**
     * Get sanitization callback for given option slug.
     *
	 * @since     1.0.0
     * @param     string    $slug    Option slug.
     * @return    mixed              String or bool false.
     */
    public function get_sanitize_callback( $slug = '' ) {
	
        if ( empty( $slug ) ) {
            return false;
        }
		
        // Iterate over registered fields and see if we can find proper callback
        foreach ( $this->fields as $section => $options ) {
            foreach ( $options as $option ) {
                if ( $option['name'] != $slug ) {
                    continue;
                }
				
                // Return the callback name
                return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
            }
        }
		
        return false;
		
    }
	
	/**
     * Get the value of a settings field
     *
	 * @since     1.0.0
     * @param     string    $option     Settings field name.
     * @param     string    $section    The section name this field belongs to
     * @param     string    $default    Default text if it's not found.
     * @return    string
     */
    public function get_option( $option, $section, $default = '' ) {
	
        $options = get_option( $section );
		
        if ( ! empty( $options[ $option ] ) ) {
            return $options[ $option ];
        }
		
        return $default;
		
    }
	
}
