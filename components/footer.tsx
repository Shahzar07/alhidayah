'use client';
import Link from 'next/link';
import { Headset, Mail, Phone } from 'lucide-react';
import { brand } from '@/lib/brand';
import { useStore } from './store';

type Item = { label: string; href?: string; topic?: string; external?: boolean };
const columns: [string, Item[]][] = [
  ['Shop', [{ label: 'New Arrivals', href: '/?filter=new#shop' }, { label: 'Best Sellers', href: '/?filter=best#shop' }, { label: 'Gift Sets', topic: 'gift-sets' }, { label: 'Limited Edition', href: '/?filter=limited#shop' }, { label: 'All Collections', href: '/?filter=all#shop' }]],
  ['Customer Care', [{ label: 'Shipping & Delivery', topic: 'shipping-delivery' }, { label: 'Returns & Exchanges', topic: 'returns-exchanges' }, { label: 'FAQ', topic: 'faq' }, { label: 'Track Order', topic: 'track-order' }, { label: 'Contact Support', topic: 'contact-support' }]],
  ['Discover', [{ label: 'Our Story', topic: 'our-story' }, { label: 'Store Locator', topic: 'store-locator' }, { label: 'Ingredients & Ethics', topic: 'ingredients-ethics' }, { label: 'Scent Guide', topic: 'scent-guide' }, { label: 'Journal & Tips', topic: 'journal-tips' }]],
  ['Legal', [{ label: 'Terms of Service', topic: 'terms-of-service' }, { label: 'Privacy Policy', topic: 'privacy-policy' }, { label: 'Refund Policy', topic: 'refund-policy' }, { label: 'Cookie Settings', topic: 'cookie-settings' }, { label: 'Accessibility', topic: 'accessibility' }]],
  ['Connect', brand.socials.map(s => ({ label: s.label, href: s.href, external: true }))],
];

export default function Footer() {
  const { openInfo } = useStore();
  const digits = brand.phone.replace(/\D/g, '');
  return <footer className="site-footer">
    <div className="footer-glow">
      <div className="footer-inner">
        <div className="footer-top">
          <h2 data-reveal>{brand.name} is where the<br />fragrance is found.</h2>
          <ul className="footer-contact">
            <li><Mail size={14} /><a href={`mailto:${brand.email}`}>{brand.email}</a></li>
            <li><Phone size={14} /><a href={`tel:${digits}`}>{brand.phone}</a></li>
            <li><Headset size={14} /><button onClick={() => openInfo('contact-support')}>{brand.support}</button></li>
          </ul>
        </div>
        <nav className="footer-columns" aria-label="Footer">
          {columns.map(([title, items]) => <div key={title}><h3>{title}</h3><ul>{items.map(item => <li key={item.label}>
            {item.topic ? <button onClick={() => openInfo(item.topic!)}>{item.label}</button>
              : item.external ? <a href={item.href} target="_blank" rel="noreferrer">{item.label}</a>
              : <Link href={item.href!} scroll={false}>{item.label}</Link>}
          </li>)}</ul></div>)}
        </nav>
      </div>
      <p className="footer-copy">© {new Date().getFullYear()} All rights reserved by {brand.name}</p>
    </div>
    <div className="footer-giant" aria-hidden="true"><svg viewBox="0 0 1000 138" preserveAspectRatio="xMidYMax meet"><text x="0" y="146" textLength="1000" lengthAdjust="spacing">{brand.wordmark}</text></svg></div>
  </footer>;
}
