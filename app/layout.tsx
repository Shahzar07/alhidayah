import type { Metadata } from 'next';
import './globals.css';
export const metadata: Metadata = { title: 'MOOKO — A scent of your own', description: 'Discover a world of expressive fragrances. Explore the MOOKO collection of warm, fresh and floral scents.', icons: { icon: '/favicon.svg' } };
export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) { return <html lang="en"><body>{children}</body></html>; }
