import { Star, StarHalf } from 'lucide-react';
import { testimonials, type Testimonial } from '@/lib/testimonials';

function Stars({ rating }: { rating: number }) {
  return <div className="stars" aria-label={`Rated ${rating} out of 5`}>{[1, 2, 3, 4, 5].map(i => rating >= i ? <Star key={i} size={14} fill="currentColor" strokeWidth={0} /> : rating >= i - .5 ? <span key={i} className="half"><Star size={14} strokeWidth={1.4} /><StarHalf size={14} fill="currentColor" strokeWidth={0} /></span> : <Star key={i} size={14} strokeWidth={1.4} className="empty" />)}</div>;
}

function Card({ t }: { t: Testimonial }) {
  const initials = t.name.split(' ').map(w => w[0]).join('').slice(0, 2);
  return <figure className="testimonial">
    <blockquote><Stars rating={t.rating} /><p>“{t.quote}”</p></blockquote>
    <figcaption><span className="avatar" style={{ background: t.tone }}>{initials}</span><span><strong>{t.name}</strong><small>{t.role}</small></span></figcaption>
  </figure>;
}

export default function Testimonials() {
  const rows = [testimonials.slice(0, 4), testimonials.slice(4)];
  return <section className="testimonials" id="reviews" aria-labelledby="reviews-title">
    <div className="section-heading" data-reveal><span className="soft-pill">Testimonials</span><h2 id="reviews-title">What our customers say<br />about our products</h2></div>
    <div className="marquee-wrap">
      {rows.map((row, r) => <div className={`marquee ${r ? 'reverse' : ''}`} key={r}>
        <div className="marquee-track">{[...row, ...row].map((t, i) => <div key={i} aria-hidden={i >= row.length || undefined}><Card t={t} /></div>)}</div>
      </div>)}
    </div>
  </section>;
}
