import { brand } from './brand';

// Content for the footer's customer care, discovery and legal pages (shown in a dialog).
// Policy wording is a starting point; review it with the business before launch.
export type InfoBlock = { p?: string; list?: string[]; faq?: [string, string][] };
export type InfoTopic = { eyebrow: string; title: string; blocks: InfoBlock[]; action?: 'shop' | 'contact' | 'track' | 'cookies' };

export const info: Record<string, InfoTopic> = {
  'our-story': { eyebrow: 'Discover', title: 'Our story', blocks: [
    { p: `${brand.name} means “the guidance”. We believe a fragrance should guide a memory home: the bakhoor of a family gathering, roses after rain, the warmth of oud on a winter evening.` },
    { p: 'Every scent in our collection is composed to feel personal, long-lasting and quietly luxurious, bridging classic Eastern perfumery with a clean, modern signature.' },
    { list: ['01 · Thoughtful compositions', '02 · Long-lasting eau de parfum', '03 · Crafted for everyday moments'] },
  ], action: 'shop' },
  'store-locator': { eyebrow: 'Discover', title: 'Store locator', blocks: [
    { p: `${brand.name} is currently available online, with delivery to your door.` },
    { p: `Looking for a stockist near you or want to experience the scents in person? Reach out and our team will point you to the nearest pop-up or partner store.` },
  ], action: 'contact' },
  'ingredients-ethics': { eyebrow: 'Discover', title: 'Ingredients & ethics', blocks: [
    { p: 'We work with perfumers and suppliers who share our standards: carefully selected aromatic materials, alcohol-based eau de parfum concentrations for performance, and responsible sourcing of precious notes like oud and saffron.' },
    { list: ['Never tested on animals', 'Quality-checked batch by batch', 'Recyclable glass bottles'] },
  ] },
  'scent-guide': { eyebrow: 'Discover', title: 'Scent guide', blocks: [
    { p: 'Not sure where to start? Choose by mood:' },
    { list: ['Oud & woody: Rozta-ul-Oud for richness, No. 56 for a clean modern signature.', 'Soft & floral: Floranza for romance, Fog Purple for evening mystery.', 'Warm & spicy: Vortex when you want to be noticed.'] },
    { p: 'Apply to pulse points (wrists, neck, behind the ears) and let it settle without rubbing.' },
  ], action: 'shop' },
  'journal-tips': { eyebrow: 'Discover', title: 'Journal & tips', blocks: [
    { p: 'Make your fragrance last longer:' },
    { list: ['Moisturise first: scent holds better on hydrated skin.', 'Spray on clothes and hair for a longer trail.', 'Layer a woody scent under a floral for depth.', 'Store bottles away from sunlight, heat and humidity.'] },
  ] },
  'shipping-delivery': { eyebrow: 'Customer care', title: 'Shipping & delivery', blocks: [
    { p: 'Orders are packed with care and dispatched within 1–2 business days. Delivery times and charges depend on your city and are confirmed with you when your order is placed.' },
    { p: 'You will receive a confirmation with your order details, and our team will keep you updated until it arrives.' },
  ], action: 'contact' },
  'returns-exchanges': { eyebrow: 'Customer care', title: 'Returns & exchanges', blocks: [
    { p: 'If your order arrives damaged or incorrect, contact us within 7 days of delivery with your order number and a photo, and we will arrange a replacement.' },
    { p: 'For hygiene reasons, opened fragrances can only be exchanged if they are faulty.' },
  ], action: 'contact' },
  faq: { eyebrow: 'Customer care', title: 'Frequently asked questions', blocks: [
    { faq: [
      ['How long does the fragrance last?', 'Our eau de parfum concentration is made to last 6–10 hours on skin, and longer on fabric.'],
      ['What size are the bottles?', 'Every fragrance comes in a 50 ml / 1.7 oz glass bottle.'],
      ['How do I place an order?', 'Add your favourites to the bag and send your order on WhatsApp or by email. Our team confirms delivery and payment details with you directly.'],
      ['Can I gift a fragrance?', 'Yes. Leave a note with your order and we will add a handwritten gift card.'],
    ] },
  ] },
  'track-order': { eyebrow: 'Customer care', title: 'Track your order', blocks: [
    { p: 'Enter your order number and we will send you the latest status.' },
  ], action: 'track' },
  'contact-support': { eyebrow: 'Customer care', title: 'Contact support', blocks: [
    { p: 'We would love to help you find your signature scent, or with anything about your order.' },
  ], action: 'contact' },
  'gift-sets': { eyebrow: 'Shop', title: 'Gift sets', blocks: [
    { p: 'Every Al-Hidayah fragrance makes a thoughtful gift. Order any two scents together and mention “gift” in your order note for complimentary gift wrapping and a handwritten card.' },
  ], action: 'shop' },
  'terms-of-service': { eyebrow: 'Legal', title: 'Terms of service', blocks: [
    { p: `By using this website and placing an order with ${brand.name} you agree to provide accurate contact and delivery details. Product images are representative; packaging may vary slightly.` },
    { p: 'Prices are shown in the store currency and may change without notice. An order is confirmed only once our team has verified it with you.' },
  ] },
  'privacy-policy': { eyebrow: 'Legal', title: 'Privacy policy', blocks: [
    { p: 'Your shopping bag is saved only in this browser so it is here when you return. This website does not use analytics or advertising cookies.' },
    { p: 'When you send an order by WhatsApp or email, we use your details only to confirm and deliver that order.' },
  ], action: 'cookies' },
  'refund-policy': { eyebrow: 'Legal', title: 'Refund policy', blocks: [
    { p: 'If an item is faulty or damaged in transit, we will replace it or refund it in full once the return is received. Refunds are issued to the original payment method.' },
  ], action: 'contact' },
  'cookie-settings': { eyebrow: 'Legal', title: 'Cookie settings', blocks: [
    { p: 'We only use essential browser storage to remember your shopping bag. No tracking cookies are set.' },
  ], action: 'cookies' },
  accessibility: { eyebrow: 'Legal', title: 'Accessibility', blocks: [
    { p: 'This website supports keyboard navigation, screen readers and reduced-motion preferences. If anything is hard to use, please tell us and we will fix it.' },
  ], action: 'contact' },
};
