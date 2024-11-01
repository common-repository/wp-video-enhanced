(function( $ ) {
		  
	'use strict';
	
	/**
	 * A custom mediaelementjs plugin.
	 *
	 * @since    1.0.0
	 */
	Object.assign(mejs.MepDefaults, {
		siteUrl: '',
		showLogo: '',
		logoImage: '',
		logoPosition: '',
		logoMargin: '',
		privacyConsentMessage: '',
		privacyConsentButtonLabel: '',
		copyrightText: ''
	});
	 
	Object.assign(MediaElementPlayer.prototype, {
	
		buildwpve: function buildwpve( player, controls, layers, media ) {
			
			var t = this;
			
			if ( ! t.isVideo ) {
				return;
			}
			
			// Is Live?
			var isLive = t.node.attributes.live && 'false' !== t.node.attributes.live;
			if ( isLive ) {		
				t.options.forceLive = true;
			}
			
			// Logo / Watermark
			if ( '' != t.options.showLogo ) {
				t.logoLayer = document.createElement( 'div' );
				t.logoLayer.className = t.options.classPrefix + 'logo ' + t.options.classPrefix + 'logo-' + t.options.logoPosition;
				t.logoLayer.style.margin = t.options.logoMargin + 'px';
				t.logoLayer.innerHTML = '<img src="' + t.options.logoImage + '" />';
					
				t.layers[0].append( t.logoLayer );
				
				if ( '' != t.options.siteUrl ) {
					t.logoLayer.addEventListener( 'click', function() {
						window.location.href = t.options.siteUrl;
					});
				}
				
				t.container[0].addEventListener( 'controlsshown', function() {
					t.logoLayer.style.display = '';
				});
				
				t.container[0].addEventListener( 'controlshidden', function() {
					t.logoLayer.style.display = 'none';
				});
			}
			
			// Privacy Consent
			var showPrivacyConsent = t.node.attributes.privacy && 'false' !== t.node.attributes.privacy;		
			if ( showPrivacyConsent ) {			
				t.privacyLayer = document.createElement( 'div' );
				t.privacyLayer.className = t.options.classPrefix + 'overlay ' + t.options.classPrefix + 'layer ' + t.options.classPrefix + 'privacy';
				t.privacyLayer.innerHTML = ( '<div class="' + t.options.classPrefix + 'privacy-consent-block">' ) + ( '<div class="' + t.options.classPrefix + 'privacy-consent-message">' + t.options.privacyConsentMessage + '</div>' ) + ( '<div class="' + t.options.classPrefix + 'privacy-consent-button">' + t.options.privacyConsentButtonLabel + '</div>' ) + '</div>';
				
				t.layers[0].append( t.privacyLayer );
				
				t.privacyLayer.querySelector( '.' + t.options.classPrefix + 'privacy-consent-button' ).addEventListener( 'click',  t.wpveOnAgreeToPrivacy.bind( t ) );			
			}
			
			// Custom Context Menu
			if ( '' != t.options.copyrightText ) {
				t.contextMenu = document.querySelector( '.' + t.options.classPrefix + 'contextmenu' );
				
				if ( ! t.contextMenu ) {
					t.contextMenu = document.createElement( 'div' );
					t.contextMenu.className = t.options.classPrefix + 'contextmenu';
					t.contextMenu.innerHTML = '<div class="' + t.options.classPrefix + 'contextmenu-item">' + t.options.copyrightText + '</div>';
					t.contextMenu.style.display = 'none';
	
					document.body.appendChild( t.contextMenu );
					
					document.addEventListener( 'contextmenu', function() {
						t.contextMenu.style.display = 'none';								 
					});
					
					document.addEventListener( 'click', function() {
						t.contextMenu.style.display = 'none';								 
					});
				}
				
				t.container[0].addEventListener( 'contextmenu', function( e ) {
					if ( e.keyCode === 3 || e.which === 3 ) {
						e.preventDefault();
						e.stopPropagation();
						
						var width = t.contextMenu.offsetWidth,
							height = t.contextMenu.offsetHeight,
							x = e.pageX,
							y = e.pageY,
							doc = document.documentElement,
							scrollLeft = ( window.pageXOffset || doc.scrollLeft ) - ( doc.clientLeft || 0 ),
							scrollTop = ( window.pageYOffset || doc.scrollTop ) - ( doc.clientTop || 0 ),
							left = x + width > window.innerWidth + scrollLeft ? x - width : x,
							top = y + height > window.innerHeight + scrollTop ? y - height : y;
				
						t.contextMenu.style.display = '';
						t.contextMenu.style.left = left + 'px';
						t.contextMenu.style.top = top + 'px';				
					}
				});
				
				if ( '' != t.options.siteUrl ) {
					t.contextMenuItem = t.contextMenu.querySelector( '.' + t.options.classPrefix + 'contextmenu-item' );
					t.contextMenuItem.addEventListener( 'click', function() {
						window.location.href = t.options.siteUrl;
					});
				}
			}
					
		},

		wpveOnAgreeToPrivacy: function wpveOnAgreeToPrivacy() {
			
			var t = this;
			
			wpveSetCookie();			
						
			// Play video
			var src = t.node.getAttribute( 'data-src' );
			t.setSrc( src );
			
			setTimeout(function() {				
				t.load();
				t.play();
				
				t.layers[0].querySelector( '.' + t.options.classPrefix + 'overlay-play' ).style.display = 'none';	
				t.privacyLayer.style.display = 'none';
			}, 10);		
			
		}
			
	});
	
	/**
	 * Set cookie for accepting the privacy consent.
	 *
	 * @since    1.0.0
	 */
	function wpveSetCookie() {
		
		var data = {
			'action': 'wpve_set_cookie'
		};
		
		$.post( wpve.ajax_url, data, function( response ) {
			// console.log( 'Cookie stored!' );
		});
		
	}
	
	/**
	 * Called when the page has loaded.
	 *
	 * @since    1.0.0
	 */
	$(function() {			   
		
		/**
		 * oEmbed privacy button listener.
		 *
		 * @since    1.0.0
		 */
		$( '.wpve-privacy-consent-button' ).on( 'click', function() {
			
			wpveSetCookie();
			
			var container = $( this ).closest( '.wpve-privacy-wrapper' );
			
			var iframe = container.find( 'iframe' ).clone();
			var src = iframe.data( 'src' );
			iframe.attr( 'src', src );
			
			container.html( iframe );
			
		});
			   
	});

})( jQuery );