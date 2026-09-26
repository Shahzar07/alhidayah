import { brand } from './brand';

export type Family = 'Oud & woody' | 'Soft & floral' | 'Warm & spicy';
export type Notes = { top: string[]; middle: string[]; base: string[] };
export type Product = {
  id: string;
  name: string;
  mood: string;
  accords: string;
  family: Family;
  price: number;
  previous?: number;
  notes: Notes;
  description: string;
  story: string;
  tint: string;
  isNew?: boolean;
  bestSeller?: boolean;
  limited?: boolean;
};

// Scent notes, descriptions and prices are starter copy. Edit them here; every page reads from this list.
export const products: Product[] = [
  {
    id: 'rozta-ul-oud', name: 'Rozta-ul-Oud', mood: 'Regal, smoky, enveloping', accords: 'Agarwood, Saffron, Taif Rose', family: 'Oud & woody',
    price: 145, tint: '#e9dcc8', isNew: true, bestSeller: true,
    notes: { top: ['Saffron', 'Bergamot'], middle: ['Taif rose', 'Incense'], base: ['Oud', 'Amber', 'Musk'] },
    description: 'A garden of oud at dusk. Precious agarwood glows with saffron and softens into Taif rose, leaving a warm trail of amber that stays for hours.',
    story: 'Rozta means garden. We built this scent around the quiet ritual of evening bakhoor: smoke curling through rose, wood and warm stone.',
  },
  {
    id: 'no-56', name: 'No. 56', mood: 'Clean, confident, modern', accords: 'Bergamot, Vetiver, Cedar', family: 'Oud & woody',
    price: 120, tint: '#e4e1dc', bestSeller: true,
    notes: { top: ['Bergamot', 'Grapefruit'], middle: ['Lavender', 'Black pepper'], base: ['Vetiver', 'Cedar'] },
    description: 'Crisp and composed. Sparkling bergamot meets aromatic lavender and a dry, woody base of vetiver and cedar that feels effortlessly sharp.',
    story: 'Our most-worn signature. A clean woody fragrance that moves from the office to the evening without asking for attention, and gets it anyway.',
  },
  {
    id: 'fog-purple', name: 'Fog Purple', mood: 'Mysterious, velvety, magnetic', accords: 'Black Plum, Violet, Patchouli', family: 'Soft & floral',
    price: 130, tint: '#e6dde8', limited: true,
    notes: { top: ['Black plum', 'Pink pepper'], middle: ['Violet', 'Iris'], base: ['Patchouli', 'Vanilla'] },
    description: 'Velvet after dark. Juicy black plum and pink pepper drift into powdery violet and iris, resting on a smooth base of patchouli and vanilla.',
    story: 'Inspired by twilight haze over the city. Fog Purple is soft at first touch and deepens the longer you wear it.',
  },
  {
    id: 'floranza', name: 'Floranza', mood: 'Romantic, radiant, feminine', accords: 'Peony, Damask Rose, White Musk', family: 'Soft & floral',
    price: 125, tint: '#f1e1dd', isNew: true,
    notes: { top: ['Pear', 'Pink lychee'], middle: ['Peony', 'Damask rose'], base: ['White musk', 'Sandalwood'] },
    description: 'A bouquet in full bloom. Fresh pear and lychee open onto lush peony and Damask rose, settling into a soft veil of white musk and sandalwood.',
    story: 'A love letter to spring gardens. Floranza is light enough for every day and pretty enough for every celebration.',
  },
  {
    id: 'vortex', name: 'Vortex', mood: 'Bold, spicy, irresistible', accords: 'Red Berries, Cinnamon, Amber', family: 'Warm & spicy',
    price: 135, previous: 159, tint: '#efdcd3',
    notes: { top: ['Red berries', 'Mandarin'], middle: ['Cinnamon', 'Cardamom'], base: ['Amber', 'Tonka bean'] },
    description: 'Magnetic heat. Red berries and mandarin collide with cinnamon and cardamom, then melt into a sweet, lingering base of amber and tonka.',
    story: 'Made for the moment you walk into a room. Vortex pulls people in with warm spice and a trail that is hard to forget.',
  },
];

export const families: Family[] = ['Oud & woody', 'Soft & floral', 'Warm & spicy'];

export type Filter = { id: string; label: string; test: (p: Product) => boolean };
export const filters: Filter[] = [
  { id: 'all', label: 'All scents', test: () => true },
  ...families.map(f => ({ id: f.split(' ')[0].toLowerCase(), label: f, test: (p: Product) => p.family === f })),
  { id: 'new', label: 'New arrivals', test: p => !!p.isNew },
  { id: 'best', label: 'Best sellers', test: p => !!p.bestSeller },
  { id: 'limited', label: 'Limited edition', test: p => !!p.limited },
];

export const cutout = (p: Product) => `/images/products/${p.id}.webp`;
export const scene = (p: Product) => `/images/scenes/${p.id}.jpg`;
export const studio = (p: Product) => `/images/products/${p.id}-studio.webp`;
export const productUrl = (p: Product) => `/products/${p.id}/`;
export const findProduct = (id: string) => products.find(p => p.id === id);
export const money = (amount: number) => new Intl.NumberFormat(brand.locale, { style: 'currency', currency: brand.currency, maximumFractionDigits: 2 }).format(amount);
