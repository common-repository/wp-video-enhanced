<?php

/**
 * Settings Form.
 *
 * @link          https://plugins360.com
 * @since         1.0.0
 *
 * @package       WP_Video_Enhanced
 * @subpackage    WP_Video_Enhanced/admin/templates
 */
?>

<div id="wpve-settings" class="wrap wpve-settings">	
    <h1><?php _e( 'WP Video Enhanced', 'wp-video-enhanced' ); ?></h1>
    <p><?php _e( 'Extending WordPress Video Shortcode with New Features.', 'wp-video-enhanced' ); ?></p>
    
	<h2 class="nav-tab-wrapper">
		<?php
        $settings_url = admin_url( 'admin.php?page=wp-video-enhanced' );
        
        foreach ( $this->tabs as $tab => $title ) {
            $class = ( $tab == $active_tab ) ? 'nav-tab nav-tab-active' : 'nav-tab';
            printf( '<a href="%s" class="%s">%s</a>', esc_url( add_query_arg( 'tab', $tab, $settings_url ) ), $class, $title );
        }
        ?>
    </h2>
    
	<?php settings_errors(); ?>
    
    <?php if ( 'support' == $active_tab ) : ?>
    
    	<div class="about-wrap">
            <p class="about-description"><?php _e( 'Need Help?', 'wp-video-enhanced' ); ?></p>
        
            <div class="changelog">    
                <div class="two-col">
                    <div class="col">
                        <h3><?php _e( 'Phenomenal Support', 'wp-video-enhanced' ); ?></h3>
                        
                        <p>
                            <?php printf( __( 'We do our best to provide the best support we can. If you encounter a problem or have a question, simply submit your question using our <a href="%s" target="_blank">support form</a>.', 'wp-video-enhanced' ), 'https://wordpress.org/support/plugin/wp-video-enhanced' ); ?>
                        </p>
                    </div>
                    
                    <div class="col">
                        <h3><?php _e( 'Need Even Faster Support?', 'wp-video-enhanced' ); ?></h3>
                        
                        <p>
                            <?php printf( __( 'Our <a href="%s" target="_blank">Priority Support</a> system is there for customers that need faster and/or more in-depth assistance.', 'all-in-one-video-gallery' ), 'https://plugins360.com/support/' ); ?>
                        </p>
                    </div>                
                </div>
            </div>
            
           	<div style="padding: 15px; background: #0073aa; border-radius: 3px; color: #fff;">
           		<p class="about-description" style="margin: 0;">
            		<span class="dashicons dashicons-thumbs-up" style="font-size: 30px; margin-right: 10px;"></span>
					<?php _e( 'WP Video Monetize ( PRO Add-On )', 'wp-video-enhanced' ); ?>
            	</p>
            	<p>
					<?php _e( 'Show ads on the WordPress\' Native Video Player from DoubleClick for Publishers (DFP), the Google AdSense network, or any VAST-compliant ad server.', 'wp-video-enhanced' ); ?>
                	<a href="https://plugins360.com/wp-video-monetize/" target="_blank" style="color: #ffff00;">Read More</a>
           		</p>
           	</div> 
    	</div>

    <?php else : ?>
    
        <form method="post" action="options.php"> 
            <?php
            settings_fields( "wpve_{$active_tab}_settings" );
            do_settings_sections( "wpve_{$active_tab}_settings" );
            
            submit_button();
            ?>
        </form>
        
    <?php endif; ?>
</div>