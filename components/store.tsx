'use client';
import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import Link from 'next/link';
import * as Dialog from '@radix-ui/react-dialog';
import { ArrowRight, ArrowUpRight, Check, Mail, MessageCircle, Minus, Phone, Plus, ShoppingBag, Trash2, X } from 'lucide-react';
import { brand } from '@/lib/brand';
import { info } from '@/lib/info';
import { findProduct, money, products, productUrl, type Product } from '@/lib/products';
import { Bottle } from './bottle';
import { Logo } from './logo';

type CartLine = { id: string; quantity: number };
type Store = { count: number; add: (p: Product, quantity?: number) => void; openBag: () => void; openMenu: () => void; openInfo: (topic: string) => void };
const StoreContext = createContext<Store | null>(null);
export const useStore = () => { const s = useContext(StoreContext); if (!s) throw new Error('useStore must be used inside StoreProvider'); return s; };
const MAX = 20;

export function Modal({ open, onOpenChange, title, children, kind = 'modal' }: { open: boolean; onOpenChange: (v: boolean) => void; title: string; children: React.ReactNode; kind?: string }) {
  return <Dialog.Root open={open} onOpenChange={onOpenChange}><Dialog.Portal><Dialog.Overlay className="overlay" /><Dialog.Content className={kind} data-lenis-prevent aria-describedby={undefined}><Dialog.Title className="sr-only">{title}</Dialog.Title><Dialog.Close className="icon-button close" aria-label="Close"><X size={21} /></Dialog.Close>{children}</Dialog.Content></Dialog.Portal></Dialog.Root>;
}

const digits = (s: string) => s.replace(/\D/g, '');

export default function StoreProvider({ children }: { children: React.ReactNode }) {
  const [cart, setCart] = useState<CartLine[]>([]), [loaded, setLoaded] = useState(false);
  const [bag, setBag] = useState(false), [menu, setMenu] = useState(false), [topic, setTopic] = useState<string | null>(null);
  const [review, setReview] = useState(false), [note, setNote] = useState(''), [toast, setToast] = useState(''), [order, setOrder] = useState('');

  useEffect(() => {
    try {
      const saved = JSON.parse(localStorage.getItem(brand.storageKey) || '[]');
      if (Array.isArray(saved)) setCart(saved.filter((l: CartLine) => findProduct(l.id) && Number.isInteger(l.quantity) && l.quantity > 0).map((l: CartLine) => ({ id: l.id, quantity: Math.min(l.quantity, MAX) })));
    } catch {}
    setLoaded(true);
  }, []);
  useEffect(() => { if (loaded) try { localStorage.setItem(brand.storageKey, JSON.stringify(cart)); } catch {} }, [cart, loaded]);
  useEffect(() => { if (!toast) return; const t = setTimeout(() => setToast(''), 2800); return () => clearTimeout(t); }, [toast]);

  const count = cart.reduce((s, l) => s + l.quantity, 0);
  const lines = cart.map(l => ({ ...l, product: findProduct(l.id)! }));
  const total = lines.reduce((s, l) => s + l.product.price * l.quantity, 0);

  const add = useCallback((p: Product, quantity = 1) => {
    setCart(c => c.some(l => l.id === p.id) ? c.map(l => l.id === p.id ? { ...l, quantity: Math.min(MAX, l.quantity + quantity) } : l) : [...c, { id: p.id, quantity: Math.min(MAX, quantity) }]);
    setToast(`${p.name} added to your bag`);
  }, []);
  const changeQty = (id: string, d: number) => setCart(c => c.map(l => l.id === id ? { ...l, quantity: Math.min(MAX, l.quantity + d) } : l).filter(l => l.quantity > 0));
  const openBag = useCallback(() => { setBag(true); setReview(false); }, []);
  const openMenu = useCallback(() => setMenu(true), []);
  const openInfo = useCallback((t: string) => setTopic(t), []);
  const value = useMemo(() => ({ count, add, openBag, openMenu, openInfo }), [count, add, openBag, openMenu, openInfo]);

  const orderText = [`Hello ${brand.name}, I would like to order:`, ...lines.map(l => `• ${l.product.name} (50 ml) × ${l.quantity} — ${money(l.product.price * l.quantity)}`), `Subtotal: ${money(total)}`, note && `Note: ${note}`].filter(Boolean).join('\n');
  const page = topic ? info[topic] : null;

  return <StoreContext.Provider value={value}>
    {children}

    <Modal open={menu} onOpenChange={setMenu} title="Navigation" kind="drawer menu-drawer">
      <Link className="menu-logo" href="/" onClick={() => setMenu(false)}><Logo /></Link>
      <nav>{[['The collection', '/#collections'], ['Shop fragrances', '/#shop'], ['Our ingredients', '/#story'], ['Customer reviews', '/#reviews']].map(([label, url], i) => <Link href={url} onClick={() => setMenu(false)} key={url}><small>0{i + 1}</small>{label}<ArrowUpRight /></Link>)}</nav>
      <div className="menu-products">{products.map(p => <Link key={p.id} href={productUrl(p)} onClick={() => setMenu(false)}><Bottle product={p} size="thumb" /><span>{p.name}</span></Link>)}</div>
      <p>{brand.tagline}.</p>
    </Modal>

    <Modal open={bag} onOpenChange={setBag} title="Your shopping bag" kind="drawer bag-drawer">
      <h2>{review ? 'Place your order' : 'Your shopping bag'} <span>({count})</span></h2>
      {lines.length ? <>
        <div className="bag-lines">{lines.map(({ product: p, quantity }) => <div className="bag-line" key={p.id}>
          <Link href={productUrl(p)} onClick={() => setBag(false)} aria-label={`View ${p.name}`}><Bottle product={p} size="thumb" /></Link>
          <div><h3>{p.name}</h3><p>Eau de parfum · 50 ml</p><strong>{money(p.price)}</strong>
            <div className="quantity"><button aria-label={`Decrease ${p.name} quantity`} onClick={() => changeQty(p.id, -1)}><Minus size={13} /></button><span>{quantity}</span><button disabled={quantity >= MAX} aria-label={`Increase ${p.name} quantity`} onClick={() => changeQty(p.id, 1)}><Plus size={13} /></button></div>
          </div>
          <button className="remove" aria-label={`Remove ${p.name}`} onClick={() => setCart(c => c.filter(l => l.id !== p.id))}><Trash2 size={16} /></button>
        </div>)}</div>
        <div className="bag-summary">
          <div><span>Subtotal</span><strong>{money(total)}</strong></div>
          <p>Delivery charges are confirmed with you when your order is placed.</p>
          {review ? <div className="checkout-notice">
            <label className="order-note">Order note (optional)<textarea value={note} onChange={e => setNote(e.target.value)} placeholder="Delivery city, gift message…" rows={2} /></label>
            <p>Send your order and our team will confirm delivery and payment with you directly.</p>
            <a className="button black" href={`https://wa.me/${digits(brand.phone)}?text=${encodeURIComponent(orderText)}`} target="_blank" rel="noreferrer"><MessageCircle size={16} /> Order on WhatsApp</a>
            <a className="button outline" href={`mailto:${brand.email}?subject=${encodeURIComponent(`New order — ${count} item${count > 1 ? 's' : ''}`)}&body=${encodeURIComponent(orderText)}`}><Mail size={16} /> Order by email</a>
            <button className="text-button" onClick={() => setReview(false)}>Back to bag</button>
          </div> : <button className="button black" onClick={() => setReview(true)}>Checkout <ArrowRight size={17} /></button>}
        </div>
      </> : <div className="empty-bag"><ShoppingBag size={40} strokeWidth={1} /><h3>Your next signature awaits.</h3><p>Discover a fragrance that feels like you.</p><Link className="button black" href="/#shop" onClick={() => setBag(false)}>Explore fragrances</Link></div>}
    </Modal>

    <Modal open={!!page} onOpenChange={v => { if (!v) setTopic(null); }} title={page?.title || brand.name}>
      {page && <div className="info-copy"><span className="eyebrow">{page.eyebrow}</span><h2>{page.title}</h2>
        {page.blocks.map((b, i) => b.p ? <p key={i}>{b.p}</p> : b.list ? <ul key={i} className="story-notes">{b.list.map(x => <li key={x}>{x}</li>)}</ul> : b.faq ? <div key={i} className="faq">{b.faq.map(([q, a]) => <details key={q}><summary>{q}<Plus size={16} /></summary><p>{a}</p></details>)}</div> : null)}
        {page.action === 'shop' && <Link className="button black" href="/#shop" onClick={() => setTopic(null)}>Explore the collection <ArrowUpRight size={15} /></Link>}
        {page.action === 'contact' && <div className="contact-list"><a href={`mailto:${brand.email}`}><Mail size={16} />{brand.email}</a><a href={`tel:${digits(brand.phone)}`}><Phone size={16} />{brand.phone}</a><a href={`https://wa.me/${digits(brand.phone)}`} target="_blank" rel="noreferrer"><MessageCircle size={16} />Chat on WhatsApp</a></div>}
        {page.action === 'track' && <form className="track-form" onSubmit={e => { e.preventDefault(); if (order.trim()) window.location.href = `mailto:${brand.email}?subject=${encodeURIComponent(`Order status: ${order.trim()}`)}&body=${encodeURIComponent(`Hello, could you share the status of order ${order.trim()}? Thank you.`)}`; }}><input value={order} onChange={e => setOrder(e.target.value)} placeholder="Order number" aria-label="Order number" required /><button className="button black" type="submit">Request status <ArrowRight size={15} /></button></form>}
        {page.action === 'cookies' && <button className="button black" onClick={() => { setCart([]); try { localStorage.removeItem(brand.storageKey); } catch {} setToast('Your saved shopping bag has been cleared'); }}>Clear saved data</button>}
      </div>}
    </Modal>

    <div className={`toast ${toast ? 'show' : ''}`} role="status" aria-live="polite">{toast && <><Check size={17} />{toast}<button onClick={() => { openBag(); setToast(''); }}>View bag</button></>}</div>
  </StoreContext.Provider>;
}
