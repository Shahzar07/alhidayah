import type { Metadata, Viewport } from 'next';
import StoreProvider from '@/components/store';
import { brand } from '@/lib/brand';
import './globals.css';
export const metadata: Metadata = { metadataBase: new URL(process.env.NEXT_PUBLIC_SITE_URL || 'http://localhost:3000'), title: `${brand.name} — A scent of your own`, description: `Discover a world of expressive fragrances. Explore the ${brand.name} collection of oud, floral and warm spicy eau de parfums.`, icons: { icon: '/favicon.png', apple: '/apple-touch-icon.png' }, openGraph: { title: `${brand.name} — A scent of your own`, images: ['/images/hero.jpg'] } };
export const viewport: Viewport = { themeColor: '#151513' };
export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) { return <html lang="en"><body><StoreProvider>{children}</StoreProvider></body></html>; }
