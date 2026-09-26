import { brand } from '@/lib/brand';
export function Mark({ className = '' }: { className?: string }) { return <span className={`mark ${className}`} aria-hidden="true" />; }
export function Logo({ className = '' }: { className?: string }) { return <span className={`logo ${className}`}><Mark /><span className="logo-word">{brand.wordmark}</span></span>; }
