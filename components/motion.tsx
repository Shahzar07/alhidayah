'use client';
import { useEffect } from 'react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
export default function Motion() {
useEffect(()=>{
 if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
 gsap.registerPlugin(ScrollTrigger);
 const lenis = new Lenis({duration:1.05,smoothWheel:true,anchors:true, prevent: (node) => node.hasAttribute('data-lenis-prevent')});
 lenis.on('scroll',ScrollTrigger.update);
 const tick=(time:number)=>lenis.raf(time*1000); gsap.ticker.add(tick);
 const has=(s:string)=>!!document.querySelector(s);
 const ctx=gsap.context(()=>{
 if(has('.hero-copy')) gsap.from('.hero-copy > *',{y:26,opacity:0,duration:1,stagger:.13,ease:'power3.out',delay:.1});
 if(has('.product-hero')) gsap.from('.product-hero .reveal-in',{y:24,opacity:0,duration:.9,stagger:.1,ease:'power3.out'});
 gsap.utils.toArray<HTMLElement>('[data-reveal]').forEach(el=>gsap.from(el,{y:32,opacity:0,duration:.8,ease:'power2.out',scrollTrigger:{trigger:el,start:'top 93%',once:true}}));
 if(has('.hero')) gsap.to('.hero-photo',{yPercent:8,ease:'none',scrollTrigger:{trigger:'.hero',start:'top top',end:'bottom top',scrub:true}});
 if(has('.ingredients')) gsap.utils.toArray<HTMLElement>('.botanical').forEach((el,i)=>gsap.to(el,{y:i%2?35:-35,rotation:i%2?4:-4,ease:'none',scrollTrigger:{trigger:'.ingredients',start:'top bottom',end:'bottom top',scrub:1}}));
 if(has('.footer-giant')) gsap.from('.footer-giant text',{y:60,ease:'none',scrollTrigger:{trigger:'.footer-giant',start:'top bottom',end:'bottom bottom',scrub:true}});
 });
 // late-loading images move sections; re-measure triggers so reveals fire at the right place
 let t=0; const refresh=()=>{clearTimeout(t);t=window.setTimeout(()=>ScrollTrigger.refresh(),150);};
 const imgs=[...document.images].filter(i=>!i.complete); imgs.forEach(i=>i.addEventListener('load',refresh,{once:true}));
 window.addEventListener('load',refresh);
 return ()=>{clearTimeout(t);window.removeEventListener('load',refresh);imgs.forEach(i=>i.removeEventListener('load',refresh));ctx.revert(); gsap.ticker.remove(tick);lenis.destroy();};
},[]); return null;
}
