/**
 * Al-Hidayah theme script.
 *
 * Vanilla port of the original React storefront: smooth scrolling and reveals (Lenis + GSAP),
 * menu and bag drawers, AJAX WooCommerce bag, shop filters, product page controls and the
 * contact form.
 */
( function () {
	'use strict';

	var data = window.alhidayahData || {};
	var i18n = data.i18n || {};
	var reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var inEditor = document.body.classList.contains( 'elementor-editor-active' ) || window.location.search.indexOf( 'elementor-preview' ) > -1;
	var lenis = null;

	/* ---------------------------------------------------------------- helpers */

	function $( sel, root ) { return ( root || document ).querySelector( sel ); }
	function $$( sel, root ) { return Array.prototype.slice.call( ( root || document ).querySelectorAll( sel ) ); }

	function post( action, fields ) {
		var body = new FormData();
		body.append( 'action', action );
		Object.keys( fields || {} ).forEach( function ( k ) { body.append( k, fields[ k ] ); } );
		return fetch( data.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' } ).then( function ( r ) { return r.json(); } );
	}

	function scrollToEl( el, smooth ) {
		if ( ! el ) { return; }
		if ( lenis && smooth ) { lenis.scrollTo( el, { offset: 0 } ); return; }
		window.scrollTo( { top: el.getBoundingClientRect().top + window.scrollY, behavior: smooth && ! reduced ? 'smooth' : 'instant' } );
	}

	/* ---------------------------------------------------------------- motion */

	function motion() {
		if ( reduced || inEditor || ! data.motion || ! window.gsap || ! window.ScrollTrigger || ! window.Lenis ) { return; }
		var gsap = window.gsap, ScrollTrigger = window.ScrollTrigger;
		gsap.registerPlugin( ScrollTrigger );
		lenis = new window.Lenis( { duration: 1.05, smoothWheel: true, anchors: true, prevent: function ( node ) { return node.hasAttribute( 'data-lenis-prevent' ); } } );
		lenis.on( 'scroll', ScrollTrigger.update );
		gsap.ticker.add( function ( time ) { lenis.raf( time * 1000 ); } );
		gsap.ticker.lagSmoothing( 0 );
		var has = function ( s ) { return !! document.querySelector( s ); };

		if ( has( '.hero-copy' ) ) { gsap.from( '.hero-copy > *', { y: 26, opacity: 0, duration: 1, stagger: 0.13, ease: 'power3.out', delay: 0.1 } ); }
		if ( has( '.reveal-in' ) ) { gsap.from( '.reveal-in', { y: 24, opacity: 0, duration: 0.9, stagger: 0.1, ease: 'power3.out' } ); }
		$$( '[data-reveal]' ).forEach( function ( el ) {
			gsap.from( el, { y: 32, opacity: 0, duration: 0.8, ease: 'power2.out', scrollTrigger: { trigger: el, start: 'top 93%', once: true } } );
		} );
		if ( has( '.hero' ) ) { gsap.to( '.hero-photo', { yPercent: 8, ease: 'none', scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true } } ); }
		if ( has( '.ingredients' ) ) {
			$$( '.botanical' ).forEach( function ( el, i ) {
				gsap.to( el, { y: i % 2 ? 35 : -35, rotation: i % 2 ? 4 : -4, ease: 'none', scrollTrigger: { trigger: '.ingredients', start: 'top bottom', end: 'bottom top', scrub: 1 } } );
			} );
		}
		if ( has( '.footer-giant' ) ) { gsap.from( '.footer-giant text', { y: 60, ease: 'none', scrollTrigger: { trigger: '.footer-giant', start: 'top bottom', end: 'bottom bottom', scrub: true } } ); }

		// Late-loading images move sections; re-measure so reveals fire in the right place.
		var t = 0;
		var refresh = function () { clearTimeout( t ); t = setTimeout( function () { ScrollTrigger.refresh(); }, 150 ); };
		$$( 'img' ).forEach( function ( img ) { if ( ! img.complete ) { img.addEventListener( 'load', refresh, { once: true } ); } } );
		window.addEventListener( 'load', refresh );
	}

	/* ---------------------------------------------------------------- dialogs */

	var openDialog = null, lastFocus = null;
	var focusable = 'a[href], button:not([disabled]), input:not([disabled]), select, textarea, [tabindex]:not([tabindex="-1"])';

	function setExpanded( id, state ) {
		$$( '[data-ah-open="' + id + '"]' ).forEach( function ( b ) { b.setAttribute( 'aria-expanded', state ? 'true' : 'false' ); } );
	}

	function open( id ) {
		var dialog = document.getElementById( id );
		if ( ! dialog ) { return; }
		if ( openDialog ) { close( true ); }
		lastFocus = document.activeElement;
		dialog.hidden = false;
		openDialog = dialog;
		document.documentElement.classList.add( 'ah-locked' );
		if ( lenis ) { lenis.stop(); }
		setExpanded( id, true );
		var panel = $( '[role="dialog"]', dialog );
		var first = $( '.close', panel ) || $( focusable, panel );
		( first || panel ).focus();
	}

	function close( silent ) {
		if ( ! openDialog ) { return; }
		setExpanded( openDialog.id, false );
		openDialog.hidden = true;
		openDialog = null;
		document.documentElement.classList.remove( 'ah-locked' );
		if ( lenis ) { lenis.start(); }
		if ( ! silent && lastFocus && lastFocus.focus ) { lastFocus.focus(); }
	}

	document.addEventListener( 'click', function ( e ) {
		var opener = e.target.closest( '[data-ah-open]' );
		if ( opener ) { e.preventDefault(); open( opener.getAttribute( 'data-ah-open' ) ); return; }
		if ( e.target.closest( '[data-ah-close]' ) ) { e.preventDefault(); close(); return; }
		var link = e.target.closest( '[data-ah-close-link]' );
		if ( link ) { close( true ); }
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( ! openDialog ) { return; }
		if ( 'Escape' === e.key ) { close(); return; }
		if ( 'Tab' !== e.key ) { return; }
		var items = $$( focusable, $( '[role="dialog"]', openDialog ) ).filter( function ( el ) { return el.offsetParent !== null; } );
		if ( ! items.length ) { return; }
		var first = items[ 0 ], last = items[ items.length - 1 ];
		if ( e.shiftKey && document.activeElement === first ) { e.preventDefault(); last.focus(); }
		else if ( ! e.shiftKey && document.activeElement === last ) { e.preventDefault(); first.focus(); }
	} );

	/* ---------------------------------------------------------------- toast */

	var toastTimer = 0;
	function toast( message, withBag ) {
		var el = $( '[data-ah-toast]' );
		if ( ! el ) { return; }
		el.innerHTML = '';
		var check = '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>';
		el.insertAdjacentHTML( 'beforeend', check );
		el.appendChild( document.createTextNode( message ) );
		if ( withBag ) {
			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.textContent = i18n.viewBag || 'View bag';
			btn.addEventListener( 'click', function () { el.classList.remove( 'show' ); open( 'ah-bag' ); } );
			el.appendChild( btn );
		}
		el.classList.add( 'show' );
		clearTimeout( toastTimer );
		toastTimer = setTimeout( function () { el.classList.remove( 'show' ); }, 2800 );
	}

	/* ---------------------------------------------------------------- bag */

	function renderBag( res ) {
		if ( ! res || ! res.success ) { return; }
		var count = res.data.count;
		$$( '[data-ah-count]' ).forEach( function ( el ) { el.textContent = count; el.hidden = ! count; } );
		$$( '.bag-button' ).forEach( function ( b ) {
			var label = b.getAttribute( 'data-ah-bag-label' );
			if ( label ) { b.setAttribute( 'aria-label', label.replace( '%d', count ) ); }
		} );
		var body = $( '[data-ah-bag-body]' );
		if ( body && res.data.html ) { body.innerHTML = res.data.html; }
	}

	function addToBag( id, quantity, name, button ) {
		if ( button ) { button.disabled = true; }
		return post( 'alhidayah_cart_add', { nonce: data.cartNonce, product_id: id, quantity: quantity || 1 } ).then( function ( res ) {
			if ( button ) { button.disabled = false; }
			if ( ! res.success ) { toast( ( res.data && res.data.message ) || i18n.error ); return; }
			renderBag( res );
			toast( ( i18n.added || '%s added to your bag' ).replace( '%s', name || res.data.name ), true );
		} ).catch( function () { if ( button ) { button.disabled = false; } toast( i18n.error ); } );
	}

	document.addEventListener( 'click', function ( e ) {
		var add = e.target.closest( '[data-ah-add]' );
		if ( add ) { e.preventDefault(); addToBag( add.getAttribute( 'data-ah-add' ), 1, add.getAttribute( 'data-ah-name' ), add ); return; }
		var qty = e.target.closest( '[data-ah-qty]' );
		if ( qty ) {
			e.preventDefault();
			qty.disabled = true;
			post( 'alhidayah_cart_update', { nonce: data.cartNonce, key: qty.getAttribute( 'data-ah-qty' ), quantity: qty.getAttribute( 'data-value' ) } ).then( renderBag );
		}
	} );

	document.addEventListener( 'submit', function ( e ) {
		var form = e.target.closest( '[data-ah-add-form]' );
		if ( ! form ) { return; }
		e.preventDefault();
		var qtyInput = $( '[name="quantity"]', form );
		var button = $( '[type="submit"]', form );
		addToBag( form.getAttribute( 'data-ah-add-form' ), qtyInput ? qtyInput.value : 1, form.getAttribute( 'data-ah-name' ), button ).then( function () {
			if ( qtyInput ) { qtyInput.value = 1; qtyInput.dispatchEvent( new Event( 'change' ) ); }
		} );
	} );

	/* ---------------------------------------------------------------- shop filters */

	function shop() {
		var root = $( '[data-ah-shop]' );
		if ( ! root ) { return null; }
		var buttons = $$( '[data-ah-filter-button]', root );
		var search = $( '[data-ah-search]', root );
		var cards = $$( '[data-ah-card]', root );
		var guide = $( '[data-ah-guide]', root );
		var empty = $( '[data-ah-empty]', root );
		var state = { filter: 'all', q: '' };

		function apply() {
			var shown = 0;
			cards.forEach( function ( card ) {
				var ok = ( ' ' + card.getAttribute( 'data-filters' ) + ' ' ).indexOf( ' ' + state.filter + ' ' ) > -1 && card.getAttribute( 'data-search' ).indexOf( state.q ) > -1;
				card.hidden = ! ok;
				if ( ok ) { shown++; }
			} );
			if ( guide ) { guide.hidden = ! ( 'all' === state.filter && ! state.q ); }
			if ( empty ) { empty.hidden = shown > 0; }
			buttons.forEach( function ( b ) {
				var on = b.getAttribute( 'data-ah-filter-button' ) === state.filter;
				b.classList.toggle( 'active', on );
				b.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
			} );
		}

		buttons.forEach( function ( b ) { b.addEventListener( 'click', function () { state.filter = b.getAttribute( 'data-ah-filter-button' ); apply(); } ); } );
		if ( search ) { search.addEventListener( 'input', function () { state.q = search.value.trim().toLowerCase(); apply(); } ); }
		var reset = $( '[data-ah-reset]', root );
		if ( reset ) { reset.addEventListener( 'click', function () { state.filter = 'all'; state.q = ''; if ( search ) { search.value = ''; } apply(); } ); }

		return {
			set: function ( filter, scroll ) {
				if ( ! buttons.some( function ( b ) { return b.getAttribute( 'data-ah-filter-button' ) === filter; } ) ) { filter = 'all'; }
				state.filter = filter; state.q = ''; if ( search ) { search.value = ''; }
				apply();
				if ( scroll ) { scrollToEl( root, true ); }
			},
		};
	}

	/* ---------------------------------------------------------------- product page */

	function productPage() {
		var sheet = $( '[data-ah-product]' );
		if ( ! sheet ) { return; }
		var qty = $( '[name="quantity"]', sheet );
		var total = $( '[data-ah-total]', sheet );
		var was = $( '[data-ah-was]', sheet );
		var cur = data.currency || {};
		function money( n ) {
			var fixed = Number( n ).toFixed( cur.decimals );
			var parts = fixed.split( '.' );
			parts[ 0 ] = parts[ 0 ].replace( /\B(?=(\d{3})+(?!\d))/g, cur.thousand );
			var num = parts.join( cur.decimal );
			return ( cur.format || '%1$s%2$s' ).replace( '%1$s', cur.symbol ).replace( '%2$s', num );
		}
		function update() {
			var q = Math.max( 1, parseInt( qty.value, 10 ) || 1 );
			var max = parseInt( qty.getAttribute( 'max' ), 10 ) || 99;
			q = Math.min( q, max );
			qty.value = q;
			$( '[data-ah-step="-1"]', sheet ).disabled = q <= 1;
			$( '[data-ah-step="1"]', sheet ).disabled = q >= max;
			$( '[data-ah-qty-label]', sheet ).textContent = q;
			if ( total ) { total.textContent = money( total.getAttribute( 'data-unit' ) * q ); }
			if ( was ) { was.textContent = money( was.getAttribute( 'data-unit' ) * q ); }
		}
		if ( qty ) {
			$$( '[data-ah-step]', sheet ).forEach( function ( b ) {
				b.addEventListener( 'click', function () { qty.value = ( parseInt( qty.value, 10 ) || 1 ) + parseInt( b.getAttribute( 'data-ah-step' ), 10 ); update(); } );
			} );
			qty.addEventListener( 'change', update );
			update();
		}
		$$( '[data-ah-view]' ).forEach( function ( b ) {
			b.addEventListener( 'click', function () {
				var view = b.getAttribute( 'data-ah-view' );
				$$( '[data-ah-view]' ).forEach( function ( x ) { x.setAttribute( 'aria-pressed', x === b ? 'true' : 'false' ); } );
				$$( '[data-ah-media]' ).forEach( function ( m ) { m.hidden = m.getAttribute( 'data-ah-media' ) !== view; } );
			} );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( openDialog || e.target.closest( 'input, textarea, select, [contenteditable]' ) ) { return; }
			var target = 'ArrowLeft' === e.key ? $( '[data-ah-prev]' ) : 'ArrowRight' === e.key ? $( '[data-ah-next]' ) : null;
			if ( target ) { window.location.href = target.href; }
		} );
	}

	/* ---------------------------------------------------------------- contact form */

	function contact() {
		var form = $( '[data-ah-contact]' );
		if ( ! form ) { return; }
		var done = $( '[data-ah-done]' );
		var failed = $( '[data-ah-failed]', form );
		var submit = $( '[type="submit"]', form );
		var topic = $( '#contact-topic', form );
		var orderField = $( '[data-ah-order-field]', form );

		function val( name ) { var el = form.elements[ name ]; return el ? el.value.trim() : ''; }
		function setError( name, message ) {
			var field = form.elements[ name ];
			if ( ! field ) { return; }
			var wrap = field.closest( '.field' );
			var small = $( 'small', wrap );
			wrap.classList.toggle( 'invalid', !! message );
			field.setAttribute( 'aria-invalid', message ? 'true' : 'false' );
			if ( message ) {
				if ( ! small ) { small = document.createElement( 'small' ); small.id = field.id + '-error'; wrap.appendChild( small ); }
				small.textContent = message;
				field.setAttribute( 'aria-describedby', small.id );
			} else if ( small ) {
				small.remove();
				field.removeAttribute( 'aria-describedby' );
			}
		}
		function validate() {
			var errors = {};
			if ( val( 'name' ).length < 2 ) { errors.name = i18n.name; }
			if ( ! /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( val( 'email' ) ) ) { errors.email = i18n.email; }
			if ( val( 'phone' ) && val( 'phone' ).replace( /\D/g, '' ).length < 7 ) { errors.phone = i18n.phone; }
			if ( val( 'message' ).length < 10 ) { errors.message = i18n.message; }
			[ 'name', 'email', 'phone', 'message' ].forEach( function ( k ) { setError( k, errors[ k ] ); } );
			var first = Object.keys( errors )[ 0 ];
			if ( first ) { form.elements[ first ].focus(); }
			return ! first;
		}
		function text() {
			var head = [ 'Topic: ' + val( 'topic' ) ];
			if ( 'Order enquiry' === val( 'topic' ) && val( 'order' ) ) { head.push( 'Order number: ' + val( 'order' ) ); }
			var sign = [ '— ' + val( 'name' ), val( 'email' ), val( 'phone' ) ].filter( Boolean );
			return [ head.join( '\n' ), val( 'message' ), sign.join( '\n' ) ].join( '\n\n' );
		}
		function send( channel ) {
			if ( failed ) { failed.hidden = true; }
			if ( ! validate() ) { return Promise.resolve( false ); }
			submit.disabled = true;
			submit.lastChild.textContent = ' ' + i18n.sending;
			var fields = { nonce: data.contactNonce, channel: channel };
			[ 'name', 'email', 'phone', 'topic', 'order', 'message', 'website', 'started' ].forEach( function ( k ) { fields[ k ] = val( k ); } );
			return post( 'alhidayah_contact_submit', fields ).then( function ( res ) {
				submit.disabled = false;
				submit.lastChild.textContent = ' ' + i18n.send;
				if ( ! res.success ) {
					if ( res.data && res.data.errors ) { Object.keys( res.data.errors ).forEach( function ( k ) { setError( k, res.data.errors[ k ] ); } ); }
					else if ( failed ) { failed.hidden = false; }
					return false;
				}
				return true;
			} ).catch( function () { submit.disabled = false; submit.lastChild.textContent = ' ' + i18n.send; if ( failed ) { failed.hidden = false; } return false; } );
		}
		function showDone( whatsapp ) {
			form.hidden = true;
			done.hidden = false;
			$( '[data-ah-done-title]', done ).textContent = done.getAttribute( whatsapp ? 'data-title-wa' : 'data-title' );
			$( '[data-ah-done-text]', done ).textContent = done.getAttribute( whatsapp ? 'data-text-wa' : 'data-text' );
			done.focus();
		}

		if ( topic && orderField ) {
			var toggle = function () { orderField.hidden = 'Order enquiry' !== topic.value; };
			topic.addEventListener( 'change', toggle );
			toggle();
		}
		$$( 'input, textarea', form ).forEach( function ( el ) {
			el.addEventListener( 'input', function () { if ( el.closest( '.field.invalid' ) ) { setError( el.name, '' ); } } );
		} );
		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			send( 'form' ).then( function ( ok ) { if ( ok ) { form.reset(); showDone( false ); } } );
		} );
		var wa = $( '[data-ah-whatsapp]', form );
		if ( wa ) {
			wa.addEventListener( 'click', function () {
				if ( ! validate() ) { return; }
				// Open the window synchronously (popup blockers), then record the inquiry.
				var win = window.open( 'https://wa.me/' + data.whatsapp + '?text=' + encodeURIComponent( 'Hello ' + data.brand + ',\n\n' + text() ), '_blank', 'noopener' );
				send( 'whatsapp' ).then( function ( ok ) { if ( ok || win ) { form.reset(); showDone( true ); } } );
			} );
		}
		var again = $( '[data-ah-again]' );
		if ( again ) { again.addEventListener( 'click', function () { done.hidden = true; form.hidden = false; if ( orderField ) { orderField.hidden = 'Order enquiry' !== topic.value; } } ); }
	}

	/* ---------------------------------------------------------------- boot */

	motion();
	var filters = shop();
	productPage();
	contact();

	// Collection cards filter the shop in place when it is on the page.
	document.addEventListener( 'click', function ( e ) {
		var card = e.target.closest( '[data-ah-filter]' );
		if ( card && filters ) { e.preventDefault(); filters.set( card.getAttribute( 'data-ah-filter' ), true ); }
	} );

	// ?filter=new#shop (footer links, collection cards from other pages).
	var params = new URLSearchParams( window.location.search );
	if ( filters && params.get( 'filter' ) ) {
		filters.set( params.get( 'filter' ), false );
	}
	// Arriving with a hash: wait for layout, then jump to the section.
	if ( window.location.hash.length > 1 ) {
		setTimeout( function () {
			var el = document.getElementById( decodeURIComponent( window.location.hash.slice( 1 ) ) );
			if ( el ) { window.scrollTo( { top: el.getBoundingClientRect().top + window.scrollY, behavior: 'instant' } ); }
		}, 80 );
	}
}() );
