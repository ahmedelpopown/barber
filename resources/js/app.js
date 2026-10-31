import Swiper from 'swiper';
import { Navigation, EffectFade, Autoplay } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/effect-fade';

// Enable website animations regardless of the operating system preference.
const reducedMotion = false;

document.documentElement.classList.add('motion-ready');

// ==================================================
// SWIPERS
// ==================================================

const heroSwiperElement = document.querySelector('.heroSwiper');

if (heroSwiperElement && !heroSwiperElement.swiper) {
    const heroSlides = heroSwiperElement.querySelectorAll('.swiper-slide');
    const nextButton = heroSwiperElement.querySelector('.swiper-button-next');
    const previousButton = heroSwiperElement.querySelector('.swiper-button-prev');

    try {
        new Swiper(heroSwiperElement, {
            modules: [Navigation, EffectFade, Autoplay],
            slidesPerView: 1,
            slidesPerGroup: 1,
            spaceBetween: 0,
            effect: 'fade',
            fadeEffect: {
                crossFade: true,
            },
            loop: heroSlides.length > 1,
            watchOverflow: true,
            observer: true,
            observeParents: true,
            updateOnWindowResize: true,
            autoHeight: false,
            autoplay: reducedMotion || heroSlides.length < 2 ? false : {
                delay: 6200,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            navigation: nextButton && previousButton ? {
                nextEl: nextButton,
                prevEl: previousButton,
            } : false,
            speed: 1100,
            on: {
                afterInit(swiper) {
                    // Recalculate once the browser has applied the final layout.
                    requestAnimationFrame(() => swiper.update());
                },
            },
        });
    } catch (error) {
        // Keep the first hero slide visible as a static fallback.
        console.error('Hero Swiper initialization failed:', error);
        heroSwiperElement.classList.add('swiper-init-failed');
    }
}

const testimonialSwiperElement = document.querySelector('.testimonialSwiper');

if (testimonialSwiperElement) {
    new Swiper(testimonialSwiperElement, {
        modules: [Navigation, Autoplay],
        slidesPerView: 1,
        loop: true,
        navigation: {
            nextEl: testimonialSwiperElement.querySelector('.testimonial-next'),
            prevEl: testimonialSwiperElement.querySelector('.testimonial-prev'),
        },
        autoplay: reducedMotion ? false : {
            delay: 5000,
            disableOnInteraction: false,
        },
    });
}

// ==================================================
// SHARED SETTINGS
// ==================================================

const navbar = document.getElementById('navbar');
const mobileMenu = document.getElementById('mobileMenu');
const menuBtn = document.getElementById('menuBtn');
const menuIcon = document.getElementById('menuIcon');
const pageLoader = document.getElementById('pageLoader');

// ==================================================
// PAGE LOADING REVEAL
// ==================================================

function hidePageLoader() {
    if (!pageLoader) return;

    pageLoader.classList.add('is-hidden');
    pageLoader.setAttribute('aria-hidden', 'true');

    window.setTimeout(() => {
        pageLoader.remove();
    }, reducedMotion ? 0 : 700);
}

if (pageLoader) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hidePageLoader, { once: true });
    } else {
        requestAnimationFrame(hidePageLoader);
    }

    // Back/forward cache can restore a page without firing DOMContentLoaded.
    window.addEventListener('pageshow', hidePageLoader);
}

// ==================================================
// NAVBAR SCROLL STATE
// ==================================================

function updateNavbar() {
    if (!navbar) return;
    navbar.classList.toggle('is-scrolled', window.scrollY > 80);
}

if (navbar) {
    updateNavbar();
    window.addEventListener('scroll', updateNavbar, { passive: true });
}

// ==================================================
// MOBILE MENU: SLIDE DOWN + STAGGERED LINKS
// ==================================================

let menuCloseTimer = null;
let afterMenuClose = null;

function finishMenuClose() {
    if (!mobileMenu || !menuBtn) return;
    if (menuBtn.getAttribute('aria-expanded') === 'true') return;

    window.clearTimeout(menuCloseTimer);
    mobileMenu.classList.add('hidden');
    mobileMenu.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('mobile-menu-open');
    navbar?.classList.remove('menu-open');

    const callback = afterMenuClose;
    afterMenuClose = null;
    if (typeof callback === 'function') callback();
}

function setMobileMenu(open, callback = null) {
    if (!navbar || !mobileMenu || !menuBtn) return;

    window.clearTimeout(menuCloseTimer);
    afterMenuClose = null;

    if (open) {
        mobileMenu.classList.remove('hidden');
        mobileMenu.setAttribute('aria-hidden', 'false');
        document.body.classList.add('mobile-menu-open');
        navbar.classList.add('menu-open');
        menuBtn.setAttribute('aria-expanded', 'true');
        menuBtn.setAttribute('aria-label', 'إغلاق القائمة');

        if (menuIcon) menuIcon.textContent = '×';

        // Ensure the browser applies the closed state before opening.
        void mobileMenu.offsetHeight;
        requestAnimationFrame(() => {
            if (menuBtn.getAttribute('aria-expanded') === 'true') {
                mobileMenu.classList.add('is-open');
            }
        });
        return;
    }

    menuBtn.setAttribute('aria-expanded', 'false');
    menuBtn.setAttribute('aria-label', 'فتح القائمة');
    if (menuIcon) menuIcon.textContent = '☰';

    afterMenuClose = callback;
    mobileMenu.setAttribute('aria-hidden', 'true');
    mobileMenu.classList.remove('is-open');

    if (reducedMotion) {
        finishMenuClose();
        return;
    }

    menuCloseTimer = window.setTimeout(finishMenuClose, 700);
}

if (menuBtn && mobileMenu) {
    menuBtn.addEventListener('click', () => {
        const shouldOpen = menuBtn.getAttribute('aria-expanded') !== 'true';
        setMobileMenu(shouldOpen);
    });

    mobileMenu.addEventListener('click', (event) => {
        // Clicking the white backdrop closes the menu.
        if (event.target === mobileMenu) {
            setMobileMenu(false);
            return;
        }

        const link = event.target.closest('a[href]');
        if (!link) return;

        // Preserve browser behavior for new tabs, downloads and modified clicks.
        if (
            event.defaultPrevented ||
            event.button !== 0 ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey ||
            link.target === '_blank' ||
            link.hasAttribute('download')
        ) {
            setMobileMenu(false);
            return;
        }

        let destination;
        try {
            destination = new URL(link.href, window.location.href);
        } catch {
            setMobileMenu(false);
            return;
        }

        const sameDocument =
            destination.origin === window.location.origin &&
            destination.pathname === window.location.pathname &&
            destination.search === window.location.search;

        if (sameDocument && destination.hash) {
            let targetId = destination.hash.slice(1);
            try {
                targetId = decodeURIComponent(targetId);
            } catch {
                // Keep the original hash if it is not valid encoded text.
            }

            const target = document.getElementById(targetId);
            if (target) {
                event.preventDefault();
                setMobileMenu(false, () => {
                    window.history.pushState(null, '', destination.hash);
                    scrollToSection(target);
                });
                return;
            }
        }

        // For a different route, finish the closing animation before navigation.
        event.preventDefault();
        setMobileMenu(false, () => window.location.assign(destination.href));
    });
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && menuBtn?.getAttribute('aria-expanded') === 'true') {
        setMobileMenu(false);
        menuBtn.focus();
    }
});

window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024 && menuBtn?.getAttribute('aria-expanded') === 'true') {
        setMobileMenu(false);
    }
});

// ==================================================
// SMOOTH SECTION NAVIGATION
// ==================================================

function scrollToSection(target) {
    if (!target) return;

    const navbarHeight = navbar?.getBoundingClientRect().height ?? 0;
    const targetTop = target.getBoundingClientRect().top + window.scrollY;
    const top = Math.max(0, targetTop - navbarHeight - 20);

    window.scrollTo({
        top,
        behavior: reducedMotion ? 'auto' : 'smooth',
    });
}

// Smoothly scroll desktop navigation hash links too.
document.querySelectorAll('#list a[href*="#"]').forEach((link) => {
    link.addEventListener('click', (event) => {
        const destination = new URL(link.href, window.location.href);
        const sameDocument =
            destination.origin === window.location.origin &&
            destination.pathname === window.location.pathname &&
            destination.search === window.location.search;

        if (!sameDocument || !destination.hash) return;

        let targetId = destination.hash.slice(1);
        try {
            targetId = decodeURIComponent(targetId);
        } catch {
            // Use the hash as-is.
        }

        const target = document.getElementById(targetId);
        if (!target) return;

        event.preventDefault();
        window.history.pushState(null, '', destination.hash);
        scrollToSection(target);
    });
});

// Keep the existing appointment/about observation behavior.
const about = document.getElementById('about');
const appointment = document.getElementById('appointment');

if ('IntersectionObserver' in window) {
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.target.id === 'appointment') {
                entry.target.classList.toggle('active', entry.isIntersecting);
            }
        });
    }, { threshold: 0.3 });

    [appointment, about].filter(Boolean).forEach((element) => sectionObserver.observe(element));
}

// ==================================================
// SCROLL REVEAL: reveal each marked section/card once
// Add data-reveal to elements and --reveal-delay for staggered cards.
// ==================================================

const revealElements = document.querySelectorAll('[data-reveal]');

if (reducedMotion || !('IntersectionObserver' in window)) {
    revealElements.forEach((element) => element.classList.add('is-revealed'));
} else if (revealElements.length) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -6% 0px',
    });

    revealElements.forEach((element) => revealObserver.observe(element));
}

// ==================================================
// PARALLAX: SCROLL + IMAGE PARALLAX
// Add data-parallax to any image/element, or use .appointment-image.
// Optional: data-parallax-speed="0.12"
// ==================================================

if (!reducedMotion) {
    const parallaxElements = Array.from(
        document.querySelectorAll('[data-parallax], .appointment-image')
    );

    if (parallaxElements.length) {
        const visibleParallaxElements = new Set();
        let parallaxFrame = null;

        const updateParallax = () => {
            parallaxFrame = null;
            const viewportCenter = window.innerHeight / 2;

            visibleParallaxElements.forEach((element) => {
                if (!element.isConnected) {
                    visibleParallaxElements.delete(element);
                    return;
                }

                const rect = element.getBoundingClientRect();
                const speedValue = Number.parseFloat(element.dataset.parallaxSpeed ?? '0.10');
                const speed = Number.isFinite(speedValue) ? speedValue : 0.10;
                const distance = (rect.top + rect.height / 2 - viewportCenter) * speed;
                const offset = Math.max(-65, Math.min(65, distance * -1));

                // CSS translate is used instead of transform so existing image zoom animations remain intact.
                element.style.setProperty('--parallax-y', `${offset.toFixed(2)}px`);
            });
        };

        const requestParallaxUpdate = () => {
            if (parallaxFrame !== null) return;
            parallaxFrame = window.requestAnimationFrame(updateParallax);
        };

        if ('IntersectionObserver' in window) {
            const parallaxObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        visibleParallaxElements.add(entry.target);
                    } else {
                        visibleParallaxElements.delete(entry.target);
                        entry.target.style.setProperty('--parallax-y', '0px');
                    }
                });
                requestParallaxUpdate();
            }, { rootMargin: '100px 0px' });

            parallaxElements.forEach((element) => parallaxObserver.observe(element));
        } else {
            parallaxElements.forEach((element) => visibleParallaxElements.add(element));
        }

        window.addEventListener('scroll', requestParallaxUpdate, { passive: true });
        window.addEventListener('resize', requestParallaxUpdate, { passive: true });
        requestParallaxUpdate();
    }
}

// ==================================================
// NUMBER COUNTERS
// HTML: <span data-counter="1250" data-counter-suffix="+">0</span>
// ==================================================

const counterElements = document.querySelectorAll('[data-counter]');

function animateCounter(element) {
    if (element.dataset.counterAnimated === 'true') return;
    element.dataset.counterAnimated = 'true';

    const target = Number.parseFloat(element.dataset.counter);
    if (!Number.isFinite(target)) return;

    const duration = Math.max(300, Number.parseInt(element.dataset.counterDuration ?? '1600', 10) || 1600);
    const decimals = Math.max(0, Math.min(4, Number.parseInt(element.dataset.counterDecimals ?? '0', 10) || 0));
    const prefix = element.dataset.counterPrefix ?? '';
    const suffix = element.dataset.counterSuffix ?? '';
    const formatter = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });

    if (reducedMotion) {
        element.textContent = `${prefix}${formatter.format(target)}${suffix}`;
        return;
    }

    const startTime = performance.now();
    const from = 0;

    const tick = (now) => {
        const progress = Math.min((now - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 4);
        const current = from + (target - from) * eased;

        element.textContent = `${prefix}${formatter.format(progress === 1 ? target : current)}${suffix}`;

        if (progress < 1) {
            requestAnimationFrame(tick);
        }
    };

    requestAnimationFrame(tick);
}

if (counterElements.length) {
    if ('IntersectionObserver' in window) {
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.45 });

        counterElements.forEach((element) => counterObserver.observe(element));
    } else {
        counterElements.forEach(animateCounter);
    }
}

// ==================================================
// BEFORE / AFTER IMAGE SLIDER
// Use the <x-web.before-after> Blade component.
// ==================================================

document.querySelectorAll('[data-before-after]').forEach((comparison) => {
    const range = comparison.querySelector('[data-before-after-range]');
    if (!range) return;

    const updateComparison = () => {
        const value = Math.min(100, Math.max(0, Number(range.value) || 50));
        comparison.style.setProperty('--before-after-position', `${value}%`);
        range.setAttribute('aria-valuetext', `${Math.round(value)}%`);
    };

    range.addEventListener('input', updateComparison);
    updateComparison();
});

// ==================================================
// BACK TO TOP BUTTON
// ==================================================

let backToTop = document.getElementById('backToTop');

if (!backToTop) {
    backToTop = document.createElement('button');
    backToTop.id = 'backToTop';
    backToTop.type = 'button';
    backToTop.className = 'back-to-top';
    backToTop.setAttribute('aria-label', 'العودة إلى أعلى الصفحة');
    backToTop.innerHTML = '<span aria-hidden="true">↑</span>';
    document.body.appendChild(backToTop);
}

function updateBackToTop() {
    backToTop.classList.toggle('is-visible', window.scrollY > 450);
}

updateBackToTop();
window.addEventListener('scroll', updateBackToTop, { passive: true });

backToTop.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: reducedMotion ? 'auto' : 'smooth',
    });
});

// ==================================================
// DESKTOP CURSOR GLOW
// Disabled automatically on touch devices and reduced-motion settings.
// ==================================================

const supportsFinePointer = window.matchMedia('(hover: hover) and (pointer: fine) and (min-width: 1024px)').matches;

if (supportsFinePointer && !reducedMotion) {
    const cursorGlow = document.createElement('div');
    cursorGlow.className = 'cursor-glow';
    cursorGlow.setAttribute('aria-hidden', 'true');
    document.body.appendChild(cursorGlow);

    let cursorFrame = null;
    let pointerX = -500;
    let pointerY = -500;

    document.addEventListener('pointermove', (event) => {
        pointerX = event.clientX;
        pointerY = event.clientY;

        if (cursorFrame !== null) return;

        cursorFrame = window.requestAnimationFrame(() => {
            cursorGlow.style.transform = `translate3d(${pointerX - 210}px, ${pointerY - 210}px, 0)`;
            cursorGlow.classList.add('is-active');
            cursorFrame = null;
        });
    }, { passive: true });

    document.addEventListener('pointerleave', () => {
        cursorGlow.classList.remove('is-active');
    });
}
