import type { Metadata } from 'next';
import ContactPage from '@/components/contact-page';
import { brand } from '@/lib/brand';
export const metadata: Metadata = { title: `Contact us — ${brand.name}`, description: `Questions about an order, help choosing a scent or gifting? Contact the ${brand.name} team by email, phone or WhatsApp.` };
export default function Page() { return <ContactPage />; }
