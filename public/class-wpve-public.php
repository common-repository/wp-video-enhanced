<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link          https://plugins360.com
 * @since         1.0.0
 *
 * @package       WP_Video_Enhanced
 * @subpackage    WP_Video_Enhanced/public
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * WPVE_Public class.
 *
 * @since    1.0.0
 */
class WPVE_Public {

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_styles() {
		wp_register_style( WPVE_PLUGIN_SLUG, WPVE_PLUGIN_URL . 'public/assets/css/wpve-public.css', array(), WPVE_PLUGIN_VERSION, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function register_scripts() {
		
		wp_register_script( WPVE_PLUGIN_SLUG, WPVE_PLUGIN_URL . 'public/assets/js/wpve-public.js', array( 'mediaelement' ), WPVE_PLUGIN_VERSION );
		wp_localize_script( WPVE_PLUGIN_SLUG, 'wpve', array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			)
		);
		
	}
	
	/**
	 * Filters the MediaElement configuration settings.
	 *
	 * @since     1.0.0
	 * @param     array    $mejs_settings    MediaElement settings array.
	 * @return    array    $mejs_settings    Modified settings array.
	 */
	public function mejs_settings( $mejs_settings ) {
	
		$general_settings = get_option( 'wpve_general_settings' );
		$brand_settings   = get_option( 'wpve_brand_settings' );
		$privacy_settings = get_option( 'wpve_privacy_settings' );
		
		if ( ! array_key_exists( 'features', $mejs_settings ) ) {
			$mejs_settings['useDefaultControls'] = true;
			$mejs_settings['features'] = array( 'wpve' );
		} else {	
			array_push( $mejs_settings['features'], 'wpve' );
		}

		$mejs_settings['startVolume'] = (int) $general_settings['start_volume'] / 100;

		$mejs_settings['siteUrl'] = esc_url_raw( $brand_settings['logo_link'] );
			
		if ( ! empty( $brand_settings['show_logo'] ) && ! empty( $brand_settings['logo_image'] ) ) {
			$mejs_settings['showLogo'] = (int) $brand_settings['show_logo'];
			$mejs_settings['logoImage'] = esc_url_raw( $brand_settings['logo_image'] );		
			$mejs_settings['logoPosition'] = sanitize_text_field( $brand_settings['logo_position'] );
			$mejs_settings['logoMargin'] = (int) $brand_settings['logo_margin'];
		}
		
		$mejs_settings['privacyConsentMessage'] = wp_kses_post( $privacy_settings['consent_message'] );
		$mejs_settings['privacyConsentButtonLabel'] = sanitize_text_field( $privacy_settings['consent_button_label'] );
		
		$mejs_settings['copyrightText'] = sanitize_text_field( $brand_settings['copyright_text'] );
		
		return $mejs_settings;
		
	}
	
	/**
	 * Filters the list of supported video formats.
	 *
	 * @since     1.0.0
	 * @param     array    $extensions    An array of support video formats.
	 * @return    array    $extensions    Modified video formats array.
	 */
	public function video_extensions( $extensions ) {
	
		$extensions[] = 'm3u8';
		$extensions[] = 'mpd';
	
		return $extensions;
		
	}
	
	/**
	 * Filters the list of mime types and file extensions.
	 *
	 * @since     1.0.0
	 * @param     array    $wp_get_mime_types    Mime types keyed by the file extension 
	 *									         regex corresponding to those types.
	 * @return    array    $wp_get_mime_types    Modified mime types array.
	 */
	public function mime_types( $wp_get_mime_types ) {
	
		$wp_get_mime_types['m3u8'] = 'application/x-mpegURL';
		$wp_get_mime_types['mpd'] = 'application/dash+xml';
	
		return $wp_get_mime_types;
	
	}
	
	/**
	 * Filters the default attributes of video shortcode.
	 *
	 * @since     1.0.0
	 * @param     array    $out      The output array of shortcode attributes.
	 * @param     array    $pairs    The supported attributes and their defaults.
	 * @param     array    $atts     The user defined shortcode attributes.
	 * @return    array    $out      Modified shortcode attributes.
	 */
	public function shortcode_atts( $out, $pairs, $atts ) {
		
		// Add support for custom query parameters in HLS, M(PEG)-DASH file formats
		$types = array( 'src', 'm3u8', 'mpd' );
		
		foreach ( $types as $type ) {
			
			if ( array_key_exists( $type, $out ) ) {
		
				$parsed_url = parse_url( $out[ $type ] );
			
				if ( array_key_exists( 'query', $parsed_url ) ) {
							
					$extension = $type;
					
					if ( 'src' == $type ) {
						$extension = pathinfo( $parsed_url['path'], PATHINFO_EXTENSION );
					}
					
					if ( 'm3u8' == $extension || 'mpd' == $extension ) {
						$out[ $type ] = str_replace( array( $parsed_url['query'], '?' ), '', $out[ $type ] );
						$out[ $extension . '_query' ] = $parsed_url['query'] ;
					}
					
				}
			
			}
		
		}
		
		// Livestream
		$out['live'] = '';
		if ( array_key_exists( 'live', $atts ) ) {
			$out['live'] = wp_validate_boolean( $atts['live'] );
		}
		
		// Privacy consent
		$out['privacy'] = '';
		if ( array_key_exists( 'privacy', $atts ) ) {
			$out['privacy'] = wp_validate_boolean( $atts['privacy'] );
		}
		
		return $out;
		
	}
	
	/**
	 * Filters the output of the video shortcode.
	 *
	 * @since     1.0.0
	 * @param     string    $output     Video shortcode HTML output.
	 * @param     array     $atts       Array of video shortcode attributes.
	 * @param     string    $video      Video file.
	 * @param     int       $post_id    Post ID.
	 * @param     string    $library    Media library used for the video shortcode.
	 * @return    string    $output     Modified HTML output.
	 */
	public function video_shortcode( $output, $atts, $video, $post_id, $library ) {

		$brand_settings   = get_option( 'wpve_brand_settings' );
		$privacy_settings = get_option( 'wpve_privacy_settings' );

		// Add support for custom query parameters in HLS, M(PEG)-DASH file formats
		$formats = array( 'm3u8', 'mpd' );
				
		foreach ( $formats as $format ) {
		
			if ( ! empty( $atts[ "{$format}_query" ] ) ) {
				$output = str_replace( ".{$format}?", ".{$format}&", $output );
				$output = str_replace( ".{$format}", ".{$format}?" . $atts[ "{$format}_query" ], $output );
			}
		
		}
		
		// Livestream
		if ( $atts['live'] ) {
			$output = str_replace( '<video', '<video live="true"', $output );
		}
		
		// Privacy consent
		$show_consent = $atts['privacy'];
				
		if ( isset( $_COOKIE['wpve_gdpr_consent'] ) || empty( $privacy_settings['show_consent'] ) || empty( $privacy_settings['consent_message'] ) || empty( $privacy_settings['consent_button_label'] ) ) {
			$show_consent = 0;
		} else {
			$is_youtube = preg_match( '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $atts['src'] );
			$is_vimeo = preg_match( '#^https?://(.+\.)?vimeo\.com/.*#', $atts['src'] );
			
			if ( $is_youtube || $is_vimeo ) {
				$show_consent = 1;
			}
		}
		
		if ( $show_consent ) {
			
			if ( empty( $atts['poster'] ) ) {
				if ( $is_youtube ) {
					$image = wpve_get_youtube_image( $atts['src'] );
				} elseif ( $is_vimeo ) {
					$image = wpve_get_vimeo_image( $atts['src'] );
				}
				
				$output = str_replace( '<video', sprintf( '<video poster="%s"', $image ), $output );
			}
			
			$output = str_replace( '<video', sprintf( '<video privacy="true" data-src="%s"', $atts['src'] ), $output );
			$output = str_replace( 'autoplay', 'data-autoplay', $output );
			$output = preg_replace( '/<source[^>]+\>/i', '', $output ); 

		}
		
		// Enqueue dependencies
		if ( ( ! empty( $brand_settings['show_logo'] ) && ! empty( $brand_settings['logo_image'] ) ) || ! empty( $brand_settings['copyright_text'] ) || $atts['live'] || $show_consent ) {
			wp_enqueue_style( WPVE_PLUGIN_SLUG );
			wp_enqueue_script( WPVE_PLUGIN_SLUG );
		}

		return $output;
	
	}
	
	/**
	 * Set cookie for accepting the privacy consent.
	 *
	 * @since    1.0.0
	 */
	public function set_cookie() {
	
		setcookie( 'wpve_gdpr_consent', 1, time() + ( 30 * 24 * 60 * 60 ), COOKIEPATH, COOKIE_DOMAIN );		
		echo 'success';
		
	}
	
	/**
	 * Filters the cached oEmbed HTML.
	 *
	 * @since     1.0.0
	 * @param     mixed     $cache      The cached HTML result, stored in post meta.
	 * @param     string    $url        The attempted embed URL.
	 * @param     array     $attr       An array of shortcode attributes.
	 * @param     int       $post_id    Post ID.
	 * @return    string    $output     Modified HTML output.
	 */
	public function embed_oembed_html( $cache, $url, $attr, $post_id ) {
	
		$privacy_settings = get_option( 'wpve_privacy_settings' );
		
		$output = $cache;
		$show_consent = 1;
		
		if ( isset( $_COOKIE['wpve_gdpr_consent'] ) || empty( $privacy_settings['show_consent'] ) || empty( $privacy_settings['consent_message'] ) || empty( $privacy_settings['consent_button_label'] ) ) {
			$show_consent = 0;
		}
		
		if ( $show_consent ) {
		
			$is_youtube = preg_match( '#^https?://(?:www\.)?(?:youtube\.com/watch|youtu\.be/)#', $url );
			$is_vimeo = preg_match( '#^https?://(.+\.)?vimeo\.com/.*#', $url );
					
			if ( $is_youtube || $is_vimeo ) {

				wp_enqueue_style( WPVE_PLUGIN_SLUG );
				wp_enqueue_script( WPVE_PLUGIN_SLUG );
				
				$html = $cache;
				
				$doc = new DOMDocument();
    			$doc->loadHTML( $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
    			$tags = $doc->getElementsByTagName( 'iframe' );
    			foreach ( $tags as $tag ) {
        			$iframe_src = $tag->attributes->getNamedItem( 'src' )->value;
					$src = add_query_arg( 'autoplay', 1, $iframe_src );
        			$tag->setAttribute( 'src', $src );
        			$html = $doc->saveHTML();
					
					$html = str_replace( 'src', 'data-src', $html );
				}
				
				if ( $is_youtube ) {
					$image = wpve_get_youtube_image( $url );
				} else {
					$image = wpve_get_vimeo_image( $url );
				}
					
				$output  = '<div class="wpve-privacy-wrapper">'; 
				$output .= $html;
				$output .= sprintf( '<div class="wpve-privacy" style="background-image: url(%s);">', $image );
				$output .= '<div class="wpve-privacy-consent-block">';
				$output .= sprintf( '<div class="wpve-privacy-consent-message">%s</div>', wp_kses_post( $privacy_settings['consent_message'] ) );
				$output .= sprintf( '<div class="wpve-privacy-consent-button">%s</div>', sanitize_text_field( $privacy_settings['consent_button_label'] ) );
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				
			}
			
		}
		
		return $output;
		
	}
	
}