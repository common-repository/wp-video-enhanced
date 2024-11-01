<?php

/**
 * Helper Functions.
 *
 * @link          https://plugins360.com
 * @since         1.0.0
 *
 * @package       WP_Video_Enhanced
 * @subpackage    WP_Video_Enhanced/includes
 */

// Exit if accessed directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * Get default plugin settings.
 *
 * @since     1.1.0
 * @return    array    $defaults    Array of plugin settings.
 */
function wpve_get_default_settings() {

	$defaults = array(
		'general' => array(
			'start_volume' => 80
		),
		'brand'   => array(
			'show_logo'      => 1,
			'logo_image'     => '',
			'logo_link'      => home_url(),
			'logo_position'  => 'bottomleft',
			'logo_margin'    => 8,
			'copyright_text' => sprintf( __( 'Copyright %d %s', 'wp-video-enhanced' ), date( 'Y' ), get_option( 'blogname' ) )
		),
		'privacy' => array(
			'show_consent'         => 1,
			'consent_message'      => __( '<strong>Please accept cookies to play this video</strong>. By accepting you will be accessing content from a service provided by an external third party.', 'wp-video-enhanced' ),
			'consent_button_label' => __( 'Accept', 'wp-video-enhanced' )
		)
	);
	
	return $defaults;

}

/*
 * Get YouTube ID from URL.
 *
 * @since     1.0.0
 * @param     string    $url    YouTube page URL.
 * @return    string    $id     YouTube video ID.
 */
function wpve_get_youtube_id( $url ) {
	
	$id  = '';
    $url = parse_url( $url );
		
    if ( 0 === strcasecmp( $url['host'], 'youtu.be' ) ) {
       	$id = substr( $url['path'], 1 );
    } elseif ( 0 === strcasecmp( $url['host'], 'www.youtube.com' ) ) {
       	if ( isset( $url['query'] ) ) {
       		parse_str( $url['query'], $url['query'] );
           	if ( isset( $url['query']['v'] ) ) {
           		$id = $url['query']['v'];
           	}
       	}
			
       	if ( empty( $id ) ) {
           	$url['path'] = explode( '/', substr( $url['path'], 1 ) );
           	if ( in_array( $url['path'][0], array( 'e', 'embed', 'v' ) ) ) {
               	$id = $url['path'][1];
           	}
       	}
    }
    	
	return $id;
	
}

/*
 * Get YouTube image from URL.
 *
 * @since     1.0.0
 * @param     string    $url    YouTube page URL.
 * @return    string    $url    YouTube image URL.
 */
function wpve_get_youtube_image( $url ) {
	
	$id  = wpve_get_youtube_id( $url );
	$url = '';

	if ( ! empty( $id ) ) {
		$url = "https://img.youtube.com/vi/$id/0.jpg"; 
	}
	   	
	return $url;
	
}

/*
 * Get Vimeo ID from URL.
 *
 * @since     1.0.0
 * @param     string    $url    Vimeo page URL.
 * @return    string    $id     Vimeo video ID.
 */
function wpve_get_vimeo_id( $url ) {
	
	$url = explode( '?', $url );
	$id = preg_replace( '/[^\/]+[^0-9]|(\/)/', '', rtrim( $url[0], '/' ) );
	
	return $id;
	
}

/*
 * Get Vimeo image from URL.
 *
 * @since     1.0.0
 * @param     string    $url    Vimeo page URL.
 * @return    string    $url    Vimeo image URL.
 */
function wpve_get_vimeo_image( $url ) {
	
	$id  = wpve_get_vimeo_id( $url );		
	$url = '';
	
	if ( ! empty( $id ) ) {
		$vimeo = unserialize( file_get_contents( "https://vimeo.com/api/v2/video/$id.php" ) );
		$url = $vimeo[0]['thumbnail_large'];
	}
    	
	return $url;
	
}
