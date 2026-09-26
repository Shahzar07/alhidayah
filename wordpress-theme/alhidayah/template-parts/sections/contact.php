<?php
/**
 * Contact: contact channels, the inquiry form (saved to Forms) and FAQ.
 *
 * @package AlHidayah
 *
 * @var array $args { pill, heading, text, form_heading, show_faq, faq_heading, faq }
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'pill'         => __( 'Contact us', 'alhidayah' ),
		'heading'      => __( 'We’d love to<br>hear from you', 'alhidayah' ),
		'text'         => __( 'Questions about an order, help choosing a signature scent, or gifting for someone special. Our team replies within one business day.', 'alhidayah' ),
		'form_heading' => __( 'Send us a message', 'alhidayah' ),
		'show_faq'     => true,
		'faq_heading'  => __( 'Frequently asked<br>questions', 'alhidayah' ),
		'faq'          => array(
			array( __( 'How long does the fragrance last?', 'alhidayah' ), __( 'Our eau de parfum concentration is made to last 6–10 hours on skin, and longer on fabric.', 'alhidayah' ) ),
			array( __( 'What size are the bottles?', 'alhidayah' ), __( 'Every fragrance comes in a 50 ml / 1.7 oz glass bottle.', 'alhidayah' ) ),
			array( __( 'How do I place an order?', 'alhidayah' ), __( 'Add your favourites to the bag and check out securely. You will receive an order confirmation by email, and our team confirms delivery with you.', 'alhidayah' ) ),
			array( __( 'Can I gift a fragrance?', 'alhidayah' ), __( 'Yes. Leave a note with your order and we will add a handwritten gift card.', 'alhidayah' ) ),
		),
	)
);

$email  = alhidayah_opt( 'contact_email' );
$phone  = alhidayah_opt( 'contact_phone' );
$topics = alhidayah_contact_topics();
?>
<section class="contact-hero">
	<div class="contact-intro reveal-in">
		<span class="soft-pill"><?php echo esc_html( $args['pill'] ); ?></span>
		<h1><?php alhidayah_inline( $args['heading'] ); ?></h1>
		<p><?php echo esc_html( $args['text'] ); ?></p>
		<ul class="contact-cards">
			<li><a href="<?php echo esc_url( 'mailto:' . antispambot( $email ) ); ?>"><?php alhidayah_icon( 'mail', 18 ); ?><span><small><?php esc_html_e( 'Email us', 'alhidayah' ); ?></small><?php echo esc_html( antispambot( $email ) ); ?></span><?php alhidayah_icon( 'arrow-up-right', 16 ); ?></a></li>
			<li><a href="<?php echo esc_url( 'tel:' . alhidayah_digits( $phone ) ); ?>"><?php alhidayah_icon( 'phone', 18 ); ?><span><small><?php esc_html_e( 'Call us', 'alhidayah' ); ?></small><?php echo esc_html( $phone ); ?></span><?php alhidayah_icon( 'arrow-up-right', 16 ); ?></a></li>
			<li><a href="<?php echo esc_url( 'https://wa.me/' . alhidayah_whatsapp() ); ?>" target="_blank" rel="noreferrer"><?php alhidayah_icon( 'message-circle', 18 ); ?><span><small><?php esc_html_e( 'WhatsApp', 'alhidayah' ); ?></small><?php esc_html_e( 'Chat with our team', 'alhidayah' ); ?></span><?php alhidayah_icon( 'arrow-up-right', 16 ); ?></a></li>
			<li><div><?php alhidayah_icon( 'headset', 18 ); ?><span><small><?php esc_html_e( 'Support line', 'alhidayah' ); ?></small><?php echo esc_html( alhidayah_opt( 'contact_support' ) ); ?></span></div></li>
			<li><div><?php alhidayah_icon( 'clock', 18 ); ?><span><small><?php esc_html_e( 'Opening hours', 'alhidayah' ); ?></small><?php echo esc_html( alhidayah_opt( 'contact_hours' ) ); ?></span></div></li>
		</ul>
		<?php $socials = alhidayah_socials(); ?>
		<?php if ( $socials ) : ?>
			<div class="contact-social">
				<?php foreach ( $socials as $social ) : ?>
					<a href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noreferrer"><?php echo esc_html( $social['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="contact-form reveal-in">
		<form data-ah-contact novalidate>
			<h2><?php echo esc_html( $args['form_heading'] ); ?></h2>
			<div class="form-row">
				<label class="field"><span><?php esc_html_e( 'Full name *', 'alhidayah' ); ?></span><input id="contact-name" name="name" autocomplete="name" placeholder="<?php esc_attr_e( 'Your name', 'alhidayah' ); ?>" required></label>
				<label class="field"><span><?php esc_html_e( 'Email *', 'alhidayah' ); ?></span><input id="contact-email" name="email" type="email" autocomplete="email" placeholder="you@example.com" required></label>
			</div>
			<div class="form-row">
				<label class="field"><span><?php esc_html_e( 'Phone (optional)', 'alhidayah' ); ?></span><input id="contact-phone" name="phone" type="tel" autocomplete="tel" placeholder="+92 …"></label>
				<label class="field"><span><?php esc_html_e( 'Topic', 'alhidayah' ); ?></span><select id="contact-topic" name="topic">
					<?php foreach ( $topics as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select></label>
			</div>
			<label class="field" data-ah-order-field><span><?php esc_html_e( 'Order number (optional)', 'alhidayah' ); ?></span><input id="contact-order" name="order" placeholder="<?php esc_attr_e( 'e.g. AH-1024', 'alhidayah' ); ?>"></label>
			<label class="field"><span><?php esc_html_e( 'Message *', 'alhidayah' ); ?></span><textarea id="contact-message" name="message" rows="5" placeholder="<?php esc_attr_e( 'How can we help?', 'alhidayah' ); ?>" required></textarea></label>
			<div class="ah-hp" aria-hidden="true"><label><?php esc_html_e( 'Leave this empty', 'alhidayah' ); ?><input name="website" tabindex="-1" autocomplete="off"></label></div>
			<input type="hidden" name="started" value="<?php echo esc_attr( time() ); ?>">
			<p class="form-error" role="alert" data-ah-failed hidden><?php echo esc_html( sprintf( /* translators: %s email */ __( 'We couldn’t send your message just now. Please try again, or email us at %s.', 'alhidayah' ), antispambot( $email ) ) ); ?></p>
			<div class="form-actions">
				<button class="button black" type="submit"><?php alhidayah_icon( 'send', 15 ); ?> <?php esc_html_e( 'Send message', 'alhidayah' ); ?></button>
				<button class="button outline" type="button" data-ah-whatsapp><?php alhidayah_icon( 'message-circle', 15 ); ?> <?php esc_html_e( 'Send on WhatsApp', 'alhidayah' ); ?></button>
			</div>
			<p class="form-fine"><?php esc_html_e( 'We only use your details to reply to your message.', 'alhidayah' ); ?></p>
		</form>
		<div class="form-done" role="status" tabindex="-1" data-ah-done hidden
			data-title="<?php esc_attr_e( 'Message sent', 'alhidayah' ); ?>"
			data-text="<?php esc_attr_e( 'Thank you for reaching out. Our team will reply to your email within one business day.', 'alhidayah' ); ?>"
			data-title-wa="<?php esc_attr_e( 'Almost there', 'alhidayah' ); ?>"
			data-text-wa="<?php esc_attr_e( 'Your message is ready in WhatsApp and saved with our team. Press send there and we will reply within one business day.', 'alhidayah' ); ?>">
			<span class="done-icon"><?php alhidayah_icon( 'check', 22 ); ?></span>
			<h2 data-ah-done-title></h2>
			<p data-ah-done-text></p>
			<button type="button" class="button black" data-ah-again><?php esc_html_e( 'Write another message', 'alhidayah' ); ?></button>
		</div>
	</div>
</section>

<?php if ( $args['show_faq'] && $args['faq'] ) : ?>
	<section class="contact-faq section" data-reveal>
		<div>
			<span class="eyebrow"><?php esc_html_e( 'Quick answers', 'alhidayah' ); ?></span>
			<h2><?php alhidayah_inline( $args['faq_heading'] ); ?></h2>
			<a class="button black" href="<?php echo esc_url( home_url( '/#shop' ) ); ?>"><?php esc_html_e( 'Explore fragrances', 'alhidayah' ); ?> <?php alhidayah_icon( 'arrow-up-right', 15 ); ?></a>
		</div>
		<div class="faq">
			<?php foreach ( $args['faq'] as $qa ) : ?>
				<details><summary><?php echo esc_html( $qa[0] ); ?><?php alhidayah_icon( 'plus', 16 ); ?></summary><p><?php echo esc_html( $qa[1] ); ?></p></details>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>
