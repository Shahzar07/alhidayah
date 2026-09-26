'use client';
import { useEffect, useState } from 'react';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';
import { ArrowDown, ArrowUpRight, FlaskConical, Plus, Search } from 'lucide-react';
import { brand } from '@/lib/brand';
import { families, filters, findProduct, money, products, productUrl, scene, type Family } from '@/lib/products';
import { Bottle } from './bottle';
import Footer from './footer';
import Header from './header';
import Motion from './motion';
import { useStore } from './store';
import Testimonials from './testimonials';

const featured = findProduct('rozta-ul-oud')!;
const collectionCopy: Record<Family, { tag: string; image: string; alt: string }> = {
  'Oud & woody': { tag: 'Rich & regal', image: 'oud', alt: 'Rozta-ul-Oud and No. 56 beside burning bakhoor and agarwood' },
  'Soft & floral': { tag: 'Soft & expressive', image: 'floral', alt: 'Floranza and Fog Purple among peonies and lavender' },
  'Warm & spicy': { tag: 'Bold & magnetic', image: 'spicy', alt: 'Vortex with cinnamon, star anise and dried rose petals' },
};

export default function Home() {
  const { add, openInfo } = useStore();
  const params = useSearchParams();
  const [filter, setFilter] = useState('all'), [search, setSearch] = useState('');

  useEffect(() => {
    const f = params.get('filter');
    if (f && filters.some(x => x.id === f)) { setFilter(f); setSearch(''); requestAnimationFrame(() => document.getElementById('shop')?.scrollIntoView({ behavior: 'smooth' })); }
  }, [params]);

  // Arriving from another page with a hash (e.g. /#reviews): wait for layout, then jump to the section.
  useEffect(() => {
    const go = () => { const id = decodeURIComponent(window.location.hash.slice(1)); const el = id && document.getElementById(id); if (el) window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY, behavior: 'instant' }); };
    const t = window.setTimeout(go, 80);
    window.addEventListener('hashchange', go);
    return () => { clearTimeout(t); window.removeEventListener('hashchange', go); };
  }, []);

  const chooseFamily = (f: Family) => { setFilter(filters.find(x => x.label === f)!.id); setSearch(''); document.getElementById('shop')?.scrollIntoView({ behavior: 'smooth' }); };
  const active = filters.find(f => f.id === filter)!;
  const q = search.trim().toLowerCase();
  const visible = products.filter(p => active.test(p) && `${p.name} ${p.accords} ${p.family} ${Object.values(p.notes).flat().join(' ')}`.toLowerCase().includes(q));

  return <><Motion /><a className="skip-link" href="#shop">Skip to products</a>
    <div className="announcement"><FlaskConical size={12} /><span>A little luxury. A lasting impression.</span><button onClick={() => openInfo('our-story')}>Discover {brand.name} <ArrowUpRight size={12} /></button></div>
    <main>
      <section className="hero" id="home">
        <picture><source media="(max-width: 640px)" srcSet="/images/hero-mobile.jpg" /><img className="hero-photo" src="/images/hero.jpg" alt={`${brand.name} Rozta-ul-Oud eau de parfum on warm travertine with amber silk`} fetchPriority="high" /></picture>
        <Header />
        <div className="hero-copy"><h1>Amazing scent that<br />reflects <span>character</span></h1><p>{brand.name} delivers distinctive fragrances with an elegant,<br className="desktop-br" /> modern touch — made for those who stand out.</p><div className="actions"><a className="button black" href="#collections">Discover collection <ArrowUpRight size={15} /></a><Link className="button white" href="/contact/">Contact us</Link></div></div>
        <Link className="hero-product" href={productUrl(featured)}><span className="pill">• New</span><strong>{featured.name}</strong><span>{featured.mood}</span><b>{money(featured.price)}</b><ArrowUpRight className="product-arrow" size={18} /></Link>
        <a className="hero-scroll" href="#collections" aria-label="Explore collections"><ArrowDown size={18} /></a>
      </section>

      <section className="collections section" id="collections">
        <div className="section-heading" data-reveal><span className="eyebrow">Our collections</span><h2>Discover the fragrance with<br />an unlimited collection</h2></div>
        <div className="collection-grid">{families.map(f => { const c = collectionCopy[f]; return <button className="collection-card" key={f} data-reveal onClick={() => chooseFamily(f)}>
          <img className="collection-visual" src={`/images/collections/${c.image}.jpg`} alt={c.alt} loading="lazy" decoding="async" />
          <span className="collection-tag">{c.tag}</span>
          <div className="collection-caption"><div><span>THE COLLECTION</span><h3>{f}</h3></div><span className="round-arrow"><ArrowUpRight size={22} /></span></div>
        </button>; })}</div>
      </section>

      <section className="ingredients" id="story"><div className="botanicals" aria-hidden="true">{['Neroli', 'Lemon', 'Star anise', 'Rose', 'Lavender', 'Jasmine'].map((n, i) => <div key={n} className={`botanical botanical-${i}`} style={{ backgroundPosition: `${(i % 3) * 50}% ${Math.floor(i / 3) * 100}%` }} />)}</div><div className="ingredients-copy" data-reveal><span className="outline-pill">Soulful creations. Beautiful ingredients.</span><h2>Only high-quality<br />perfume ingredients</h2><p>We create perfumes that can be enjoyed to the fullest,<br className="desktop-br" /> using ingredients whose quality is beyond doubt.</p><button className="button white" onClick={() => openInfo('our-story')}>View our story <ArrowUpRight size={14} /></button></div></section>

      <section className="products section" id="shop">
        <div className="section-heading" data-reveal><span className="eyebrow">Our products</span><h2>We know you love lots of<br />scents, discover them now</h2></div>
        <div className="shop-tools"><div className="filters" aria-label="Filter fragrances">{filters.map(f => <button key={f.id} aria-pressed={filter === f.id} onClick={() => setFilter(f.id)} className={filter === f.id ? 'active' : ''}>{f.label}</button>)}</div><label className="search"><Search size={16} /><input value={search} onChange={e => setSearch(e.target.value)} placeholder="Find your scent" aria-label="Search fragrances" /></label></div>
        <div className="product-grid">
          {visible.map(p => <article className="product-card" key={p.id}>
            <Link className="product-image-button" href={productUrl(p)} aria-label={`View ${p.name}`}><Bottle product={p} /><img className="card-scene" src={scene(p)} alt="" loading="lazy" decoding="async" />{p.isNew && <span className="pill product-badge">• New</span>}{p.limited && <span className="pill product-badge">• Limited</span>}{p.previous && <span className="discount">{Math.round((1 - p.price / p.previous) * 100)}% OFF</span>}<span className="quick-view">Discover scent <ArrowUpRight size={18} /></span></Link>
            <div className="product-details"><Link href={productUrl(p)}><h3>{p.name}</h3></Link><p>{p.mood}</p><div className="price">{money(p.price)} {p.previous && <del>{money(p.previous)}</del>}</div><button className="quick-add icon-button" aria-label={`Add ${p.name} to bag`} onClick={() => add(p)}><Plus size={18} /></button></div>
          </article>)}
          {filter === 'all' && !q && <button className="guide-card" onClick={() => openInfo('scent-guide')}><span className="eyebrow">Scent guide</span><strong>Not sure where<br />to begin?</strong><p>Find the {brand.name} fragrance that matches your mood in under a minute.</p><span className="round-arrow"><ArrowUpRight size={20} /></span></button>}
        </div>
        {visible.length === 0 && <div className="empty-state"><h3>No scents found.</h3><p>Try a different name or note, or explore all our fragrances.</p><button className="button black" onClick={() => { setSearch(''); setFilter('all'); }}>View all scents</button></div>}
      </section>

      <Testimonials />

      <section className="closing" data-reveal><div><span className="eyebrow">A SCENT OF YOUR OWN</span><h2>Leave a little<br />of <span>yourself</span> everywhere.</h2></div><a className="button black" href="#shop">Find your signature <ArrowUpRight size={16} /></a></section>
    </main>
    <Footer />
  </>;
}
