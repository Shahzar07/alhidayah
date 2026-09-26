'use client';
import { useState } from 'react';
import Link from 'next/link';
import { ArrowUpRight, Check, Clock, Headset, Mail, MessageCircle, Phone, Plus, Send } from 'lucide-react';
import { brand } from '@/lib/brand';
import { info } from '@/lib/info';
import Footer from './footer';
import Header from './header';
import Motion from './motion';

const topics = ['Order enquiry', 'Product advice', 'Wholesale & gifting', 'Feedback', 'Other'];
const digits = (s: string) => s.replace(/\D/g, '');
type Fields = { name: string; email: string; phone: string; topic: string; order: string; message: string };
const empty: Fields = { name: '', email: '', phone: '', topic: topics[0], order: '', message: '' };

export default function ContactPage() {
  const [f, setF] = useState<Fields>(empty);
  const [errors, setErrors] = useState<Partial<Record<keyof Fields, string>>>({});
  const [state, setState] = useState<'idle' | 'sending' | 'sent' | 'handoff' | 'failed'>('idle');
  const set = (k: keyof Fields) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => { setF(v => ({ ...v, [k]: e.target.value })); setErrors(v => ({ ...v, [k]: undefined })); };

  const validate = () => {
    const e: typeof errors = {};
    if (f.name.trim().length < 2) e.name = 'Please enter your name.';
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email.trim())) e.email = 'Please enter a valid email address.';
    if (f.phone && digits(f.phone).length < 7) e.phone = 'Please enter a valid phone number.';
    if (f.message.trim().length < 10) e.message = 'Please write a little more (at least 10 characters).';
    setErrors(e);
    if (Object.keys(e).length) document.getElementById(`contact-${Object.keys(e)[0]}`)?.focus();
    return !Object.keys(e).length;
  };
  const text = () => [
    [`Topic: ${f.topic}`, f.topic === 'Order enquiry' && f.order.trim() ? `Order number: ${f.order.trim()}` : ''].filter(Boolean).join('\n'),
    f.message.trim(),
    [`— ${f.name.trim()}`, f.email.trim(), f.phone.trim()].filter(Boolean).join('\n'),
  ].join('\n\n');

  const sendEmail = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validate()) return;
    if (brand.formEndpoint) {
      setState('sending');
      try {
        const r = await fetch(brand.formEndpoint, { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify(f) });
        if (!r.ok) throw new Error(String(r.status));
        setState('sent'); setF(empty);
      } catch { setState('failed'); }
      return;
    }
    window.location.href = `mailto:${brand.email}?subject=${encodeURIComponent(`${f.topic} — ${f.name.trim()}`)}&body=${encodeURIComponent(text())}`;
    setState('handoff');
  };
  const sendWhatsApp = () => {
    if (!validate()) return;
    window.open(`https://wa.me/${digits(brand.phone)}?text=${encodeURIComponent(`Hello ${brand.name},\n\n${text()}`)}`, '_blank', 'noopener');
    setState('handoff');
  };

  const field = (k: keyof Fields, label: string, props: React.InputHTMLAttributes<HTMLInputElement> = {}) => <label className={`field ${errors[k] ? 'invalid' : ''}`}>
    <span>{label}</span><input id={`contact-${k}`} value={f[k]} onChange={set(k)} aria-invalid={!!errors[k]} aria-describedby={errors[k] ? `contact-${k}-error` : undefined} {...props} />
    {errors[k] && <small id={`contact-${k}-error`}>{errors[k]}</small>}
  </label>;

  return <><Motion />
    <div className="page-shell"><Header solid /></div>
    <main>
      <section className="contact-hero">
        <div className="contact-intro reveal-in">
          <span className="soft-pill">Contact us</span>
          <h1>We’d love to<br />hear from you</h1>
          <p>Questions about an order, help choosing a signature scent, or gifting for someone special. Our team replies within one business day.</p>
          <ul className="contact-cards">
            <li><a href={`mailto:${brand.email}`}><Mail size={18} /><span><small>Email us</small>{brand.email}</span><ArrowUpRight size={16} /></a></li>
            <li><a href={`tel:${digits(brand.phone)}`}><Phone size={18} /><span><small>Call us</small>{brand.phone}</span><ArrowUpRight size={16} /></a></li>
            <li><a href={`https://wa.me/${digits(brand.phone)}`} target="_blank" rel="noreferrer"><MessageCircle size={18} /><span><small>WhatsApp</small>Chat with our team</span><ArrowUpRight size={16} /></a></li>
            <li><div><Headset size={18} /><span><small>Support line</small>{brand.support}</span></div></li>
            <li><div><Clock size={18} /><span><small>Opening hours</small>{brand.hours}</span></div></li>
          </ul>
          <div className="contact-social">{brand.socials.map(s => <a key={s.label} href={s.href} target="_blank" rel="noreferrer">{s.label}</a>)}</div>
        </div>

        <form className="contact-form reveal-in" onSubmit={sendEmail} noValidate>
          {state === 'sent' || state === 'handoff' ? <div className="form-done" role="status">
            <span className="done-icon"><Check size={22} /></span>
            <h2>{state === 'sent' ? 'Message sent' : 'Almost there'}</h2>
            <p>{state === 'sent' ? 'Thank you for reaching out. Our team will reply to your email within one business day.' : 'Your message is ready in your email app or WhatsApp. Press send there and our team will reply within one business day.'}</p>
            <button type="button" className="button black" onClick={() => setState('idle')}>Write another message</button>
          </div> : <>
            <h2>Send us a message</h2>
            <div className="form-row">{field('name', 'Full name *', { autoComplete: 'name', placeholder: 'Your name' })}{field('email', 'Email *', { type: 'email', autoComplete: 'email', placeholder: 'you@example.com' })}</div>
            <div className="form-row">{field('phone', 'Phone (optional)', { type: 'tel', autoComplete: 'tel', placeholder: '+92 …' })}
              <label className="field"><span>Topic</span><select id="contact-topic" value={f.topic} onChange={set('topic')}>{topics.map(t => <option key={t}>{t}</option>)}</select></label></div>
            {f.topic === 'Order enquiry' && field('order', 'Order number (optional)', { placeholder: 'e.g. AH-1024' })}
            <label className={`field ${errors.message ? 'invalid' : ''}`}><span>Message *</span><textarea id="contact-message" rows={5} value={f.message} onChange={set('message')} placeholder="How can we help?" aria-invalid={!!errors.message} aria-describedby={errors.message ? 'contact-message-error' : undefined} />{errors.message && <small id="contact-message-error">{errors.message}</small>}</label>
            {state === 'failed' && <p className="form-error" role="alert">We couldn’t send your message just now. Please try again, or email us at {brand.email}.</p>}
            <div className="form-actions">
              <button className="button black" type="submit" disabled={state === 'sending'}><Send size={15} /> {state === 'sending' ? 'Sending…' : 'Send message'}</button>
              <button className="button outline" type="button" onClick={sendWhatsApp}><MessageCircle size={15} /> Send on WhatsApp</button>
            </div>
            <p className="form-fine">We only use your details to reply to your message.</p>
          </>}
        </form>
      </section>

      <section className="contact-faq section" data-reveal>
        <div><span className="eyebrow">Quick answers</span><h2>Frequently asked<br />questions</h2><Link className="button black" href="/#shop">Explore fragrances <ArrowUpRight size={15} /></Link></div>
        <div className="faq">{info.faq.blocks[0].faq!.map(([q, a]) => <details key={q}><summary>{q}<Plus size={16} /></summary><p>{a}</p></details>)}</div>
      </section>
    </main>
    <Footer />
  </>;
}
