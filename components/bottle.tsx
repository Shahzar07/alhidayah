import { cutout, type Product } from '@/lib/products';
// The real product photo, cut out of its white studio background and set on the scent's tint.
export function Bottle({ product, size = 'card', priority = false }: { product: Product; size?: 'card' | 'thumb' | 'stage'; priority?: boolean }) {
  return <div className={`bottle bottle-${size}`} style={{ '--tint': product.tint } as React.CSSProperties}>
    <img src={cutout(product)} alt={`${product.name} eau de parfum, 50 ml`} loading={priority ? 'eager' : 'lazy'} decoding="async" draggable={false} />
  </div>;
}
