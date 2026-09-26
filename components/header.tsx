'use client';
import Link from 'next/link';
import { Menu, ShoppingBag } from 'lucide-react';
import { brand } from '@/lib/brand';
import { Logo } from './logo';
import { useStore } from './store';

export default function Header({ solid = false }: { solid?: boolean }) {
  const { count, openBag, openMenu } = useStore();
  return <header className={`header ${solid ? 'header-solid' : ''}`}>
    <button className="icon-button" aria-label="Open navigation" onClick={openMenu}><Menu size={19} /></button>
    <Link className="wordmark" href="/" aria-label={`${brand.name} home`}><Logo /></Link>
    <button className="icon-button bag-button" aria-label={`Open shopping bag, ${count} items`} onClick={openBag}><ShoppingBag size={18} />{count > 0 && <span className="bag-count">{count}</span>}</button>
  </header>;
}
