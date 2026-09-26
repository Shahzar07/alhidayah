<?php
/**
 * Demo content imported by Appearance → Alhidayah Setup.
 *
 * Prices, notes and copy are starter content: edit them in WooCommerce → Products after import.
 *
 * @package AlHidayah
 */

defined( 'ABSPATH' ) || exit;

$p  = static function ( $text ) {
	return '<!-- wp:paragraph --><p>' . $text . '</p><!-- /wp:paragraph -->';
};
$ul = static function ( $items, $class = '' ) {
	$li = '';
	foreach ( $items as $item ) {
		$li .= '<!-- wp:list-item --><li>' . $item . '</li><!-- /wp:list-item -->';
	}
	return '<!-- wp:list' . ( $class ? ' {"className":"' . $class . '"}' : '' ) . ' --><ul class="wp-block-list' . ( $class ? ' ' . $class : '' ) . '">' . $li . '</ul><!-- /wp:list -->';
};
$qa = static function ( $q, $a ) {
	return '<!-- wp:details --><details class="wp-block-details"><summary>' . $q . '</summary><!-- wp:paragraph --><p>' . $a . '</p><!-- /wp:paragraph --></details><!-- /wp:details -->';
};
$btn = static function ( $label, $url ) {
	return '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $url ) . '">' . $label . '</a></div><!-- /wp:button --></div><!-- /wp:buttons -->';
};
$contact = '<!-- wp:shortcode -->[alhidayah_contact_links]<!-- /wp:shortcode -->';
$shop    = $btn( 'Explore the collection', home_url( '/#shop' ) );

return array(
	'categories'   => array(
		array( 'oud-woody', 'Oud & woody', 'Rich & regal', 'collections/oud.jpg', 'Rozta-ul-Oud and No. 56 beside burning bakhoor and agarwood' ),
		array( 'soft-floral', 'Soft & floral', 'Soft & expressive', 'collections/floral.jpg', 'Floranza and Fog Purple among peonies and lavender' ),
		array( 'warm-spicy', 'Warm & spicy', 'Bold & magnetic', 'collections/spicy.jpg', 'Vortex with cinnamon, star anise and dried rose petals' ),
	),

	'products'     => array(
		array(
			'slug'        => 'rozta-ul-oud',
			'name'        => 'Rozta-ul-Oud',
			'category'    => 'oud-woody',
			'price'       => '145',
			'sale'        => '',
			'sku'         => 'AH-ROZTA-50',
			'description' => 'A garden of oud at dusk. Precious agarwood glows with saffron and softens into Taif rose, leaving a warm trail of amber that stays for hours.',
			'meta'        => array(
				'mood'         => 'Regal, smoky, enveloping',
				'accords'      => 'Agarwood, Saffron, Taif Rose',
				'notes_top'    => 'Saffron, Bergamot',
				'notes_middle' => 'Taif rose, Incense',
				'notes_base'   => 'Oud, Amber, Musk',
				'story'        => 'Rozta means garden. We built this scent around the quiet ritual of evening bakhoor: smoke curling through rose, wood and warm stone.',
				'tint'         => '#e9dcc8',
				'is_new'       => 'yes',
				'best_seller'  => 'yes',
				'limited'      => 'no',
			),
		),
		array(
			'slug'        => 'no-56',
			'name'        => 'No. 56',
			'category'    => 'oud-woody',
			'price'       => '120',
			'sale'        => '',
			'sku'         => 'AH-56-50',
			'description' => 'Crisp and composed. Sparkling bergamot meets aromatic lavender and a dry, woody base of vetiver and cedar that feels effortlessly sharp.',
			'meta'        => array(
				'mood'         => 'Clean, confident, modern',
				'accords'      => 'Bergamot, Vetiver, Cedar',
				'notes_top'    => 'Bergamot, Grapefruit',
				'notes_middle' => 'Lavender, Black pepper',
				'notes_base'   => 'Vetiver, Cedar',
				'story'        => 'Our most-worn signature. A clean woody fragrance that moves from the office to the evening without asking for attention, and gets it anyway.',
				'tint'         => '#e4e1dc',
				'is_new'       => 'no',
				'best_seller'  => 'yes',
				'limited'      => 'no',
			),
		),
		array(
			'slug'        => 'fog-purple',
			'name'        => 'Fog Purple',
			'category'    => 'soft-floral',
			'price'       => '130',
			'sale'        => '',
			'sku'         => 'AH-FOG-50',
			'description' => 'Velvet after dark. Juicy black plum and pink pepper drift into powdery violet and iris, resting on a smooth base of patchouli and vanilla.',
			'meta'        => array(
				'mood'         => 'Mysterious, velvety, magnetic',
				'accords'      => 'Black Plum, Violet, Patchouli',
				'notes_top'    => 'Black plum, Pink pepper',
				'notes_middle' => 'Violet, Iris',
				'notes_base'   => 'Patchouli, Vanilla',
				'story'        => 'Inspired by twilight haze over the city. Fog Purple is soft at first touch and deepens the longer you wear it.',
				'tint'         => '#e6dde8',
				'is_new'       => 'no',
				'best_seller'  => 'no',
				'limited'      => 'yes',
			),
		),
		array(
			'slug'        => 'floranza',
			'name'        => 'Floranza',
			'category'    => 'soft-floral',
			'price'       => '125',
			'sale'        => '',
			'sku'         => 'AH-FLORANZA-50',
			'description' => 'A bouquet in full bloom. Fresh pear and lychee open onto lush peony and Damask rose, settling into a soft veil of white musk and sandalwood.',
			'meta'        => array(
				'mood'         => 'Romantic, radiant, feminine',
				'accords'      => 'Peony, Damask Rose, White Musk',
				'notes_top'    => 'Pear, Pink lychee',
				'notes_middle' => 'Peony, Damask rose',
				'notes_base'   => 'White musk, Sandalwood',
				'story'        => 'A love letter to spring gardens. Floranza is light enough for every day and pretty enough for every celebration.',
				'tint'         => '#f1e1dd',
				'is_new'       => 'yes',
				'best_seller'  => 'no',
				'limited'      => 'no',
			),
		),
		array(
			'slug'        => 'vortex',
			'name'        => 'Vortex',
			'category'    => 'warm-spicy',
			'price'       => '159',
			'sale'        => '135',
			'sku'         => 'AH-VORTEX-50',
			'description' => 'Magnetic heat. Red berries and mandarin collide with cinnamon and cardamom, then melt into a sweet, lingering base of amber and tonka.',
			'meta'        => array(
				'mood'         => 'Bold, spicy, irresistible',
				'accords'      => 'Red Berries, Cinnamon, Amber',
				'notes_top'    => 'Red berries, Mandarin',
				'notes_middle' => 'Cinnamon, Cardamom',
				'notes_base'   => 'Amber, Tonka bean',
				'story'        => 'Made for the moment you walk into a room. Vortex pulls people in with warm spice and a trail that is hard to forget.',
				'tint'         => '#efdcd3',
				'is_new'       => 'no',
				'best_seller'  => 'no',
				'limited'      => 'no',
			),
		),
	),

	// Sample reviews for layout. Replace them with real customer reviews (Testimonials menu).
	'testimonials' => array(
		array( 'Ayesha Khan', 'Wellness Coach', 4.5, '#e8cdb5', 'I’m in love with the floral line. ‘Floranza’ has this soft rose-and-peony touch that makes me feel calm and put together.' ),
		array( 'Hamza Malik', 'Marketing Specialist', 5, '#cdb9a3', 'This completely changed the way I feel about oud. ‘Rozta-ul-Oud’ is rich, smooth and never too heavy. I get compliments every single day!' ),
		array( 'Daniel Cho', 'UI/UX Designer', 4.5, '#d5c6b8', 'I usually don’t wear perfume, but ‘No. 56’ is so clean and fresh. It’s perfect for my workdays: not too strong, just right.' ),
		array( 'Sara Ahmed', 'Architect', 5, '#e3c9c9', 'Tried it on a friend’s recommendation and instantly understood why. The floral notes are elegant and the bottle looks beautiful on my shelf.' ),
		array( 'Mark Evans', 'Photographer', 4.5, '#c9c2b6', 'The packaging is beautiful, and the scent lasts long. ‘Vortex’ gives a really warm, confident vibe. Highly recommended!' ),
		array( 'Zainab Raza', 'Fashion Content Creator', 4.5, '#d9c3d6', 'Al-Hidayah feels like a luxury brand at a fair price. ‘Fog Purple’ is velvety and mysterious. I wear it every evening!' ),
		array( 'Omar Siddiqui', 'Art Director', 4, '#cbbba8', 'Every scent feels considered. ‘Rozta-ul-Oud’ gives me a confident vibe that lasts well into evening events.' ),
		array( 'Hira Aslam', 'Doctor', 5, '#e6d2bf', 'Long-lasting without being overpowering. I keep ‘Floranza’ in my bag and ‘No. 56’ on my desk. Both are simply gorgeous.' ),
	),

	// slug => [ title, content ]. Policy wording is a starting point; review it before launch.
	'pages'        => array(
		'our-story'          => array( 'Our story', $p( 'Al-Hidayah means “the guidance”. We believe a fragrance should guide a memory home: the bakhoor of a family gathering, roses after rain, the warmth of oud on a winter evening.' ) . $p( 'Every scent in our collection is composed to feel personal, long-lasting and quietly luxurious, bridging classic Eastern perfumery with a clean, modern signature.' ) . $ul( array( '01 · Thoughtful compositions', '02 · Long-lasting eau de parfum', '03 · Crafted for everyday moments' ), 'story-list' ) . $shop ),
		'store-locator'      => array( 'Store locator', $p( 'Al-Hidayah is currently available online, with delivery to your door.' ) . $p( 'Looking for a stockist near you or want to experience the scents in person? Reach out and our team will point you to the nearest pop-up or partner store.' ) . $contact ),
		'ingredients-ethics' => array( 'Ingredients & ethics', $p( 'We work with perfumers and suppliers who share our standards: carefully selected aromatic materials, alcohol-based eau de parfum concentrations for performance, and responsible sourcing of precious notes like oud and saffron.' ) . $ul( array( 'Never tested on animals', 'Quality-checked batch by batch', 'Recyclable glass bottles' ), 'story-list' ) ),
		'scent-guide'        => array( 'Scent guide', $p( 'Not sure where to start? Choose by mood:' ) . $ul( array( '<strong>Oud &amp; woody:</strong> Rozta-ul-Oud for richness, No. 56 for a clean modern signature.', '<strong>Soft &amp; floral:</strong> Floranza for romance, Fog Purple for evening mystery.', '<strong>Warm &amp; spicy:</strong> Vortex when you want to be noticed.' ) ) . $p( 'Apply to pulse points (wrists, neck, behind the ears) and let it settle without rubbing.' ) . $shop ),
		'journal-tips'       => array( 'Journal & tips', $p( 'Make your fragrance last longer:' ) . $ul( array( 'Moisturise first: scent holds better on hydrated skin.', 'Spray on clothes and hair for a longer trail.', 'Layer a woody scent under a floral for depth.', 'Store bottles away from sunlight, heat and humidity.' ) ) ),
		'shipping-delivery'  => array( 'Shipping & delivery', $p( 'Orders are packed with care and dispatched within 1–2 business days. Delivery times and charges depend on your city and are shown at checkout.' ) . $p( 'You will receive a confirmation email with your order details, and our team will keep you updated until it arrives.' ) . $contact ),
		'returns-exchanges'  => array( 'Returns & exchanges', $p( 'If your order arrives damaged or incorrect, contact us within 7 days of delivery with your order number and a photo, and we will arrange a replacement.' ) . $p( 'For hygiene reasons, opened fragrances can only be exchanged if they are faulty.' ) . $contact ),
		'faq'                => array( 'Frequently asked questions', $qa( 'How long does the fragrance last?', 'Our eau de parfum concentration is made to last 6–10 hours on skin, and longer on fabric.' ) . $qa( 'What size are the bottles?', 'Every fragrance comes in a 50 ml / 1.7 oz glass bottle.' ) . $qa( 'How do I place an order?', 'Add your favourites to the bag and check out securely. You will receive an order confirmation by email, and our team confirms delivery with you.' ) . $qa( 'Can I gift a fragrance?', 'Yes. Leave a note with your order and we will add a handwritten gift card.' ) ),
		'track-order'        => array( 'Track your order', $p( 'Enter your order number and the email you used at checkout to see the latest status.' ) . '<!-- wp:shortcode -->[woocommerce_order_tracking]<!-- /wp:shortcode -->' ),
		'gift-sets'          => array( 'Gift sets', $p( 'Every Al-Hidayah fragrance makes a thoughtful gift. Order any two scents together and mention “gift” in your order note for complimentary gift wrapping and a handwritten card.' ) . $shop ),
		'terms-of-service'   => array( 'Terms of service', $p( 'By using this website and placing an order with Al-Hidayah you agree to provide accurate contact and delivery details. Product images are representative; packaging may vary slightly.' ) . $p( 'Prices are shown in the store currency and may change without notice. An order is confirmed once you receive our order confirmation email.' ) ),
		'privacy-policy'     => array( 'Privacy policy', $p( 'We collect the details you provide at checkout or through our contact form (name, email, phone and delivery address) and use them only to process your order, deliver it and reply to your messages.' ) . $p( 'Your shopping bag is kept in a secure session so it is here when you return. We do not sell your personal data. You can request a copy or deletion of your data at any time by contacting us.' ) . $contact ),
		'refund-policy'      => array( 'Refund policy', $p( 'If an item is faulty or damaged in transit, we will replace it or refund it in full once the return is received. Refunds are issued to the original payment method.' ) . $contact ),
		'cookie-settings'    => array( 'Cookie settings', $p( 'We only use essential cookies: they keep your shopping bag and checkout session working. No advertising or tracking cookies are set by this theme.' ) . $p( 'You can clear cookies at any time in your browser settings; your bag will then be emptied.' ) ),
		'accessibility'      => array( 'Accessibility', $p( 'This website supports keyboard navigation, screen readers and reduced-motion preferences. If anything is hard to use, please tell us and we will fix it.' ) . $contact ),
	),
);
