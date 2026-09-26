# MOOKO — premium fragrance storefront

A complete responsive landing page built in **Next.js 16 + React 19 + TypeScript**, with GSAP/ScrollTrigger motion, Lenis smooth scrolling, Radix accessible dialogs and Lucide icons. This is a React application, not a standalone HTML template. Styling is in the Next.js global stylesheet.

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

The included `out/` folder is a ready-to-host static export. Upload **the contents of out/** to the root of a static host or Hostinger public_html. Do not open index.html directly via file://. Alternatively import the source into Vercel as a Next.js project. No API keys or external image services are required.

`next start` does not serve static-export projects. For a local production preview, use `npx serve out`.

## Included

- Reference-led amber hero, rounded navigation, three collection cards, dark botanical ingredients section and fragrance catalog.
- Nine editable sample products, scent-family filters and note/name search.
- Product quick-view dialogs, three bottle sizes, price updates, add-to-bag, quantity changes, item removal and browser-local bag persistence.
- Responsive mobile layout, keyboard-accessible menus/dialogs, focus management, Escape close and reduced-motion support.
- All generated images and self-hosted Manrope font files, including font license.

## Customize

- Product names, prices, descriptions and notes: `lib/products.ts`.
- Page sections, brand text, dialogs, bag and menu: `components/storefront.tsx`.
- Typography, colors, layouts and responsive rules: `app/globals.css`.
- Animation: `components/motion.tsx`.
- SEO/title/favicon: `app/layout.tsx`, `public/favicon.svg`.
- Images: `public/images/`. The catalog is a 3×3 image sheet; product `cell` indexes range from 0–8. Botanicals use a 3×2 sheet.

## Reference fidelity and assets

The supplied screenshot guides the composition, amber palette, product presentation, headline style and section hierarchy. Original high-resolution photography and the exact source font were not supplied. Images were recreated with the built-in image generation tool; Manrope is a close visual font match, not a verified identification. Product information and pricing are sample content, not verified merchant inventory. The matching screenshot wording “parfume ingredients” is preserved.

Asset briefs: hero — amber rectangular MOOKO bottle, gold cap, sandstone and orange silk with left-side negative space; catalog — nine reference-inspired perfume product photos in a 3×3 grid; botanicals — neroli, lemon, star anise, rose, lavender and jasmine in a 3×2 grid.

## Commerce status

This deliverable is the storefront frontend. Bag and order summary work locally. **No payment gateway, Shopify backend, inventory service or order fulfillment is connected.** Review order clearly explains that no order is placed and no payment is collected. Before commercial launch, connect a commerce provider, replace sample data, and add real contact, shipping, return and privacy information. No account credentials or secrets are included.
