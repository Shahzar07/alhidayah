// Store-wide brand details. Replace the contact and social values with the real ones before launch.
export const brand = {
  name: 'Al-Hidayah',
  wordmark: 'AL-HIDAYAH',
  arabic: 'الهدايه',
  tagline: 'A scent of guidance',
  email: 'care@alhidayah.com',
  phone: '+92 300 000 0000',
  support: '0800-HIDAYAH',
  hours: 'Mon – Sat, 10:00 – 20:00',
  // Optional: a form service URL (for example https://formspree.io/f/xxxx). When set, the contact form posts
  // there directly; when empty, it opens the customer's email app or WhatsApp with the message filled in.
  formEndpoint: '',
  currency: 'USD',
  locale: 'en-US',
  storageKey: 'alhidayah-bag',
  socials: [
    { label: 'X (Twitter)', href: 'https://x.com' },
    { label: 'Instagram', href: 'https://instagram.com' },
    { label: 'Facebook', href: 'https://facebook.com' },
    { label: 'Telegram', href: 'https://telegram.org' },
    { label: 'Tik Tok', href: 'https://tiktok.com' },
  ],
} as const;
