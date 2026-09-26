/**
 * Checkout helpers: inline discount code, mobile order-summary toggle, selected-option styling.
 * Works alongside WooCommerce's own checkout.js (wc-checkout).
 */
( function ( $ ) {
	'use strict';

	var params = window.wc_checkout_params || {};
	var $body = $( document.body );

	function endpoint( name ) {
		return params.wc_ajax_url ? params.wc_ajax_url.toString().replace( '%%endpoint%%', name ) : '';
	}

	function message( text, isError ) {
		var $msg = $( '[data-ah-coupon-message]' );
		$msg.text( text ).toggleClass( 'is-error', !! isError ).prop( 'hidden', ! text );
	}

	function applyCoupon() {
		var $input = $( '[data-ah-coupon-input]' );
		var code = $.trim( $input.val() );
		if ( ! code ) { $input.trigger( 'focus' ); return; }
		var $box = $( '[data-ah-coupon-box]' ).addClass( 'is-loading' );
		$.post( endpoint( 'apply_coupon' ), { security: params.apply_coupon_nonce, coupon_code: code, billing_email: $( '#billing_email' ).val() } )
			.done( function ( html ) {
				var text = $( '<div>' ).html( html ).text().trim();
				var isError = /woocommerce-error|is-error/.test( html );
				$body.trigger( 'update_checkout', { update_shipping_method: false } );
				// The summary is re-rendered; show the result once it is back.
				$body.one( 'updated_checkout', function () { message( text, isError ); if ( isError ) { $( '[data-ah-coupon-input]' ).val( code ); } } );
			} )
			.always( function () { $box.removeClass( 'is-loading' ); } );
	}

	$( document )
		.on( 'click', '[data-ah-coupon-apply]', function ( e ) { e.preventDefault(); applyCoupon(); } )
		.on( 'keydown', '[data-ah-coupon-input]', function ( e ) { if ( 13 === e.which ) { e.preventDefault(); applyCoupon(); } } )
		.on( 'click', '[data-ah-summary-toggle]', function () {
			var $btn = $( this );
			var open = 'true' !== $btn.attr( 'aria-expanded' );
			$btn.attr( 'aria-expanded', open ? 'true' : 'false' );
			$btn.find( '[data-show]' ).prop( 'hidden', open );
			$btn.find( '[data-hide]' ).prop( 'hidden', ! open );
			$( '#ah-co-summary' ).toggleClass( 'is-open', open );
		} )
		.on( 'change', 'input[name^="shipping_method"]', function () {
			$( '.ah-option' ).removeClass( 'is-checked' );
			$( this ).closest( '.ah-option' ).addClass( 'is-checked' );
		} );
}( jQuery ) );
