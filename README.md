# Al-Hidayah — premium fragrance storefront

A responsive storefront built in **Next.js 16 + React 19 + TypeScript**, with GSAP/ScrollTrigger motion, Lenis smooth scrolling, Radix accessible dialogs and Lucide icons. It exports as a static site.

## Start locally

Requires Node.js 20.9+ (Node.js 22 LTS recommended).

```bash
npm ci
npm run dev
```

Open http://localhost:3000.

## Production

```bash
npm run build
```

`npm run build` writes a ready-to-host static site to `out/`. Upload **the contents of out/** to the root of any static host (for example Hostinger `public_html`), or import the repository into Vercel as a Next.js project. For a local production preview use `npx serve out`. Set `NEXT_PUBLIC_SITE_URL` to your domain so social-share images resolve correctly.

## Pages

- **Home** (`/`): amber hero, three scent collections, ingredients, shop with filters (family, new arrivals, best sellers, limited edition) and note search, testimonials and the brand footer.
- **Product pages** (`/products/<id>/`): one static page per fragrance with Prev/Next navigation (and ← → keys), note table, quantity, "I want this" add-to-bag, a quick switcher for every scent, story and wear tips.
- **Contact** (`/contact/`): email, phone, WhatsApp, support line, opening hours and socials, plus a validated contact form. The form sends through WhatsApp or the customer's email app; set `formEndpoint` in `lib/brand.ts` (for example a Formspree URL) to have it post directly instead.
- **Bag**: shared across pages and saved in the browser. Checkout sends the full order to the store on **WhatsApp** or by **email**; the team then confirms delivery and payment with the customer.
- **Footer**: every link works. Shop links filter the catalogue, Contact Support opens the contact page, the other customer care, discover and legal links open content dialogs (FAQ, order tracking request, policies), and Connect opens the social profiles.

## Customize

- Brand name, contact email, phone, support line, opening hours, contact form endpoint, social links and currency: `lib/brand.ts`.
- Products (names, prices, notes, descriptions, badges, backdrop tint): `lib/products.ts`.
- Footer dialog content and policies: `lib/info.ts`.
- Testimonials: `lib/testimonials.ts`.
- Layout and styles: `components/` and `app/globals.css`.
- Motion: `components/motion.tsx`.

## Images

Photography was generated with Higgsfield from the real product photos, so every label matches the actual bottles.

- `public/images/hero.jpg`: desktop and tablet hero (Rozta-ul-Oud on travertine with amber silk). `hero-mobile.jpg` is a portrait crop centred on the bottle, served to phones.
- `public/images/collections/`: the three collection cards (oud, floral, spicy).
- `public/images/scenes/<id>.jpg`: one lifestyle shot per fragrance, used on the product page, on card hover and for share previews.
- `public/images/products/<id>.webp`: transparent cut-outs of the original product photos, used on the shop cards, bag and product switcher.
- `public/images/brand/mark.png`, `lockup.png`: the Al-Hidayah calligraphy logo extracted from the label, used as a CSS mask so it can take any colour.
- `public/images/botanicals.jpg`: ingredient photography (3×2 sheet).

To add a product, add its cut-out to `public/images/products/`, a lifestyle shot to `public/images/scenes/`, and an entry to `lib/products.ts`; its page is generated automatically.

## Before launch

Replace the sample contact details and social links in `lib/brand.ts`, confirm prices and notes in `lib/products.ts`, review the policy wording in `lib/info.ts`, and replace the sample testimonials in `lib/testimonials.ts` with real customer reviews. No payment gateway is connected; orders are placed through WhatsApp or email.
