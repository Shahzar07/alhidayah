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
 const ctx=gsap.context(()=>{
 gsap.from('.hero-copy > *',{y:26,opacity:0,duration:1,stagger:.13,ease:'power3.out',delay:.1});
 gsap.utils.toArray<HTMLElement>('[data-reveal]').forEach(el=>gsap.from(el,{y:32,opacity:0,duration:.8,ease:'power2.out',scrollTrigger:{trigger:el,start:'top 93%',once:true}}));
 gsap.to('.hero-photo',{yPercent:8,ease:'none',scrollTrigger:{trigger:'.hero',start:'top top',end:'bottom top',scrub:true}});
 gsap.utils.toArray<HTMLElement>('.botanical').forEach((el,i)=>gsap.to(el,{y:i%2?35:-35,rotation:i%2?4:-4,ease:'none',scrollTrigger:{trigger:'.ingredients',start:'top bottom',end:'bottom top',scrub:1}}));
 });
 return ()=>{ctx.revert(); gsap.ticker.remove(tick);lenis.destroy();};
},[]); return null;
}
