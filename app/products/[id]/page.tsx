import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import ProductPage from '@/components/product-page';
import { brand } from '@/lib/brand';
import { findProduct, products, studio } from '@/lib/products';

type Props = { params: Promise<{ id: string }> };
export const dynamicParams = false;
export function generateStaticParams() { return products.map(p => ({ id: p.id })); }
export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const p = findProduct((await params).id);
  if (!p) return {};
  return { title: `${p.name} — ${brand.name}`, description: p.description, openGraph: { title: `${p.name} — ${brand.name}`, description: p.description, images: [studio(p)] } };
}
export default async function Page({ params }: Props) {
  const p = findProduct((await params).id);
  if (!p) notFound();
  return <ProductPage product={p} />;
}
