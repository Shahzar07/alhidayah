'use client';
import { useEffect, useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { ArrowLeft, ArrowRight, ArrowUpRight, Minus, Plus, ShoppingBag } from 'lucide-react';
import { money, products, productUrl, type Product } from '@/lib/products';
import { Bottle } from './bottle';
import Footer from './footer';
import Header from './header';
import Motion from './motion';
import { useStore } from './store';
import Testimonials from './testimonials';

export default function ProductPage({ product: p }: { product: Product }) {
  const { add } = useStore();
  const router = useRouter();
  const [quantity, setQuantity] = useState(1);
  const index = products.findIndex(x => x.id === p.id);
  const prev = products[(index - 1 + products.length) % products.length], next = products[(index + 1) % products.length];

  useEffect(() => {
    products.forEach(x => router.prefetch(productUrl(x)));
    const onKey = (e: KeyboardEvent) => {
      if (e.target instanceof HTMLElement && e.target.closest('input, textarea, [role="dialog"]')) return;
      if (e.key === 'ArrowLeft') router.push(productUrl(prev), { scroll: false });
      if (e.key === 'ArrowRight') router.push(productUrl(next), { scroll: false });
    };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [router, prev, next]);

  return <><Motion />
    <div className="page-shell"><Header solid /></div>
    <main>
      <section className="product-hero" aria-labelledby="product-name">
        <div className="product-hero-head">
          <div className="reveal-in"><span className="soft-pill">Our products</span><h1>Discover our best collection,<br />in other words, many fans</h1><p>Five signature eau de parfums, each composed around a single feeling.<br className="desktop-br" /> Explore the scent that was made to become yours.</p></div>
          <div className="pager reveal-in">
            <span className="pager-count">{String(index + 1).padStart(2, '0')} / {String(products.length).padStart(2, '0')}</span>
            <Link className="pager-button" href={productUrl(prev)} scroll={false} aria-label={`Previous: ${prev.name}`}><ArrowLeft size={14} /> Prev</Link>
            <Link className="pager-button" href={productUrl(next)} scroll={false} aria-label={`Next: ${next.name}`}>Next <ArrowRight size={14} /></Link>
          </div>
        </div>

        <article className="product-sheet reveal-in" key={p.id}>
          <div className="product-sheet-media"><Bottle product={p} size="stage" priority />{p.isNew && <span className="pill product-badge">• New</span>}{p.limited && <span className="pill product-badge">• Limited edition</span>}{p.previous && <span className="discount">{Math.round((1 - p.price / p.previous) * 100)}% OFF</span>}</div>
          <div className="product-sheet-copy">
            <div><span className="sheet-family">{p.family} · Eau de parfum 50 ml</span><h2 id="product-name">{p.name}</h2><p className="sheet-accords">{p.accords}</p></div>
            <div className="sheet-bottom">
              <p className="sheet-description">{p.description}</p>
              <dl className="note-table">
                <div><dt>Base note</dt><dd>{p.notes.base.join(', ')}</dd></div>
                <div><dt>Middle note</dt><dd>{p.notes.middle.join(', ')}</dd></div>
                <div><dt>Top note</dt><dd>{p.notes.top.join(', ')}</dd></div>
              </dl>
              <div className="sheet-buy">
                <div className="sheet-price"><strong>{money(p.price * quantity)}</strong>{p.previous && <del>{money(p.previous * quantity)}</del>}</div>
                <div className="sheet-actions">
                  <div className="quantity" aria-label="Quantity"><button aria-label="Decrease quantity" disabled={quantity <= 1} onClick={() => setQuantity(q => q - 1)}><Minus size={13} /></button><span aria-live="polite">{quantity}</span><button aria-label="Increase quantity" disabled={quantity >= 20} onClick={() => setQuantity(q => q + 1)}><Plus size={13} /></button></div>
                  <button className="button black want" onClick={() => { add(p, quantity); setQuantity(1); }}><ShoppingBag size={15} /> I want this</button>
                </div>
              </div>
            </div>
          </div>
        </article>

        <div className="sheet-thumbs" aria-label="All fragrances">{products.map(x => <Link key={x.id} href={productUrl(x)} scroll={false} className={x.id === p.id ? 'active' : ''} aria-current={x.id === p.id ? 'page' : undefined}><Bottle product={x} size="thumb" /><span>{x.name}</span></Link>)}</div>
      </section>

      <section className="product-story section" data-reveal>
        <div><span className="eyebrow">The story</span><h2>{p.story}</h2></div>
        <div className="wear-tips"><div><small>01</small><strong>Where to apply</strong><p>Pulse points: wrists, neck and behind the ears. Let it settle, don’t rub.</p></div><div><small>02</small><strong>When to wear</strong><p>{p.family === 'Soft & floral' ? 'Daytime, celebrations and warm evenings.' : p.family === 'Warm & spicy' ? 'Evenings out and cooler days.' : 'Any time you want to leave an impression.'}</p></div><div><small>03</small><strong>Longevity</strong><p>6–10 hours on skin, longer on fabric.</p></div></div>
      </section>

      <Testimonials />
      <section className="closing" data-reveal><div><span className="eyebrow">Keep exploring</span><h2>Find the scent that<br />feels like <span>you</span>.</h2></div><Link className="button black" href="/#shop">View all fragrances <ArrowUpRight size={16} /></Link></section>
    </main>
    <Footer />
  </>;
}
