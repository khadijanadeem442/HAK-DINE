
gsap.to(".animated-text", {
    opacity: 1,
    y: 0,
    duration: 1,
    ease: "power2.out",
    stagger: 0.3
});

gsap.from(".contact-info, .contact-form", {
    opacity: 0,
    scale: 0.9,
    duration: 1,
    ease: "power2.out",
    delay: 0.5
});