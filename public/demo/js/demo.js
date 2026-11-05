(() => {
    'use strict';

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const topbar = document.querySelector('.topbar');
    const progressBar = document.getElementById('readingProgress');
    const navToggle = document.getElementById('navToggle');
    const mainNav = document.getElementById('mainNav');

    // Reveal sections as they enter the viewport.
    const revealElements = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window && !reduceMotion) {
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('visible');
                currentObserver.unobserve(entry.target);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -30px 0px' });
        revealElements.forEach((element) => observer.observe(element));
    } else {
        revealElements.forEach((element) => element.classList.add('visible'));
    }

    // Keep the visual placeholder when an image is missing.
    document.querySelectorAll('.media-frame').forEach((frame) => {
        const image = frame.querySelector('.asset-img');
        if (!image) return;
        const loaded = () => frame.classList.add('loaded');
        const failed = () => frame.classList.remove('loaded');
        if (image.complete && image.naturalWidth > 0) loaded();
        image.addEventListener('load', loaded, { once: true });
        image.addEventListener('error', failed);
    });

    // Mobile navigation.
    const closeMenu = () => {
        if (!navToggle || !mainNav) return;
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.setAttribute('aria-label', 'فتح القائمة');
        mainNav.classList.remove('open');
        document.body.classList.remove('locked');
    };
    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            const open = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', String(!open));
            navToggle.setAttribute('aria-label', open ? 'فتح القائمة' : 'إغلاق القائمة');
            mainNav.classList.toggle('open', !open);
            document.body.classList.toggle('locked', !open);
        });
        mainNav.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
        window.addEventListener('resize', () => { if (window.innerWidth > 760) closeMenu(); });
    }

    // Scroll progress and sticky header state.
    let scrollPending = false;
    const updateScroll = () => {
        const total = document.documentElement.scrollHeight - window.innerHeight;
        const percent = total > 0 ? (window.scrollY / total) * 100 : 0;
        if (progressBar) progressBar.style.width = `${Math.min(100, Math.max(0, percent))}%`;
        if (topbar) topbar.classList.toggle('scrolled', window.scrollY > 12);
        scrollPending = false;
    };
    window.addEventListener('scroll', () => {
        if (scrollPending) return;
        window.requestAnimationFrame(updateScroll);
        scrollPending = true;
    }, { passive: true });
    updateScroll();

    // Gentle parallax on pointer devices only.
    const parallaxElements = document.querySelectorAll('[data-parallax]');
    if (!reduceMotion && window.matchMedia('(pointer: fine)').matches && parallaxElements.length) {
        let parallaxPending = false;
        const updateParallax = () => {
            const center = window.innerHeight / 2;
            parallaxElements.forEach((element) => {
                const rect = element.getBoundingClientRect();
                const speed = Number(element.dataset.parallax) || 0;
                const offset = (rect.top + rect.height / 2 - center) * speed;
                element.style.translate = `0 ${Math.max(-30, Math.min(30, offset))}px`;
            });
            parallaxPending = false;
        };
        window.addEventListener('scroll', () => {
            if (parallaxPending) return;
            window.requestAnimationFrame(updateParallax);
            parallaxPending = true;
        }, { passive: true });
        updateParallax();
    }

    // Subtle desktop tilt for feature previews.
    if (!reduceMotion && window.matchMedia('(pointer: fine)').matches) {
        document.querySelectorAll('.tilt-card').forEach((card) => {
            card.addEventListener('pointermove', (event) => {
                const rect = card.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) - 0.5;
                const y = ((event.clientY - rect.top) / rect.height) - 0.5;
                const isHero = card.classList.contains('preview-window');
                const base = isHero ? 'rotateY(-5deg) rotateX(2deg)' : 'rotate(-1deg)';
                card.style.transform = `${base} rotateY(${x * 2.5}deg) rotateX(${-y * 2.5}deg)`;
            });
            card.addEventListener('pointerleave', () => {
                card.style.transform = card.classList.contains('preview-window') ? 'rotateY(-5deg) rotateX(2deg)' : '';
            });
        });
    }

    // Hide video fallback only when the video can actually play.
    const video = document.getElementById('productVideo');
    const videoEmpty = document.getElementById('videoEmpty');
    if (video && videoEmpty) {
        const videoReady = () => videoEmpty.classList.add('hidden');
        video.addEventListener('loadeddata', videoReady, { once: true });
        video.addEventListener('canplay', videoReady, { once: true });
        video.addEventListener('error', () => videoEmpty.classList.remove('hidden'));
        if (video.readyState >= 2) videoReady();
    }

    // Gallery category filters.
    const filters = document.querySelectorAll('.gallery-filter');
    const galleryItems = Array.from(document.querySelectorAll('.gallery-item'));
    filters.forEach((filterButton) => {
        filterButton.addEventListener('click', () => {
            const filter = filterButton.dataset.filter;
            filters.forEach((button) => {
                const active = button === filterButton;
                button.classList.toggle('active', active);
                button.setAttribute('aria-pressed', String(active));
            });
            galleryItems.forEach((item) => {
                item.classList.toggle('is-hidden', filter !== 'all' && item.dataset.category !== filter);
            });
        });
    });

    // Accessible gallery lightbox.
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const lightboxCaption = document.getElementById('lightboxCaption');
    const closeButton = document.getElementById('lightboxClose');
    const previousButton = document.getElementById('lightboxPrev');
    const nextButton = document.getElementById('lightboxNext');
    let activeItems = [];
    let activeIndex = 0;
    let previousFocus = null;

    const renderImage = () => {
        const item = activeItems[activeIndex];
        if (!item || !lightboxImage) return;
        lightboxImage.src = item.dataset.full || '';
        lightboxImage.alt = item.querySelector('img')?.alt || '';
        if (lightboxCaption) lightboxCaption.textContent = item.dataset.caption || '';
    };
    const openLightbox = (item) => {
        if (!lightbox) return;
        activeItems = galleryItems.filter((galleryItem) => !galleryItem.classList.contains('is-hidden'));
        activeIndex = activeItems.indexOf(item);
        previousFocus = document.activeElement;
        renderImage();
        lightbox.classList.add('open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.classList.add('locked');
        closeButton?.focus();
    };
    const closeLightbox = () => {
        if (!lightbox) return;
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('locked');
        if (lightboxImage) lightboxImage.src = '';
        previousFocus?.focus?.();
    };
    const moveImage = (direction) => {
        if (!activeItems.length) return;
        activeIndex = (activeIndex + direction + activeItems.length) % activeItems.length;
        renderImage();
    };

    galleryItems.forEach((item) => item.addEventListener('click', () => openLightbox(item)));
    closeButton?.addEventListener('click', closeLightbox);
    previousButton?.addEventListener('click', () => moveImage(-1));
    nextButton?.addEventListener('click', () => moveImage(1));
    lightbox?.addEventListener('click', (event) => { if (event.target === lightbox) closeLightbox(); });
    document.addEventListener('keydown', (event) => {
        if (!lightbox?.classList.contains('open')) return;
        if (event.key === 'Escape') closeLightbox();
        if (event.key === 'ArrowLeft') moveImage(1);
        if (event.key === 'ArrowRight') moveImage(-1);
    });
})();
